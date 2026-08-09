<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\AccessoryStock;
use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPhones = Phone::where('status', 'available')->count();
        $totalInvested = $this->inventoryCostValue();
        $totalAccessories = DB::table('cashews')
            ->where('status', 'available')->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Monthly Sales
        $monthlySales = Sale::activeTransaction()
            ->whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('final_amount');

        // Pending Installments
        $pendingInstallmentsAmount = 0;
        $activeInstallmentPlans = InstallmentPlan::where('status', 'active')
            ->whereHas('sale', fn ($query) => $query->activeTransaction())
            ->with(['sale.saleReceipt', 'installmentPayments'])
            ->get();

        foreach ($activeInstallmentPlans as $plan) {
            $receiptPaid = optional(optional($plan->sale)->saleReceipt)->paid_amount ?? 0;
            $totalPaid = $plan->installmentPayments->sum('amount_paid') + $receiptPaid;
            $remainingAmount = optional($plan->sale)->final_amount - $totalPaid;
            if ($remainingAmount > 0) {
                $pendingInstallmentsAmount += $remainingAmount;
            }
        }

        // Profit Margin
        $totalRevenueThisMonth = $monthlySales;
        $totalCogsThisMonth = 0;

        $soldPhonesThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->activeTransaction()
                ->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })->with('phone', 'product')->get();

        $totalCogsThisMonth = $this->saleItemsCostValue($soldPhonesThisMonth);

        $totalMonthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $grossProfit = $totalRevenueThisMonth - $totalCogsThisMonth;
        $netProfit = $grossProfit - $totalMonthlyExpenses;
        $profitMarginPercentage = $totalRevenueThisMonth > 0 ? ($netProfit / $totalRevenueThisMonth) * 100 : 0;
        $totalProfit = max($netProfit, 0);
        $totalLoss = max($netProfit * -1, 0);

        // Sales chart (last 30 days)
        $salesData = Sale::activeTransaction()
            ->select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total_sales')
        )
            ->where('sale_date', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesChartLabels = $salesData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('j M'))->toArray();
        $salesChartData = $salesData->pluck('total_sales')->toArray();

        // Inventory chart
        $inventoryDistribution = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('count(*) as count'))
            ->with('brand')
            ->groupBy('brand_id')
            ->get();

        $inventoryChartLabels = $inventoryDistribution->pluck('brand.name')->toArray();
        $inventoryChartData = $inventoryDistribution->pluck('count')->toArray();

        // Low stock products
        $lowStockProducts = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        // Notifications count
        $notificationCount = $lowStockProducts->count();

        // Recent Activities
        $recentSales = Sale::activeTransaction()->with('saleItems.phone.brand', 'saleItems.product')->latest('sale_date')->take(5)->get()->map(function($sale) {
            $itemNames = $sale->saleItems
                ->map(fn($item) => $item->phone
                    ? (optional($item->phone->brand)->name ?? 'N/A') . ' ' . $item->phone->model
                    : ($item->product ? $item->product->name . ' x ' . $item->quantity : 'Item removed'))
                ->implode(', ');
            return [
                'type' => 'sale',
                'description' => "✔️ {$itemNames} sold to {$sale->customer_name} - Tsh " . number_format($sale->final_amount, 2),
                'date' => $sale->sale_date,
                'link' => route('sales.show', $sale->id)
            ];
        });

        $recentReceivedPhones = Phone::with('brand')->latest('received_at')->take(5)->get()->map(function($phone) {
            return [
                'type' => 'received',
                'description' => 'Received ' . (optional($phone->brand)->name ?? 'N/A') . " {$phone->model} ({$phone->color}) (IMEI: {$phone->imei})",
                'date' => $phone->received_at,
                'link' => route('phones.index')
            ];
        });

        $recentInstallmentPayments = InstallmentPayment::with('installmentPlan.sale.saleItems.phone')
            ->latest('payment_date')->take(5)->get()->map(function($payment) {
                $firstPhone = $payment->installmentPlan?->sale?->saleItems->first()?->phone;
                $phoneName = $firstPhone?->brand?->name ?? 'N/A';
                $phoneModel = $firstPhone?->model ?? '';
                return [
                    'type' => 'payment',
                    'description' => "💵 Installment payment received for {$phoneName} {$phoneModel} - Tsh " . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => $payment->installmentPlan?->sale ? route('sales.show', $payment->installmentPlan->sale->id) : route('installments.index')
                ];
            });

        $recentActivitiesCollection = collect()
            ->concat($recentSales)
            ->concat($recentReceivedPhones)
            ->concat($recentInstallmentPayments)
            ->sortByDesc('date')
            ->values();

        $page = request()->get('page', 1);
        $perPage = 5;
        $recentActivities = new LengthAwarePaginator(
            $recentActivitiesCollection->forPage($page, $perPage),
            $recentActivitiesCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('dashboard', compact(
            'totalPhones',
            'totalAccessories',
            'monthlySales',
            'pendingInstallmentsAmount',
            'profitMarginPercentage',
            'salesChartLabels',
            'salesChartData',
            'inventoryChartLabels',
            'inventoryChartData',
            'lowStockProducts',
            'notificationCount',
            'recentActivities',
            'totalInvested',
            'grossProfit',
            'netProfit',
            'totalProfit',
            'totalLoss',
            'totalMonthlyExpenses'
        ));
    }

    private function inventoryCostValue(): float
    {
//        $phoneValue = (float) Phone::sum('purchase_price');
        $phoneValue = (float) Phone::where('status', '!=', 'sold')
            ->sum('purchase_price');
        $accessoryValue = Schema::hasTable('cashews')
            ? (float) DB::table('cashews')
            ->where('status', 'available')
            ->selectRaw('COALESCE(SUM(CAST(quantity AS DECIMAL(15, 2)) * CAST(unit_price AS DECIMAL(15, 2))), 0) as total')
            ->value('total')
            : 0;

        return $phoneValue + $accessoryValue;
    }

    private function saleItemsCostValue($saleItems): float
    {
        return (float) $saleItems->sum(function ($item) {
            $quantity = (float) ($item->quantity ?? 1);

            if ($item->phone) {
                return (float) $item->phone->purchase_price * $quantity;
            }

            $unitCost = $item->unit_cost ?? 0;
            return (float) $unitCost * $quantity;
        });
    }

    // Optional: Edit phone action
    public function editPhone(Phone $phone)
    {
        $this->authorize('edit phones');

        if ($this->phoneIsSaleLinked($phone)) {
            return redirect()->route('phones.index')->with('error', 'This phone is linked to a sale and can only be viewed.');
        }

        $brands = Brand::all(); // Fetch all brands
        return view('phones.edit', compact('phone', 'brands'));
    }

    // Optional: Delete phone action
    public function deletePhone(Phone $phone)
    {
        $this->authorize('delete phones');

        if ($this->phoneIsSaleLinked($phone)) {
            return redirect()->back()->with('error', 'This phone is linked to a sale and cannot be deleted. Void the sale first if it was recorded by mistake.');
        }

        DB::transaction(function () use ($phone) {
            if ($phone->status === 'available') {
                $stockLevel = StockLevel::where('brand_id', $phone->brand_id)
                    ->where('model', $phone->model)
                    ->where('color', $phone->color)
                    ->lockForUpdate()
                    ->first();

                if ($stockLevel && $stockLevel->current_stock > 0) {
                    $stockLevel->decrement('current_stock');
                    $stockLevel->last_updated_at = now();
                    $stockLevel->save();
                }
            }

            $phone->delete();
        });

        return redirect()->back()->with('success', 'Phone deleted successfully');
    }

    public function updatePhone(Request $request, Phone $phone)
    {
        $this->authorize('edit phones');

        if ($this->phoneIsSaleLinked($phone)) {
            return redirect()->route('phones.index')->with('error', 'This phone is linked to a sale and can only be viewed.');
        }

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'storage_capacity' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'imei' => 'required|string|max:255|unique:phones,imei,' . $phone->id,
        ]);

        $phone->update($validated);

        return redirect()->back()->with('success', 'Phone updated successfully');
    }

    public function editAccessory(Product $product)
    {
        $this->authorize('edit phones');

        $availableStock = $product->accessoryStocks()
            ->where('status', 'available')
            ->get();

        $currentStock = (int) $availableStock->sum('quantity');
        $latestStock = $availableStock->sortByDesc('received_at')->first();

        return view('accessories.edit', [
            'product' => $product,
            'currentStock' => $currentStock,
            'unit' => $latestStock?->unit ?? 'piece',
            'unitPrice' => $latestStock?->unit_price ?? 0,
            'sellingPrice' => $latestStock?->selling_price ?? 0,
            'lowStockThreshold' => $latestStock?->low_stock_threshold ?? 5,
        ]);
    }

    public function updateAccessory(Request $request, Product $product)
    {
        $this->authorize('edit phones');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:unit_price',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $product->update(['name' => $validated['name']]);

            $availableStocks = $product->accessoryStocks()
                ->where('status', 'available')
                ->lockForUpdate()
                ->orderByDesc('received_at')
                ->orderByDesc('id')
                ->get();

            $currentStock = (int) $availableStocks->sum('quantity');
            $targetStock = (int) $validated['current_stock'];

            foreach ($availableStocks as $stock) {
                $stock->update([
                    'unit' => $validated['unit'],
                    'unit_price' => $validated['unit_price'],
                    'selling_price' => $validated['selling_price'],
                    'low_stock_threshold' => $validated['low_stock_threshold'],
                ]);
            }

            if ($targetStock > $currentStock) {
                $increase = $targetStock - $currentStock;
                $stock = $availableStocks->first();

                if ($stock) {
                    $stock->increment('quantity', $increase);
                } else {
                    AccessoryStock::create([
                        'product_id' => $product->id,
                        'status' => 'available',
                        'received_at' => now()->toDateString(),
                        'batch_number' => 'ACC-ADJ-' . now()->format('YmdHis'),
                        'condition' => 'new',
                        'quantity' => $increase,
                        'unit' => $validated['unit'],
                        'unit_price' => $validated['unit_price'],
                        'selling_price' => $validated['selling_price'],
                        'barcode' => null,
                        'user_id' => auth()->id(),
                        'low_stock_threshold' => $validated['low_stock_threshold'],
                    ]);
                }
            }

            if ($targetStock < $currentStock) {
                $remainingReduction = $currentStock - $targetStock;

                foreach ($availableStocks as $stock) {
                    if ($remainingReduction <= 0) {
                        break;
                    }

                    $stockQuantity = (int) $stock->quantity;
                    $reduction = min($stockQuantity, $remainingReduction);
                    $newQuantity = $stockQuantity - $reduction;

                    $stock->quantity = $newQuantity;
                    $stock->save();

                    $remainingReduction -= $reduction;
                }
            }
        });

        return redirect()->route('phones.index')->with('success', 'Accessory updated successfully');
    }

    private function phoneIsSaleLinked(Phone $phone): bool
    {
        return in_array($phone->status, ['sold', 'under_installment'], true) || $phone->saleItem()->exists();
    }
}
