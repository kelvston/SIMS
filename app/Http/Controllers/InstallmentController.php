<?php


namespace App\Http\Controllers;

use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling

class InstallmentController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    public function __construct()
    {
        // Protect installment related actions
        $this->middleware(['auth', 'permission:view installments'])->only('index');
        $this->middleware(['auth', 'permission:record installment payments'])->only(['showPaymentForm', 'recordPayment']);
    }



    public function index()
    {
        $installmentPlans = InstallmentPlan::with(['sale.saleItems.phone', 'installmentPayments'])
            ->orderBy('next_payment_date', 'asc')
            ->paginate(5);
        return view('installments.index', compact('installmentPlans'));
    }

    /**
     * Show the form for recording a new payment for an installment plan.
     *
     * @param  \App\Models\InstallmentPlan  $installmentPlan
     * @return \Illuminate\View\View
     */
    public function showPaymentForm(InstallmentPlan $installmentPlan)
    {
        // Load related sale and phone data for display
        $installmentPlan->load(['sale.saleItems.phone', 'installmentPayments']);

        // Calculate total paid and remaining amount
        $totalPaid = $installmentPlan->installmentPayments->sum('amount_paid');
        $remainingAmount = $installmentPlan->sale->final_amount - $totalPaid;

        return view('installments.pay', compact('installmentPlan', 'totalPaid', 'remainingAmount'));
    }

    /**
     * Store a newly recorded payment for an installment plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InstallmentPlan  $installmentPlan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recordPayment(Request $request, InstallmentPlan $installmentPlan)
    {
        // Validate the request data
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date', // Allow user to specify payment date, default to now
        ]);

        try {
            DB::beginTransaction();

            // Calculate current total paid and remaining balance
            $totalPaid = $installmentPlan->installmentPayments->sum('amount_paid');
            $remainingAmount = $installmentPlan->sale->final_amount - $totalPaid;

            $amountToPay = $request->amount_paid;

            // Prevent overpayment beyond the remaining amount
            if ($amountToPay > $remainingAmount + 0.01) { // Add a small tolerance for floating point issues
                throw ValidationException::withMessages([
                    'amount_paid' => ['The payment amount cannot exceed the remaining balance of $' . number_format($remainingAmount, 2) . '.'],
                ]);
            }

            // Create the InstallmentPayment record
            InstallmentPayment::create([
                'installment_plan_id' => $installmentPlan->id,
                'amount_paid' => $amountToPay,
                'payment_date' => $request->payment_date ?? now(),
            ]);

            // Recalculate total paid after the new payment
            $newTotalPaid = $totalPaid + $amountToPay;

            // Update installment plan status and next payment date
            if ($newTotalPaid >= $installmentPlan->sale->final_amount) {
                $installmentPlan->status = 'completed';
                $installmentPlan->next_payment_date = null; // No more payments expected
            } else {
                // For simplicity, let's assume next payment is due one month from current payment or start date
                // A more complex system might have fixed payment schedules
                $installmentPlan->next_payment_date = now()->addMonth();
            }
            $installmentPlan->save();

            DB::commit();

            return redirect()->route('sales.show', $installmentPlan->sale->id)->with('success', 'Payment recorded successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to record payment. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'installment_plan_id' => 'required|exists:installment_plans,id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            // Create payment
            InstallmentPayment::create([
                'installment_plan_id' => $request->installment_plan_id,
                'payment_date' => $request->payment_date,
                'amount_paid' => $request->amount_paid,
            ]);

            // Update next payment date (simple monthly increment)
            $plan = InstallmentPlan::find($request->installment_plan_id);
            $plan->next_payment_date = now()->addMonth();
            $plan->save();
        });

        return response()->json(['message' => 'Payment recorded successfully!']);
    }
}
