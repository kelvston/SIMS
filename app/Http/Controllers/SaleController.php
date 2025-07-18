<?php
namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InstallmentPlan;
use App\Models\SaleReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\SaleReceiptMail;
use Illuminate\Support\Facades\Mail;

class SaleController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    public function __construct()
    {
        // Protect sales related actions
        $this->middleware(['auth', 'permission:view sales'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create sales'])->only(['create', 'store']);
        // Add middleware for 'edit sales' and 'delete sales' if you implement those methods
    }


    public function index()
    {
        // Eager load saleItems and their associated phones, and installmentPlan if it exists
        $sales = Sale::with(['saleItems.phone', 'installmentPlan'])
            ->orderBy('sale_date', 'desc')
            ->paginate(5);


        \Artisan::call('stock:check-low'); // Run the command

        $lowStockAlerts = Cache::pull('low_stock_alerts', []);

        return view('sales.index', compact('sales','lowStockAlerts'));



    }

    /**
     * Show the form for creating a new sale.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch only available phones for selection in the sales form
        $availablePhones = Phone::where('status', 'available')->orderBy('model')->get();
        return view('sales.create', compact('availablePhones'));
    }

    /**
     * Store a newly created sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'phone_imeis' => 'required|array|min:1',
            'phone_imeis.*' => 'required|string|distinct|exists:phones,imei',
            'discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required_if:is_installment,true|date',
        ]);

        if ($request['is_installment'] == 'true') {
            $request->validate([
                'is_installment' => 'boolean',
                'total_installments' => 'required_if:is_installment,true|integer|min:1',
                'installment_amount' => 'required_if:is_installment,true|numeric|min:0.01',
            ]);
        }

        try {
            DB::beginTransaction();

            $phoneIds = [];
            $totalAmount = 0;

            // Fetch phones and calculate total amount
            foreach ($request->phone_imeis as $imei) {
                $phone = Phone::where('imei', $imei)->where('status', 'available')->first();

                if (!$phone) {
                    throw ValidationException::withMessages([
                        'phone_imeis' => ['Phone with IMEI ' . $imei . ' is not available for sale.'],
                    ]);
                }

                $phoneIds[] = $phone->id;
                $totalAmount += $phone->selling_price;
            }

            $discountAmount = $request->input('discount_amount', 0);
            $finalAmount = $totalAmount - $discountAmount;

            if ($finalAmount < 0) {
                throw ValidationException::withMessages([
                    'discount_amount' => ['Discount cannot exceed the total amount.'],
                ]);
            }

            // Create the Sale record
            $sale = Sale::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'sale_date' => now(),
                'is_installment' => $request->boolean('is_installment'),
            ]);

            // Create SaleItem records, update phone status, and reduce stock level
            foreach ($phoneIds as $phoneId) {
                $phone = Phone::find($phoneId);

                $phone->status = $request->boolean('is_installment') ? 'under_installment' : 'sold';
                $phone->save();

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'phone_id' => $phone->id,
                    'unit_price' => $phone->selling_price,
                ]);

                // Decrease stock in StockLevel
                $stockLevel = \App\Models\StockLevel::where('brand_id', $phone->brand_id)
                    ->where('model', $phone->model)
                    ->where('color', $phone->color)
                    ->first();

                if ($stockLevel && $stockLevel->current_stock > 0) {
                    $stockLevel->decrement('current_stock');
                    $stockLevel->last_updated_at = now();
                    $stockLevel->save();
                }
            }

            // Generate unique receipt number
            $receiptNumber = 'RCPT-' . strtoupper(uniqid());

            // Determine paid amount
            $paidAmount = $request->boolean('is_installment')
                ? $request->installment_amount
                : $finalAmount;

            // Create the receipt
            $receipt = SaleReceipt::create([
                'receipt_number' => $receiptNumber,
                'sale_id' => $sale->id,
                'issued_at' => now(),
                'subtotal' => $totalAmount,
                'tax' => 0,
                'discount' => $discountAmount,
                'total' => $finalAmount,
                'is_installment' => $request->boolean('is_installment'),
                'paid_amount' => $paidAmount,
                'payment_method' => 'cash',
                'status' => $paidAmount < $finalAmount ? 'partial' : 'paid',
                'notes' => null,
            ]);

            // Load sale with related items and nested phone/brand relationships for PDF
            $sale->load('saleItems.phone.brand', 'installmentPlan');


            // Load receipt with sale relation if needed
            $receipt->load('sale');

            // Generate PDF




            $pdf = Pdf::loadView('pdf.receipt', ['sale' => $sale, 'receipt' => $receipt]);
            $pdfContent = base64_encode($pdf->output());

            Mail::to($sale->customer_email)->send(new SaleReceiptMail($sale, $receipt, $pdfContent));


            // If it's an installment sale, create InstallmentPlan
            if ($request->boolean('is_installment')) {
                if ($request->installment_amount * $request->total_installments < $finalAmount) {
                    throw ValidationException::withMessages([
                        'installment_amount' => ['Total installment amount is less than the final sale amount.'],
                    ]);
                }

                InstallmentPlan::create([
                    'sale_id' => $sale->id,
                    'total_installments' => $request->total_installments,
                    'installment_amount' => $request->installment_amount,
                    'start_date' => $request->start_date,
                    'next_payment_date' => $request->start_date,
                    'status' => 'active',
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording sale: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to record sale. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Display the specified sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function show(Sale $sale)
    {
        // Eager load related data for the sale details page
        $sale->load(['saleItems.phone', 'installmentPlan.installmentPayments']);
        return view('sales.show', compact('sale'));
    }
}
