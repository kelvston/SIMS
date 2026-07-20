<?php
namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\AccessoryStock;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InstallmentPlan;
use App\Models\SaleReceipt;
use App\Models\StockLevel;
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
        $this->middleware(['auth', 'permission:view sales'])->only(['index', 'show', 'receipt']);
        $this->middleware(['auth', 'permission:create sales'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:delete sales'])->only('void');
    }


    public function index()
    {
        // Eager load saleItems and their associated phones, and installmentPlan if it exists
        $sales = Sale::with(['saleItems.phone.brand', 'saleItems.product', 'installmentPlan', 'voidedBy'])
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
        $availablePhones = Phone::with('brand')
            ->where('status', 'available')
            ->orderBy('model')
            ->orderBy('storage_capacity')
            ->orderBy('imei')
            ->get();
        $accessoryProducts = Product::query()
            ->whereHas('accessoryStocks', fn ($query) => $query->where('status', 'available')->where('quantity', '>', 0))
            ->withSum(['accessoryStocks as available_quantity' => fn ($query) => $query->where('status', 'available')], 'quantity')
            ->withMax(['accessoryStocks as selling_price' => fn ($query) => $query->where('status', 'available')->where('quantity', '>', 0)], 'selling_price')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('availablePhones', 'accessoryProducts'));
    }

    /**
     * Store a newly created sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $request->merge([
            'phone_imeis' => collect($request->input('phone_imeis', []))
                ->map(fn ($imei) => preg_replace('/\s+/', '', trim((string) $imei)))
                ->filter()
                ->values()
                ->all(),
            'accessories' => collect($request->input('accessories', []))
                ->map(function ($item) {
                    return [
                        'product_id' => $item['product_id'] ?? null,
                        'quantity' => (int) ($item['quantity'] ?? 0),
                    ];
                })
                ->filter(fn ($item) => $item['product_id'] && $item['quantity'] > 0)
                ->values()
                ->all(),
            'discount_amount' => $request->filled('discount_amount') ? $request->input('discount_amount') : 0,
        ]);

        // Validate the request data
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'phone_imeis' => 'nullable|array',
            'phone_imeis.*' => 'required|string|distinct|exists:phones,imei',
            'accessories' => 'nullable|array',
            'accessories.*.product_id' => 'required|exists:products,id',
            'accessories.*.quantity' => 'required|integer|min:1',
            'discount_amount' => 'nullable|numeric|min:0',
            'is_installment' => 'boolean',
            'total_installments' => 'required_if:is_installment,1|nullable|integer|min:1',
            'installment_amount' => 'required_if:is_installment,1|nullable|numeric|min:0.01',
            'start_date' => 'required_if:is_installment,1|nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $phoneIds = [];
            $accessoryLines = [];
            $totalAmountCents = 0;

            // Fetch phones and calculate total amount
            foreach ($request->phone_imeis as $imei) {
                $phone = Phone::where('imei', $imei)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (!$phone) {
                    throw ValidationException::withMessages([
                        'phone_imeis' => ['Phone with IMEI ' . $imei . ' is not available for sale.'],
                    ]);
                }

                $phoneIds[] = $phone->id;
                $totalAmountCents += $this->moneyToCents($phone->selling_price);
            }

            foreach ($request->input('accessories', []) as $accessoryInput) {
                $productId = (int) $accessoryInput['product_id'];
                $quantityNeeded = (int) $accessoryInput['quantity'];
                $remainingQuantity = $quantityNeeded;
                $lineTotalCents = 0;
                $lineCostCents = 0;
                $batchesUsed = [];

                $product = Product::lockForUpdate()->findOrFail($productId);
                $batches = AccessoryStock::where('product_id', $productId)
                    ->where('status', 'available')
                    ->where('quantity', '>', 0)
                    ->orderBy('received_at')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingQuantity <= 0) {
                        break;
                    }

                    $availableQuantity = (int) floor((float) $batch->quantity);
                    $soldFromBatch = min($availableQuantity, $remainingQuantity);

                    if ($soldFromBatch <= 0) {
                        continue;
                    }

                    $batch->quantity = $availableQuantity - $soldFromBatch;
                    if ($batch->quantity <= 0) {
                        $batch->status = 'sold';
                    }
                    $batch->save();

                    $lineTotalCents += $this->moneyToCents($batch->selling_price) * $soldFromBatch;
                    $lineCostCents += $this->moneyToCents($batch->unit_price) * $soldFromBatch;
                    $batchesUsed[] = [
                        'batch_id' => $batch->id,
                        'quantity' => $soldFromBatch,
                    ];
                    $remainingQuantity -= $soldFromBatch;
                }

                if ($remainingQuantity > 0) {
                    throw ValidationException::withMessages([
                        'accessories' => [$product->name . ' has only ' . ($quantityNeeded - $remainingQuantity) . ' unit(s) available.'],
                    ]);
                }

                $unitPriceCents = (int) round($lineTotalCents / max($quantityNeeded, 1));
                $unitCostCents = (int) round($lineCostCents / max($quantityNeeded, 1));

                $accessoryLines[] = [
                    'product' => $product,
                    'quantity' => $quantityNeeded,
                    'unit_price' => $this->centsToMoney($unitPriceCents),
                    'unit_cost' => $this->centsToMoney($unitCostCents),
                    'batches_used' => $batchesUsed,
                ];

                $totalAmountCents += $lineTotalCents;
            }

            if (empty($phoneIds) && empty($accessoryLines)) {
                throw ValidationException::withMessages([
                    'phone_imeis' => ['Add at least one phone or accessory to the sale.'],
                ]);
            }

            $discountAmountCents = $this->moneyToCents($request->input('discount_amount', 0));
            $finalAmountCents = $totalAmountCents - $discountAmountCents;

            if ($finalAmountCents < 0) {
                throw ValidationException::withMessages([
                    'discount_amount' => ['Discount cannot exceed the total amount.'],
                ]);
            }

            $isInstallment = $request->boolean('is_installment');
            $paidAmountCents = $isInstallment
                ? $this->moneyToCents($request->installment_amount)
                : $finalAmountCents;

            if ($isInstallment) {
                $installmentTotalCents = $this->moneyToCents($request->installment_amount) * (int) $request->total_installments;

                if ($installmentTotalCents < $finalAmountCents) {
                    throw ValidationException::withMessages([
                        'installment_amount' => ['Total installment amount is less than the final sale amount.'],
                    ]);
                }
            }

            $totalAmount = $this->centsToMoney($totalAmountCents);
            $discountAmount = $this->centsToMoney($discountAmountCents);
            $finalAmount = $this->centsToMoney($finalAmountCents);
            $paidAmount = $this->centsToMoney($paidAmountCents);
            $amountDue = $this->centsToMoney(max($finalAmountCents - $paidAmountCents, 0));

            // Create the Sale record
            $sale = Sale::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'sale_date' => now(),
                'is_installment' => $isInstallment,
                'amount_paid' => $paidAmount,
                'amount_due' => $amountDue,
                'payment_option' => $isInstallment ? 'installment' : 'cash',
                'status' => 'completed',
            ]);

            // Create SaleItem records, update phone status, and reduce stock level
            foreach ($phoneIds as $phoneId) {
                $phone = Phone::lockForUpdate()->find($phoneId);

                $phone->status = $isInstallment ? 'under_installment' : 'sold';
                $phone->save();

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'phone_id' => $phone->id,
                    'unit_price' => $this->centsToMoney($this->moneyToCents($phone->selling_price)),
                    'quantity' => 1,
                    'unit_cost' => $this->centsToMoney($this->moneyToCents($phone->purchase_price)),
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

            foreach ($accessoryLines as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $line['product']->id,
                    'phone_id' => null,
                    'cosmetic_id' => $line['batches_used'][0]['batch_id'] ?? null,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                ]);
            }

            // Generate unique receipt number
            $receiptNumber = 'RCPT-' . strtoupper(uniqid());

            // Create the receipt
            $receipt = SaleReceipt::create([
                'receipt_number' => $receiptNumber,
                'sale_id' => $sale->id,
                'issued_at' => now(),
                'subtotal' => $totalAmount,
                'tax' => 0,
                'discount' => $discountAmount,
                'total' => $finalAmount,
                'is_installment' => $isInstallment,
                'paid_amount' => $paidAmount,
                'payment_method' => 'cash',
                'status' => $paidAmountCents <= 0 ? 'unpaid' : ($paidAmountCents < $finalAmountCents ? 'partial' : 'paid'),
                'notes' => null,
            ]);

            // If it's an installment sale, create InstallmentPlan
            if ($isInstallment) {
                InstallmentPlan::create([
                    'sale_id' => $sale->id,
                    'total_installments' => $request->total_installments,
                    'installment_amount' => $this->centsToMoney($this->moneyToCents($request->installment_amount)),
                    'start_date' => $request->start_date,
                    'next_payment_date' => $request->start_date,
                    'status' => 'active',
                ]);
            }

            DB::commit();

            $emailWarning = null;

            if ($sale->customer_email) {
                try {
                    $sale->load('saleItems.phone.brand', 'saleItems.product', 'installmentPlan');
                    $receipt->load('sale');

                    $pdf = Pdf::loadView('pdf.receipt', ['sale' => $sale, 'receipt' => $receipt]);
                    $pdfContent = base64_encode($pdf->output());

                    Mail::to($sale->customer_email)->send(new SaleReceiptMail($sale, $receipt, $pdfContent));
                } catch (\Throwable $mailException) {
                    \Log::warning('Sale receipt email failed for sale #' . $sale->id . ': ' . $mailException->getMessage());
                    $emailWarning = 'Sale recorded successfully, but the receipt email was not sent. Please check mail settings.';
                }
            }

            $redirect = redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');

            if ($emailWarning) {
                $redirect->with('warning', $emailWarning);
            }

            return $redirect;
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording sale: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to record sale. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }

    private function moneyToCents($amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function centsToMoney(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
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
        $sale->load(['saleItems.phone.brand', 'saleItems.product', 'saleReceipt', 'installmentPlan.installmentPayments', 'voidedBy']);
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['saleItems.phone.brand', 'saleItems.product', 'saleReceipt', 'installmentPlan']);

        if (! $sale->saleReceipt) {
            abort(404, 'Receipt not found for this sale.');
        }

        $pdf = Pdf::loadView('pdf.receipt', [
            'sale' => $sale,
            'receipt' => $sale->saleReceipt,
        ]);

        return $pdf->download($sale->saleReceipt->receipt_number . '.pdf');
    }

    public function void(Request $request, Sale $sale)
    {
        $request->validate([
            'void_reason' => 'required|string|min:5|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                $sale = Sale::with(['saleItems.phone', 'saleItems.product', 'saleReceipt', 'installmentPlan.installmentPayments'])
                    ->lockForUpdate()
                    ->findOrFail($sale->id);

                if ($sale->is_voided) {
                    throw ValidationException::withMessages([
                        'void_reason' => ['This sale has already been voided.'],
                    ]);
                }

                if ($sale->installmentPlan && $sale->installmentPlan->installmentPayments->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'void_reason' => ['This installment sale already has installment payments. Reverse those payments before voiding the sale.'],
                    ]);
                }

                foreach ($sale->saleItems as $item) {
                    if (! $item->phone) {
                        if ($item->product_id) {
                            $stock = AccessoryStock::where('product_id', $item->product_id)
                                ->where('status', 'available')
                                ->orderByDesc('id')
                                ->lockForUpdate()
                                ->first();

                            if (! $stock) {
                                $stock = AccessoryStock::create([
                                    'product_id' => $item->product_id,
                                    'status' => 'available',
                                    'received_at' => now()->toDateString(),
                                    'batch_number' => 'VOID-' . $sale->id . '-' . $item->id,
                                    'condition' => 'returned',
                                    'quantity' => 0,
                                    'unit' => 'piece',
                                    'unit_price' => $item->unit_cost ?? 0,
                                    'selling_price' => $item->unit_price,
                                    'user_id' => auth()->id(),
                                    'low_stock_threshold' => 5,
                                ]);
                            }

                            $stock->quantity = (int) $stock->quantity + (int) ($item->quantity ?? 1);
                            $stock->status = 'available';
                            $stock->save();
                        }

                        continue;
                    }

                    $phone = Phone::lockForUpdate()->find($item->phone_id);

                    if (! $phone) {
                        continue;
                    }

                    if (in_array($phone->status, ['sold', 'under_installment'], true)) {
                        $phone->status = 'available';
                        $phone->save();

                        $stockLevel = StockLevel::firstOrCreate(
                            [
                                'brand_id' => $phone->brand_id,
                                'model' => $phone->model,
                                'color' => $phone->color,
                            ],
                            [
                                'current_stock' => 0,
                                'low_stock_threshold' => 5,
                                'last_updated_at' => now(),
                            ]
                        );

                        $stockLevel->increment('current_stock');
                        $stockLevel->last_updated_at = now();
                        $stockLevel->save();
                    }
                }

                $sale->forceFill([
                    'status' => 'voided',
                    'voided_at' => now(),
                    'voided_by' => auth()->id(),
                    'void_reason' => $request->void_reason,
                    'original_final_amount' => $sale->final_amount,
                ])->save();

                if ($sale->saleReceipt) {
                    $existingNotes = trim((string) $sale->saleReceipt->notes);
                    $voidNote = 'Voided on ' . now()->format('Y-m-d H:i') . ': ' . $request->void_reason;

                    $sale->saleReceipt->forceFill([
                        'status' => 'voided',
                        'notes' => $existingNotes === '' ? $voidNote : $existingNotes . "\n" . $voidNote,
                    ])->save();
                }
            });

            return redirect()->route('sales.show', $sale->id)->with('success', 'Sale voided successfully. Items were returned to available stock.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error voiding sale: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to void sale. Please try again. Error: ' . $e->getMessage());
        }
    }
}
