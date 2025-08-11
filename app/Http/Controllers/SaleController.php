<?php
namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Phone;
use App\Models\Setting;
use App\Models\ReturnLog;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InstallmentPlan;
use App\Models\SaleReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
    public function find($code)
    {
        $phone = \App\Models\Phone::where('imei', $code)->first();
        if ($phone) {
            return response()->json([
                'success' => true,
                'type' => 'phone',
                'item' => [
                    'id' => $phone->id,
                    'name' => $phone->name,
                    'imei' => $phone->imei,
                ]
            ]);
        }

        $accessory = \App\Models\Accessory::where('barcode', $code)
            ->orWhere('id', $code)->first();
        if ($accessory) {
            return response()->json([
                'success' => true,
                'type' => 'accessory',
                'item' => [
                    'id' => $accessory->id,
                    'name' => $accessory->name,
                    'barcode' => $accessory->barcode,
                ]
            ]);
        }

        return response()->json(['success' => false]);
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
        $availablePhones = Phone::where('status', 'available')
            ->select('phones.*')
            ->leftJoin('sale_items', 'phones.id', '=', 'sale_items.phone_id')
            ->whereNull('sale_items.phone_id')
            ->orderBy('model')
            ->get();



        $availableAccessories = Accessory::where('status', 'in_stock')->where('quantity','>=',1)->orderBy('created_at')->get();
        return view('sales.create', compact('availableAccessories','availablePhones'));
    }
    public function printReceipt()
    {
        $receipts = SaleReceipt::with('sale')->latest()->get();
        return view('sales.print', compact('receipts'));
    }
    public function printSingleReceipt(SaleReceipt $receipt)
    {
        $receipt->load(['sale.saleItems.phone', 'sale.saleItems.accessory']);
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('sales.print_receipt', compact('receipt', 'settings'));
    }


    /**
     * Store a newly created sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

//    public function store(Request $request)
//    {
//        // Validate the request data
//        $request->validate([
//            'customer_name' => 'required|string|max:255',
//            'customer_phone' => 'nullable|string|max:255',
//            'customer_email' => 'nullable|email|max:255',
//            'phone_imeis' => 'required|array|min:1',
//            'phone_imeis.*' => 'required|string|distinct|exists:phones,imei',
//            'discount_amount' => 'nullable|numeric|min:0',
//            'start_date' => 'required_if:is_installment,true|date',
//        ]);
//
//        if ($request['is_installment'] == 'true') {
//            $request->validate([
//                'is_installment' => 'boolean',
//                'total_installments' => 'required_if:is_installment,true|integer|min:1',
//                'installment_amount' => 'required_if:is_installment,true|numeric|min:0.01',
//            ]);
//        }
//
//        try {
//            DB::beginTransaction();
//
//            $phoneIds = [];
//            $totalAmount = 0;
//
//            // Fetch phones and calculate total amount
//            foreach ($request->phone_imeis as $imei) {
//                $phone = Phone::where('imei', $imei)->where('status', 'available')->first();
//
//                if (!$phone) {
//                    throw ValidationException::withMessages([
//                        'phone_imeis' => ['Phone with IMEI ' . $imei . ' is not available for sale.'],
//                    ]);
//                }
//
//                $phoneIds[] = $phone->id;
//                $totalAmount += $phone->selling_price;
//            }
//
//            $discountAmount = $request->input('discount_amount', 0);
//            $finalAmount = $totalAmount - $discountAmount;
//
//            if ($finalAmount < 0) {
//                throw ValidationException::withMessages([
//                    'discount_amount' => ['Discount cannot exceed the total amount.'],
//                ]);
//            }
//
//            // Create the Sale record
//            $sale = Sale::create([
//                'customer_name' => $request->customer_name,
//                'customer_phone' => $request->customer_phone,
//                'customer_email' => $request->customer_email,
//                'total_amount' => $totalAmount,
//                'discount_amount' => $discountAmount,
//                'final_amount' => $finalAmount,
//                'sale_date' => now(),
//                'is_installment' => $request->boolean('is_installment'),
//            ]);
//
//            // Create SaleItem records, update phone status, and reduce stock level
//            foreach ($phoneIds as $phoneId) {
//                $phone = Phone::find($phoneId);
//
//                $phone->status = $request->boolean('is_installment') ? 'under_installment' : 'sold';
//                $phone->save();
//
//                SaleItem::create([
//                    'sale_id' => $sale->id,
//                    'phone_id' => $phone->id,
//                    'unit_price' => $phone->selling_price,
//                ]);
//
//                // Decrease stock in StockLevel
//                $stockLevel = \App\Models\StockLevel::where('brand_id', $phone->brand_id)
//                    ->where('model', $phone->model)
//                    ->where('color', $phone->color)
//                    ->first();
//
//                if ($stockLevel && $stockLevel->current_stock > 0) {
//                    $stockLevel->decrement('current_stock');
//                    $stockLevel->last_updated_at = now();
//                    $stockLevel->save();
//                }
//            }
//
//            // Generate unique receipt number
//            $receiptNumber = 'RCPT-' . strtoupper(uniqid());
//
//            // Determine paid amount
//            $paidAmount = $request->boolean('is_installment')
//                ? $request->installment_amount
//                : $finalAmount;
//
//            // Create the receipt
//            $receipt = SaleReceipt::create([
//                'receipt_number' => $receiptNumber,
//                'sale_id' => $sale->id,
//                'issued_at' => now(),
//                'subtotal' => $totalAmount,
//                'tax' => 0,
//                'discount' => $discountAmount,
//                'total' => $finalAmount,
//                'is_installment' => $request->boolean('is_installment'),
//                'paid_amount' => $paidAmount,
//                'payment_method' => 'cash',
//                'status' => $paidAmount < $finalAmount ? 'partial' : 'paid',
//                'notes' => null,
//            ]);
//
//            // Load sale with related items and nested phone/brand relationships for PDF
//            $sale->load('saleItems.phone.brand', 'installmentPlan');
//
//
//            // Load receipt with sale relation if needed
//            $receipt->load('sale');
//
//            // Generate PDF
//
//
//
//
//            $pdf = Pdf::loadView('pdf.receipt', ['sale' => $sale, 'receipt' => $receipt]);
//            $pdfContent = base64_encode($pdf->output());
//
//            Mail::to($sale->customer_email)->send(new SaleReceiptMail($sale, $receipt, $pdfContent));
//
//
//            // If it's an installment sale, create InstallmentPlan
//            if ($request->boolean('is_installment')) {
//                if ($request->installment_amount * $request->total_installments < $finalAmount) {
//                    throw ValidationException::withMessages([
//                        'installment_amount' => ['Total installment amount is less than the final sale amount.'],
//                    ]);
//                }
//
//                InstallmentPlan::create([
//                    'sale_id' => $sale->id,
//                    'total_installments' => $request->total_installments,
//                    'installment_amount' => $request->installment_amount,
//                    'start_date' => $request->start_date,
//                    'next_payment_date' => $request->start_date,
//                    'status' => 'active',
//                ]);
//            }
//
//            DB::commit();
//            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');
//        } catch (ValidationException $e) {
//            DB::rollBack();
//            return redirect()->back()->withErrors($e->errors())->withInput();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            \Log::error('Error recording sale: ' . $e->getMessage());
//            return redirect()->back()->with('error', 'Failed to record sale. Please try again. Error: ' . $e->getMessage())->withInput();
//        }
//    }


    public function store(Request $request)
    {

        // 1. Basic validation for customer, discount, and the new credit_sale flag
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'payment_option' => 'nullable|numeric|min:0',
            // amount_paid is now only required if it's NOT a credit sale
            'amount_paid' => 'required_without:credit_sale|nullable|numeric|min:0',
            'credit_sale' => 'nullable|boolean', // New validation for the credit_sale flag
            'is_installment' => 'nullable|boolean',
            'total_installments' => 'required_if:is_installment,true|nullable|integer|min:1',
            'installment_amount' => 'required_if:is_installment,true|nullable|numeric|min:0.01',
            'start_date' => 'required_if:is_installment,true|nullable|date',
            'phone_imeis' => 'nullable|array',
            'accessories' => 'nullable|array',
            'accessories.*.id' => 'required_with:accessories|integer|exists:accessories,id',
            'accessories.*.quantity' => 'required_with:accessories|integer|min:1',
        ]);

        // Custom validation: Ensure at least one item is selected.
        if (empty($request->input('phone_imeis')) && empty($request->input('accessories'))) {
            throw ValidationException::withMessages(['items' => 'At least one phone or accessory must be selected.']);
        }

        // 2. Validate phones and accessories stock before creating a sale
        $totalAmount = 0;
        $phonesToSell = [];
        $accessoriesToSell = [];

        // Validate and get phone details
        $phoneImeis = array_unique($request->input('phone_imeis', []));
        if (!empty($phoneImeis)) {
            $phones = Phone::whereIn('imei', $phoneImeis)->where('is_sold', false)->get();
            if ($phones->count() !== count($phoneImeis)) {
                throw ValidationException::withMessages(['items' => 'One or more selected phones are either not found or already sold.']);
            }
            $phonesToSell = $phones;
            foreach ($phonesToSell as $phone) {
                $totalAmount += $phone->selling_price;
            }
        }

        // Validate and get accessory details
        $accessories = $request->input('accessories', []);
        if (!empty($accessories)) {
            foreach ($accessories as $accessoryData) {
                $accessory = Accessory::findOrFail($accessoryData['id']);
                $quantity = $accessoryData['quantity'];

                // Stock validation check
                if ($accessory->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'accessories' => "Accessory {$accessory->name} does not have {$quantity} units available in stock."
                    ]);
                }
                $accessoriesToSell[] = ['accessory' => $accessory, 'quantity' => $quantity];
                $totalAmount += ($accessory->selling_price * $quantity);
            }
        }

        // Calculate final amount after discount
        $finalAmount = $totalAmount - $validated['discount_amount'];
        if ($finalAmount < 0) {
            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed the total amount.']);
        }

        // Determine the amount paid based on the credit_sale flag
        $amountPaid = 0;
        if (!($validated['credit_sale'] ?? false)) {
            // If it's NOT a credit sale, use the amount_paid from the request
            $amountPaid = $validated['amount_paid'] ?? 0;
        }

        // Validate that the amount paid does not exceed the final amount
        if ($amountPaid > $finalAmount) {
            throw ValidationException::withMessages(['amount_paid' => 'Amount paid cannot exceed the final sale amount.']);
        }

        // Calculate the remaining balance (the "credit" amount)
        if($validated['credit_sale'] == 1){
            $amountDue = $finalAmount - $amountPaid;
        }else{
            $amountDue =0;
        }


        try {
            DB::beginTransaction();

            // Re-validate and get phones inside the transaction with a lock
            $phoneImeis = array_unique($request->input('phone_imeis', []));
            if (!empty($phoneImeis)) {
                $phonesToSell = Phone::whereIn('imei', $phoneImeis)
                    ->where('is_sold', false)
                    ->lockForUpdate() // Lock the selected rows to prevent race conditions
                    ->get();
                if ($phonesToSell->count() !== count($phoneImeis)) {
                    throw ValidationException::withMessages(['items' => 'One or more selected phones are either not found or already sold.']);
                }
            }
            // 3. Create the Sale record with the calculated total amount and payment details
            $payment_option = null;
            if (isset($request['payment_option'])) {
                switch ((int) $request['payment_option']) {
                    case 1:
                        $payment_option = 'Cash';
                        break;
                    case 2:
                        $payment_option = 'Bank';
                        break;
                    case 3:
                        $payment_option = 'Phone';
                        break;
                    default:
                        $payment_option = 'Cash';
                }
            }


            $sale = Sale::create([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'total_amount' => $totalAmount,
                'discount_amount' => $validated['discount_amount'],
                'final_amount' => $finalAmount,
                'amount_paid' => $amountPaid,
                'amount_due' => $amountDue,
                'is_installment' => $request->boolean('is_installment'),
                'sale_date' => now(),
                'payment_option' => $payment_option,
            ]);

            // 4. Create sale items and update stock
            foreach ($phonesToSell as $phone) {
                $sale->saleItems()->create([
                    'phone_id' => $phone->id,
                    'accessory_id' => null,
                    'unit_price' => $phone->selling_price,
                    'quantity' => 1,
                ]);
                $phone->update(['is_sold' => true]);
            }

            foreach ($accessoriesToSell as $item) {
                $accessory = $item['accessory'];
                $quantity = $item['quantity'];

                $sale->saleItems()->create([
                    'accessory_id' => $accessory->id,
                    'phone_id' => null, // Explicitly set to null for accessories
                    'unit_price' => $accessory->selling_price,
                    'quantity' => $quantity,
                ]);
                // Decrease the stock
                $accessory->decrement('quantity', $quantity);
            }

            // 5. Handle installment details if applicable
            if ($sale->is_installment) {
                $sale->installment()->create([
                    'total_installments' => $validated['total_installments'],
                    'installment_amount' => $validated['installment_amount'],
                    'start_date' => $validated['start_date'],
                ]);
            }

            // 6. Generate the receipt automatically
            SaleReceipt::create([
                'receipt_number' => 'RCPT-' . Str::upper(Str::random(13)),
                'sale_id' => $sale->id,
                'issued_at' => now(),
                'subtotal' => $sale->total_amount,
                'tax' => 0.00, // Assuming tax is not calculated for now
                'discount' => $sale->discount_amount,
                'total' => $sale->final_amount,
                'is_installment' => $sale->is_installment,
                'paid_amount' => $sale->amount_paid,
                'balance' => $sale->amount_due,
                'payment_method' => 'cash',
                'status' => $sale->amount_due > 0 ? 'partial' : 'paid',
                'notes' => null,
            ]);
            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while recording the sale: ' . $e->getMessage())->withInput();
        }
    }


//    public function store(Request $request)
//    {
//
//        // 1. Basic validation for customer and discount
//        $validated = $request->validate([
//            'customer_name' => 'required|string|max:255',
//            'customer_phone' => 'nullable|string|max:255',
//            'customer_email' => 'nullable|email|max:255',
//            'discount_amount' => 'required|numeric|min:0',
//            'is_installment' => 'nullable|boolean',
//            'total_installments' => 'required_if:is_installment,true|nullable|integer|min:1',
//            'installment_amount' => 'required_if:is_installment,true|nullable|numeric|min:0.01',
//            'start_date' => 'required_if:is_installment,true|nullable|date',
//            'phone_imeis' => 'nullable|array',
//            'accessories' => 'nullable|array',
//            'accessories.*.id' => 'required_with:accessories|integer|exists:accessories,id',
//            'accessories.*.quantity' => 'required_with:accessories|integer|min:1',
//        ]);
//
//        // Custom validation: Ensure at least one item is selected.
//        if (empty($request->input('phone_imeis')) && empty($request->input('accessories'))) {
//            throw ValidationException::withMessages(['items' => 'At least one phone or accessory must be selected.']);
//        }
//
//        // 2. Validate phones and accessories stock before creating a sale
//        $totalAmount = 0;
//        $phonesToSell = [];
//        $accessoriesToSell = [];
//
//        // Validate and get phone details
//        $phoneImeis = array_unique($request->input('phone_imeis', []));
//        if (!empty($phoneImeis)) {
//            $phones = Phone::whereIn('imei', $phoneImeis)->where('is_sold', false)->get();
//            if ($phones->count() !== count($phoneImeis)) {
//                throw ValidationException::withMessages(['items' => 'One or more selected phones are either not found or already sold.']);
//            }
//            $phonesToSell = $phones;
//            foreach ($phonesToSell as $phone) {
//                $totalAmount += $phone->selling_price;
//            }
//        }
//
//        // Validate and get accessory details
//        $accessories = $request->input('accessories', []);
//        if (!empty($accessories)) {
//            foreach ($accessories as $accessoryData) {
//                $accessory = Accessory::findOrFail($accessoryData['id']);
//                $quantity = $accessoryData['quantity'];
//
//                // Stock validation check
//                if ($accessory->quantity < $quantity) {
//                    throw ValidationException::withMessages([
//                        'accessories' => "Accessory {$accessory->name} does not have {$quantity} units available in stock."
//                    ]);
//                }
//                $accessoriesToSell[] = ['accessory' => $accessory, 'quantity' => $quantity];
//                $totalAmount += ($accessory->selling_price * $quantity);
//            }
//        }
//
//        // Calculate final amount after discount
//        $finalAmount = $totalAmount - $validated['discount_amount'];
//        if ($finalAmount < 0) {
//            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed the total amount.']);
//        }
//
//        try {
//            DB::beginTransaction();
//
//            // 3. Create the Sale record with the calculated total amount
//            $sale = Sale::create([
//                'customer_name' => $validated['customer_name'],
//                'customer_phone' => $validated['customer_phone'],
//                'customer_email' => $validated['customer_email'],
//                'total_amount' => $totalAmount,
//                'discount_amount' => $validated['discount_amount'],
//                'final_amount' => $finalAmount,
//                'is_installment' => $request->boolean('is_installment'),
//                'sale_date' => now(),
//            ]);
//
//            // 4. Create sale items and update stock
//            foreach ($phonesToSell as $phone) {
//                $sale->saleItems()->create([
//                    'phone_id' => $phone->id,
//                    'accessory_id' => null,
//                    'unit_price' => $phone->selling_price,
//                    'quantity' => 1,
//                ]);
//                $phone->update(['is_sold' => true]);
//            }
//
//            foreach ($accessoriesToSell as $item) {
//                $accessory = $item['accessory'];
//                $quantity = $item['quantity'];
//
//                $sale->saleItems()->create([
//                    'accessory_id' => $accessory->id,
//                    'phone_id' => null, // Explicitly set to null for accessories
//                    'unit_price' => $accessory->selling_price,
//                    'quantity' => $quantity,
//                ]);
//                // Decrease the stock
//                $accessory->decrement('quantity', $quantity);
//            }
//
//            // 5. Handle installment details if applicable
//            if ($sale->is_installment) {
//                $sale->installment()->create([
//                    'total_installments' => $validated['total_installments'],
//                    'installment_amount' => $validated['installment_amount'],
//                    'start_date' => $validated['start_date'],
//                ]);
//            }
//
//            DB::commit();
//
//            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
//
//        } catch (ValidationException $e) {
//            DB::rollBack();
//            return back()->withErrors($e->errors())->withInput();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return back()->with('error', 'An error occurred while recording the sale: ' . $e->getMessage())->withInput();
//        }
//    }

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

    public function payForm(Sale $sale)
    {
        // Make sure this is a credit sale and not an installment
        if ($sale->amount_due <= 0 || $sale->is_installment) {
            return redirect()->route('sales.show', $sale)->with('error', 'This sale does not have an outstanding balance or is an installment plan.');
        }

        return view('sales.pay_form', compact('sale'));
    }

    /**
     * Store the payment for a credit sale.
     */
    public function storePayment(Request $request, Sale $sale)
    {
        // Validate the incoming request
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $sale->amount_due,
        ]);

        DB::beginTransaction();
        try {
            // Update the sale's financial details
            $sale->amount_paid += $request->input('payment_amount');
            $sale->amount_due -= $request->input('payment_amount');

            // Set to full payment if the amount due is 0
            if ($sale->amount_due <= 0) {
                $sale->is_installment = false;
            }

            $sale->save();

            DB::commit();
            return redirect()->route('sales.show', $sale)->with('success', 'Payment of $' . number_format($request->input('payment_amount'), 2) . ' has been recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record payment. Please try again.');
        }
    }
    public function showReturnPage()
    {
        return view('sales.return');
    }

    // This method handles searching for the sale details
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'required|string',
            'customer_phone' => 'nullable|string',
        ]);

        $query = Sale::with(['saleItems.phone', 'saleItems.accessory']);

        $query->join('sale_receipts', 'sales.id', '=', 'sale_receipts.sale_id');
        $query->where('sale_receipts.receipt_number', $validatedData['invoice_number']);

        // If a customer phone is provided, add that condition
        if ($validatedData['customer_phone']) {
            $query->where('sales.customer_phone', $validatedData['customer_phone']);
        }

        // Get the first matching sale, ensuring we only select from the sales table
        $sale = $query->select('sales.*')->first();

        if (!$sale) {
            return response()->json(['message' => 'Sale not found.'], 404);
        }

        return response()->json(['message' => 'Sale found!', 'sale' => $sale]);
    }

    public function processReturn(Request $request, Sale $sale)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:sale_items,id',
            'reason' => 'required|string|max:255',
        ]);
        try {
            DB::beginTransaction();

            foreach ($validatedData['items'] as $saleItemId) {
                $saleItem = SaleItem::find($saleItemId);

                if ($saleItem) {
                    // Revert the stock
                    if ($saleItem->phone_id) {
                        $phone = Phone::find($saleItem->phone_id);
                        if ($phone) {
                            $phone->status = 'available';
                            $phone->save();
                        }
                    } elseif ($saleItem->accessory_id) {
                        $accessory = Accessory::find($saleItem->accessory_id);
                        if ($accessory) {
                            $accessory->quantity += $saleItem->quantity;
                            $accessory->save();
                        }
                    }

                    // Log the return
                    ReturnLog::create([
                        'sale_id' => $sale->id,
                        'sale_item_id' => $saleItem->id,
                        'reason' => $validatedData['reason'],
                        'returned_by_user_id' => Auth()->user()->id,
                    ]);

                    $saleItem->delete();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Selected items successfully returned to stock.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred during the return process.'], 500);
        }
    }

}
