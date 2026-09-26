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
        $installmentPlans = InstallmentPlan::with(['sale.saleItems.product', 'sale.saleItems.cashews.product', 'installmentPayments'])
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
        $installmentPlan->load(['sale.saleItems.product', 'sale.saleItems.cashews.product', 'installmentPayments']);

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

            $installmentPlan = InstallmentPlan::query()
                ->with('sale')
                ->lockForUpdate()
                ->findOrFail($installmentPlan->id);

            $totalPaid = (float) InstallmentPayment::where('installment_plan_id', $installmentPlan->id)->sum('amount_paid');
            $remainingAmount = round((float) $installmentPlan->sale->final_amount - $totalPaid, 2);

            $amountToPay = round((float) $request->amount_paid, 2);

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
            $newTotalPaid = round($totalPaid + $amountToPay, 2);

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

            $installmentPlan->sale->amount_paid = $newTotalPaid;
            $installmentPlan->sale->amount_due = round((float) $installmentPlan->sale->final_amount - $newTotalPaid, 2);
            $installmentPlan->sale->save();

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
            $plan = InstallmentPlan::query()
                ->with('sale')
                ->lockForUpdate()
                ->findOrFail($request->installment_plan_id);
            $totalPaid = (float) InstallmentPayment::where('installment_plan_id', $plan->id)->sum('amount_paid');
            $amountToPay = round((float) $request->amount_paid, 2);
            $remainingAmount = round((float) $plan->sale->final_amount - $totalPaid, 2);

            if ($plan->status !== 'active' || $amountToPay > $remainingAmount) {
                throw ValidationException::withMessages([
                    'amount_paid' => 'Payment cannot exceed the outstanding installment balance.',
                ]);
            }

            InstallmentPayment::create([
                'installment_plan_id' => $plan->id,
                'payment_date' => $request->payment_date,
                'amount_paid' => $amountToPay,
            ]);

            $newTotalPaid = round($totalPaid + $amountToPay, 2);
            $plan->status = $newTotalPaid >= (float) $plan->sale->final_amount ? 'completed' : 'active';
            $plan->next_payment_date = $plan->status === 'completed' ? null : now()->addMonth();
            $plan->save();

            $plan->sale->amount_paid = $newTotalPaid;
            $plan->sale->amount_due = round((float) $plan->sale->final_amount - $newTotalPaid, 2);
            $plan->sale->save();
        });

        return response()->json(['message' => 'Payment recorded successfully!']);
    }
}
