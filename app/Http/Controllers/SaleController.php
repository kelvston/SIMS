<?php
namespace App\Http\Controllers;

use App\Models\Cashew;
use App\Models\Cosmetic;
use App\Models\Medicine;
use App\Models\Product;
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
        $medicine = \App\Models\Medicine::where('imei', $code)->first();
        if ($medicine) {
            return response()->json([
                'success' => true,
                'type' => 'medicine',
                'item' => [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                ]
            ]);
        }

        $cosmetic = \App\Models\Cosmetic::where('barcode', $code)
            ->orWhere('id', $code)->first();
        if ($cosmetic) {
            return response()->json([
                'success' => true,
                'type' => 'cosmetic',
                'item' => [
                    'id' => $cosmetic->id,
                    'name' => $cosmetic->name,
                    'barcode' => $cosmetic->barcode,
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }


    public function index()
    {
        // Eager load saleItems and their associated medicines, and installmentPlan if it exists
        $sales = Sale::with([
            'saleItems.cashews.product',
            'saleItems.productSize',
            'installmentPlan',
            'soldBy',
        ])
            ->orderBy('sale_date', 'desc')
            ->paginate(5);

        \Artisan::call('stock:check-low'); // Run the command

        $lowStockAlerts = Cache::pull('low_stock_alerts', []);
        $hasOutstandingBalance = $sales->contains(fn($s) => $s->amount_due > 0 || $s->is_installment);

        return view('sales.index', compact('sales','lowStockAlerts','hasOutstandingBalance'));



    }

    /**
     * Show the form for creating a new sale.
     *
     * @return \Illuminate\View\View
     */
//    public function create()
//    {
//        // Fetch only available cashew for selection in the sales form
////        $availableCashews = Product::select('products.*')
////            ->leftJoin('cashews', 'products.id', '=', 'cashews.product_id')
////            ->where('cashews.quantity', '>', 0)
////            ->get();
//
//        $availableCashews = Cashew::with('product')
//            ->where('quantity', '>', 0)
//            ->get()
//            ->map(fn($c) => [
//                'id' => $c->product_id,
//                'barcode' => $c->barcode,
//                'product_name' => $c->product?->name ?? 'N/A',
//                'selling_price' => $c->selling_price,
//            ]);
//
//
//
//        $availableCosmetics = Cosmetic::where('status', 'in_stock')->where('quantity','>=',1)->orderBy('created_at')->get();
//        return view('sales.create', compact('availableCosmetics','availableCashews'));
//    }

    public function create()
    {
        $availableCashews = Cashew::with(['product', 'productSizes'])
            ->where('quantity', '>', 0)
            ->get()
            ->flatMap(function ($cashew) {
                if ($cashew->productSizes->count() > 0) {
                    return $cashew->productSizes
                        ->where('quantity', '>', 0)
                        ->map(fn($s) => [
                            'id'              => $cashew->id,        // cashew's actual id
                            'product_size_id' => $s->id,
                            'product_name'    => $cashew->product->name,
                            'display_name'    => $cashew->product->name . ' — ' . $s->size . ' / ' . $s->color,
                            'size'            => $s->size,
                            'color'           => $s->color,
                            'selling_price'   => $cashew->selling_price,
                            'unit'            => $cashew->unit,
                            'available_qty'   => $s->quantity,
                            'barcode'         => $cashew->barcode ?? null,
                        ]);
                }

                return [[
                    'id'              => $cashew->id,
                    'product_size_id' => null,
                    'product_name'    => $cashew->product->name,
                    'display_name'    => $cashew->product->name,
                    'size'            => null,
                    'color'           => null,
                    'selling_price'   => $cashew->selling_price,
                    'unit'            => $cashew->unit,
                    'available_qty'   => $cashew->quantity,
                    'barcode'         => $cashew->barcode ?? null,
                ]];
            })
            ->values();

        $availableCosmetics = Cosmetic::where('status', 'in_stock')->where('quantity', '>=', 1)->orderBy('created_at')->get();

        return view('sales.create', compact('availableCosmetics', 'availableCashews'));
    }
    public function printReceipt($id)
    {
        $receipt = SaleReceipt::with('sale')->where('sale_id', $id)->firstOrFail();
        $receipt->load(['sale.saleItems.cashews.product', 'sale.saleItems.productSize', 'sale.soldBy']);
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('sales.print_receipt', compact('receipt', 'settings'));
    }

    public function printSingleReceipt(SaleReceipt $receipt)
    {
        $receipt->load(['sale.saleItems.medicine', 'sale.saleItems.cosmetic']);
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
//
//        // 1. Basic validation for customer, discount, and the new credit_sale flag
//        $validated = $request->validate([
//            'customer_name' => 'nullable',
//            'customer_email' => 'nullable',
//            'payment_option' => 'nullable|numeric|min:0',
//            'quantities' => 'required|array',
//            'cashew_ids' => 'required|array',
//            'amount_paid' => 'required_without:credit_sale|nullable|numeric|min:0',
//            'credit_sale' => 'nullable|boolean', // New validation for the credit_sale flag
//            'is_installment' => 'nullable|boolean',
//            'total_installments' => 'required_if:is_installment,true|nullable|integer|min:1',
//            'installment_amount' => 'required_if:is_installment,true|nullable|numeric|min:0.01',
//            'start_date' => 'required_if:is_installment,true|nullable|date',
//            'discount_amount' => 'nullable|numeric|min:0',
//        ]);
//
//
//        // 2. Validate product stock before creating a sale
//        $totalAmount = 0;
//        $cashewsToSell = [];
//
//        // Validate and get cashew details
//        $totalAmount = 0;
//        $cashewIds = $request->input('cashew_ids', []);
//        $quantities = $request->input('quantities', []);
//        foreach ($cashewIds as $index => $productId) {
//            $qty = $quantities[$index] ?? 0;
//            $cashew = Cashew::where('product_id', $productId)->first();
//            if (!$cashew) {
//                throw ValidationException::withMessages([
//                    'items' => "Product not found for ID {$productId}"
//                ]);
//            }
//            if ($cashew->quantity < $qty) {
//                throw ValidationException::withMessages([
//                    'items' => "Not enough stock for the product "
//                ]);
//            }
//            $totalAmount += $cashew->selling_price * $qty;
//        }
//
//
//        // Calculate final amount after discount
//
//        $finalAmount = $totalAmount - $validated['discount_amount'];
//        if ($finalAmount < 0) {
//            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed the total amount.']);
//        }
//
//        // Determine the amount paid based on the credit_sale flag
//        $amountPaid = 0;
//        if (!($validated['credit_sale'] ?? false)) {
//            // If it's NOT a credit sale, use the amount_paid from the request
//            $amountPaid = $validated['amount_paid'] ?? 0;
//        }
//
//        // Validate that the amount paid does not exceed the final amount
//        if ($amountPaid > $finalAmount) {
//            throw ValidationException::withMessages(['amount_paid' => 'Amount paid cannot exceed the final sale amount.']);
//        }
//
//        // Calculate the remaining balance (the "credit" amount)
//        if($validated['credit_sale'] == 1){
//            $amountDue = $finalAmount - $amountPaid;
//        }else{
//            $amountDue =0;
//        }
//
//
//        try {
//            DB::beginTransaction();
//
//            // Re-validate and get products inside the transaction with a lock
//            $cashewIds = $request->cashew_ids;
//            $quantities = $request->quantities;
//            foreach ($cashewIds as $index => $productId) {
//                $qty = $quantities[$index];
//                $cashew = Cashew::where('product_id', $productId)->lockForUpdate()->first();
//                if (!$cashew) {
//                    throw ValidationException::withMessages([
//                        'items' => "Product not found for ID {$productId}"
//                    ]);
//                }
//                if ($cashew->quantity < $qty) {
//                    throw ValidationException::withMessages([
//                        'items' => "Not enough stock for the product"
//                    ]);
//                }
//            }
//            // 3. Create the Sale record with the calculated total amount and payment details
//            $payment_option = null;
//            if (isset($request['payment_option'])) {
//                switch ((int) $request['payment_option']) {
//                    case 1:
//                        $payment_option = 'Cash';
//                        break;
//                    case 2:
//                        $payment_option = 'Bank';
//                        break;
//                    case 3:
//                        $payment_option = 'Phone';
//                        break;
//                    default:
//                        $payment_option = 'Cash';
//                }
//            }
//
//
//            $sale = Sale::create([
//                'customer_name' => $validated['customer_name'],
//                'customer_email' => $validated['customer_email'],
//                'total_amount' => $totalAmount,
//                'discount_amount' => $validated['discount_amount'],
//                'final_amount' => $finalAmount,
//                'amount_paid' => $amountPaid,
//                'amount_due' => $amountDue,
//                'is_installment' => $request->boolean('is_installment'),
//                'sale_date' => now(),
//                'payment_option' => $payment_option,
//                'user_id' => auth()->id(),
//            ]);
//
//            // 4. Create sale items and update stock
//            foreach ($validated['cashew_ids'] as $index => $productId) {
//                $cashew = Cashew::where('product_id', $productId)->first();
//                 $qty = $validated['quantities'][$index];
//                $sale->saleItems()->create([
//                    'product_id' => $cashew->product_id,
//                    'cosmetic_id' => null,
//                    'unit_price' => $cashew->selling_price,
//                    'unit_cost' => $cashew->unit_price,
//                    'quantity' => $qty,
//                ]);
//                // reduce stock
//                $cashew->quantity -= $qty;
//                $cashew->save();
//            }
//
//
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
//            // 6. Generate the receipt automatically
//            SaleReceipt::create([
//                'receipt_number' => 'RCPT-' . Str::upper(Str::random(13)),
//                'sale_id' => $sale->id,
//                'issued_at' => now(),
//                'subtotal' => $sale->total_amount,
//                'tax' => 0.00, // Assuming tax is not calculated for now
//                'discount' => $sale->discount_amount,
//                'total' => $sale->final_amount,
//                'is_installment' => $sale->is_installment,
//                'paid_amount' => $sale->amount_paid,
//                'balance' => $sale->amount_due,
//                'payment_method' => 'cash',
//                'status' => $sale->amount_due > 0 ? 'partial' : 'paid',
//                'notes' => null,
//            ]);
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'      => 'nullable',
            'customer_email'     => 'nullable',
            'payment_option'     => 'nullable|numeric|min:0',
            'quantities'         => 'required|array',
            'cashew_ids'         => 'required|array',
            'product_size_ids'   => 'nullable|array',   // ← new
            'amount_paid'        => 'required_without:credit_sale|nullable|numeric|min:0',
            'credit_sale'        => 'nullable|boolean',
            'is_installment'     => 'nullable|boolean',
            'total_installments' => 'required_if:is_installment,true|nullable|integer|min:1',
            'installment_amount' => 'required_if:is_installment,true|nullable|numeric|min:0.01',
            'start_date'         => 'required_if:is_installment,true|nullable|date',
            'discount_amount'    => 'nullable|numeric|min:0',
        ]);

        $cashewIds     = $request->input('cashew_ids', []);
        $quantities    = $request->input('quantities', []);
        $productSizeIds= $request->input('product_size_ids', []);

        // --- Pre-transaction stock check ---
        $totalAmount = 0;

        foreach ($cashewIds as $index => $cashewId) {
            $qty    = $quantities[$index] ?? 0;
            $cashew = Cashew::find($cashewId);

            if (!$cashew) {
                throw ValidationException::withMessages([
                    'items' => "Cashew record not found for ID {$cashewId}"
                ]);
            }

            $sizeId = $productSizeIds[$index] ?? null;

            if ($sizeId) {
                // Variant product — check size stock
                $sizeVariant = \App\Models\ProductSize::whereKey($sizeId)
                    ->where('product_id', $cashew->product_id)
                    ->first();
                if (!$sizeVariant || $sizeVariant->quantity < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for size/color variant."
                    ]);
                }
            } else {
                // Plain product — check cashew stock
                if ($cashew->quantity < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$cashew->product->name}."
                    ]);
                }
            }

            $totalAmount += $cashew->selling_price * $qty;
        }

        // --- Discount & payment calc ---
        $discountAmount = $validated['discount_amount'] ?? 0;
        $finalAmount    = $totalAmount - $discountAmount;

        if ($finalAmount < 0) {
            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed the total amount.']);
        }

        $amountPaid = 0;
        if (!($validated['credit_sale'] ?? false)) {
            $amountPaid = $validated['amount_paid'] ?? 0;
        }

        if ($amountPaid > $finalAmount) {
            throw ValidationException::withMessages(['amount_paid' => 'Amount paid cannot exceed the final sale amount.']);
        }

        $amountDue = ($validated['credit_sale'] == 1) ? ($finalAmount - $amountPaid) : 0;

        // --- Payment option label ---
        $payment_option = match((int) $request->input('payment_option')) {
            1 => 'Cash',
            2 => 'Bank',
            3 => 'Phone',
            default => 'Cash',
        };

        try {
            DB::beginTransaction();

            // Lock rows and re-check stock inside transaction
            foreach ($cashewIds as $index => $cashewId) {
                $qty    = $quantities[$index];
                $sizeId = $productSizeIds[$index] ?? null;
                $cashew = Cashew::lockForUpdate()->find($cashewId);

                if ($sizeId) {
                    $sizeVariant = \App\Models\ProductSize::whereKey($sizeId)
                        ->where('product_id', $cashew->product_id)
                        ->lockForUpdate()
                        ->first();
                    if (!$sizeVariant || $sizeVariant->quantity < $qty) {
                        throw ValidationException::withMessages(['items' => 'Not enough stock for size/color variant (locked check).']);
                    }
                } else {
                    if (!$cashew || $cashew->quantity < $qty) {
                        throw ValidationException::withMessages(['items' => "Not enough stock for {$cashew->product->name} (locked check)."]);
                    }
                }
            }

            // Create sale
            $sale = Sale::create([
                'customer_name'  => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'total_amount'   => $totalAmount,
                'discount_amount'=> $discountAmount,
                'final_amount'   => $finalAmount,
                'amount_paid'    => $amountPaid,
                'amount_due'     => $amountDue,
                'is_installment' => $request->boolean('is_installment'),
                'sale_date'      => now(),
                'payment_option' => $payment_option,
                'user_id'        => auth()->id(),
            ]);

            // Create sale items + deduct stock
            foreach ($cashewIds as $index => $cashewId) {
                $qty    = $quantities[$index];
                $sizeId = $productSizeIds[$index] ?? null;
                $cashew = Cashew::find($cashewId);

                $sale->saleItems()->create([
                    'product_id'      => $cashew->product_id,
                    'cosmetic_id'     => null,
                    'unit_price'      => $cashew->selling_price,
                    'unit_cost'       => $cashew->unit_price,
                    'quantity'        => $qty,
                    'product_size_id' => $sizeId,
                ]);

                // Deduct from cashews total quantity
                $cashew->decrement('quantity', $qty);

                // Deduct from product_sizes if variant
                if ($sizeId) {
                    \App\Models\ProductSize::find($sizeId)->decrement('quantity', $qty);
                }
            }

            // Installment plan
            if ($sale->is_installment) {
                $sale->installment()->create([
                    'total_installments' => $validated['total_installments'],
                    'installment_amount' => $validated['installment_amount'],
                    'start_date'         => $validated['start_date'],
                ]);
            }

            // Receipt
            SaleReceipt::create([
                'receipt_number' => 'RCPT-' . Str::upper(Str::random(13)),
                'sale_id'        => $sale->id,
                'issued_at'      => now(),
                'subtotal'       => $sale->total_amount,
                'tax'            => 0.00,
                'discount'       => $sale->discount_amount,
                'total'          => $sale->final_amount,
                'is_installment' => $sale->is_installment,
                'paid_amount'    => $sale->amount_paid,
                'balance'        => $sale->amount_due,
                'payment_method' => 'cash',
                'status'         => $sale->amount_due > 0 ? 'partial' : 'paid',
                'notes'          => null,
            ]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
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
        $sale->load(['saleItems.cashews.product', 'saleItems.productSize', 'installmentPlan.installmentPayments', 'soldBy']);
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
            'customer_medicine' => 'nullable|string',
        ]);

        $query = Sale::with(['saleItems.medicine', 'saleItems.cosmetic']);

        $query->join('sale_receipts', 'sales.id', '=', 'sale_receipts.sale_id');
        $query->where('sale_receipts.receipt_number', $validatedData['invoice_number']);

        // If a customer medicine is provided, add that condition
        if ($validatedData['customer_medicine']) {
            $query->where('sales.customer_medicine', $validatedData['customer_medicine']);
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
                    if ($saleItem->medicine_id) {
                        $medicine = Medicine::find($saleItem->medicine_id);
                        if ($medicine) {
                            $medicine->status = 'available';
                            $medicine->save();
                        }
                    } elseif ($saleItem->cosmetic_id) {
                        $cosmetic = Cosmetic::find($saleItem->cosmetic_id);
                        if ($cosmetic) {
                            $cosmetic->quantity += $saleItem->quantity;
                            $cosmetic->save();
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
