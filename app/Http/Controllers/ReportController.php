<?php



namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AccessoryStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use App\Models\Phone;
use App\Models\Product;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
 use Barryvdh\DomPDF\Facade\Pdf;
 use Illuminate\Support\Facades\Mail;
 use App\Mail\GeneralReportMail;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling

class ReportController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    public function __construct()
    {
        // Protect report related actions
        $this->middleware('auth'); // All reports require authentication
        $this->middleware('permission:view sales reports')->only('salesReport');
        $this->middleware('permission:view stock reports')->only('stockReport');
        $this->middleware('permission:view profit loss reports')->only('profitLossReport');
        // Dashboard can be accessed by anyone with 'view dashboard' permission
        $this->middleware('permission:view dashboard')->only('home');
        $this->middleware('permission:view general reports')->only(['generalReport', 'downloadGeneralReport', 'sendGeneralReportEmail']);
    }


    public function home()
    {
        // 1. Total Phones (Available in Stock)
        $totalPhones = Phone::where('status', 'available')->count();

        // 2. Monthly Sales (Current Month's Revenue)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlySales = Sale::activeTransaction()
            ->whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('final_amount');

        // 3. Pending Installments Amount
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

        // 4. Profit Margin (Current Month)
        $totalRevenueThisMonth = $monthlySales; // Already calculated

        $totalCogsThisMonth = 0;
        $soldPhonesThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->activeTransaction()
                ->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })
            ->with('phone')
            ->get();

        $totalCogsThisMonth = $this->saleItemsCostValue($soldPhonesThisMonth);

        // NEW: Total Expenses for the current month
        $totalMonthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $grossProfit = $totalRevenueThisMonth - $totalCogsThisMonth - $totalMonthlyExpenses; // Deduct expenses
        $profitMarginPercentage = 0;
        if ($totalRevenueThisMonth > 0) {
            $profitMarginPercentage = ($grossProfit / $totalRevenueThisMonth) * 100;
        }

        // 5. Sales Chart Data (Last 30 days)
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

        // 6. Inventory Distribution Chart Data (Available Phones by Brand)
        $inventoryDistribution = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('count(*) as count'))
            ->with('brand')
            ->groupBy('brand_id')
            ->get();

        $inventoryChartLabels = $inventoryDistribution->pluck('brand.name')->toArray();
        $inventoryChartData = $inventoryDistribution->pluck('count')->toArray();

        // 7. Low Stock Products
        $lowStockProducts = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        // Count for notifications (e.g., low stock items)
        $notificationCount = $lowStockProducts->count();

        // New: Total Sales for the Year
        $totalYearlySales = Sale::activeTransaction()->whereYear('sale_date', $currentYear)->sum('final_amount');

        // New: Total Sold Phones
        $totalSoldPhones = Phone::where('status', 'sold')->count();

        // New: Total Active Installment Plans
        $totalActiveInstallments = InstallmentPlan::where('status', 'active')
            ->whereHas('sale', fn ($query) => $query->activeTransaction())
            ->count();

        // New: Average Selling Price of ALL phones (could be refined to average *sold* price)
        $averageSellingPrice = Phone::avg('selling_price');

        // New: Recent Activities (combining sales, received, payments, expenses)
        $recentSales = Sale::activeTransaction()
            ->with('saleItems.phone.brand')
            ->latest('sale_date')
            ->take(5)
            ->get()
            ->map(function($sale) {
                $phoneNames = $sale->saleItems
                    ->map(fn($item) => $item->phone
                        ? (optional($item->phone->brand)->name ?? 'N/A') . ' ' . $item->phone->model
                        : 'Phone removed')
                    ->implode(', ');
                return [
                    'type' => 'sale',
                    'description' => "✔️ {$phoneNames} sold to {$sale->customer_name} - $" . number_format($sale->final_amount, 2),
                    'date' => $sale->sale_date,
                    'link' => route('sales.show', $sale->id)
                ];
            });

        $recentReceivedPhones = Phone::with('brand')
            ->latest('received_at')
            ->take(5)
            ->get()
            ->map(function($phone) {
                return [
                    'type' => 'received',
                    'description' => 'Received ' . (optional($phone->brand)->name ?? 'N/A') . " {$phone->model} ({$phone->color}) into inventory (IMEI: {$phone->imei})",
                    'date' => $phone->received_at,
                    'link' => route('phones.index') // Link to general phone inventory
                ];
            });

        $recentInstallmentPayments = InstallmentPayment::with('installmentPlan.sale.saleItems.phone')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function($payment) {
                $phoneName = 'N/A';
                if ($payment->installmentPlan && $payment->installmentPlan->sale && $payment->installmentPlan->sale->saleItems->isNotEmpty()) {
                    $firstPhone = $payment->installmentPlan->sale->saleItems->first()->phone;
                    $phoneName = $firstPhone
                        ? (optional($firstPhone->brand)->name ?? 'N/A') . ' ' . $firstPhone->model
                        : 'Phone removed';
                }
                return [
                    'type' => 'payment',
                    'description' => "💵 Installment payment received for {$phoneName} - $" . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => $payment->installmentPlan?->sale ? route('sales.show', $payment->installmentPlan->sale->id) : route('installments.index')
                ];
            });

        // NEW: Recent Expenses
        $recentExpenses = Expense::latest('expense_date')
            ->take(5)
            ->get()
            ->map(function($expense) {
                return [
                    'type' => 'expense',
                    'description' => "💸 Expense: {$expense->description} ({$expense->category}) - $" . number_format($expense->amount, 2),
                    'date' => $expense->expense_date,
                    'link' => route('expenses.index') // Link to expense list
                ];
            });

        // Combine all recent activities and sort by date
        $recentActivitiesCollection = collect()
            ->concat($recentSales)
            ->concat($recentReceivedPhones)
            ->concat($recentInstallmentPayments)
            ->concat($recentExpenses)
            ->sortByDesc('date')
            ->values(); // important to reindex keys

// Paginate the collection manually
        $page = request()->get('page', 1);
        $perPage = 5;
        $offset = ($page - 1) * $perPage;

        $recentActivities = new LengthAwarePaginator(
            $recentActivitiesCollection->slice($offset, $perPage),
            $recentActivitiesCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        return view('dashboard', compact(
            'totalPhones',
            'monthlySales',
            'pendingInstallmentsAmount',
            'profitMarginPercentage',
            'salesChartLabels',
            'salesChartData',
            'inventoryChartLabels',
            'inventoryChartData',
            'lowStockProducts',
            'notificationCount',
            'totalYearlySales', // New stat
            'totalSoldPhones', // New stat
            'totalActiveInstallments', // New stat
            'averageSellingPrice', // New stat
            'recentActivities', // New dynamic activity list
            'totalMonthlyExpenses' // NEW: Pass total monthly expenses to dashboard
        ));
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', ''); // Provide default empty string
        $endDate = $request->input('end_date', '');   // Provide default empty string

        $salesQuery = Sale::activeTransaction();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
        }

        $sales = $salesQuery->with('saleItems.phone.brand', 'saleItems.product')
            ->orderBy('sale_date', 'desc')
            ->paginate(10);

        // Calculate summary statistics
        // Re-run the query for aggregates to ensure they reflect the filtered results
        $filteredSalesForSummary = Sale::activeTransaction();
        if ($startDate) {
            $filteredSalesForSummary->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $filteredSalesForSummary->whereDate('sale_date', '<=', $endDate);
        }

        $totalSalesAmount = $filteredSalesForSummary->sum('final_amount');
        $totalDiscountAmount = $filteredSalesForSummary->sum('discount_amount');
        $totalInstallmentSales = $filteredSalesForSummary->clone()->where('is_installment', true)->count();
        $totalFullPaymentSales = $filteredSalesForSummary->clone()->where('is_installment', false)->count();

        return view('reports.sales', compact('sales', 'totalSalesAmount', 'totalDiscountAmount', 'totalInstallmentSales', 'totalFullPaymentSales', 'startDate', 'endDate'));
    }

    /**
     * Display a stock report.
     *
     * @return \Illuminate\View\View
     */
    public function stockReport()
    {
        $stockLevels = StockLevel::with('brand')->orderBy('current_stock', 'asc')->paginate(10);
        $accessoryStocks = Product::withSum(['accessoryStocks as current_stock' => function ($query) {
            $query->where('status', 'available');
        }], 'quantity')
            ->withMax(['accessoryStocks as low_stock_threshold' => function ($query) {
                $query->where('status', 'available');
            }], 'low_stock_threshold')
            ->withMax(['accessoryStocks as selling_price' => function ($query) {
                $query->where('status', 'available');
            }], 'selling_price')
            ->orderBy('name')
            ->get();

        // Calculate summary statistics for stock
        $totalStockItems = StockLevel::sum('current_stock') + $accessoryStocks->sum(fn ($item) => (int) $item->current_stock);
        $lowAccessoryCount = $accessoryStocks->filter(fn ($item) => (int) $item->current_stock <= (int) ($item->low_stock_threshold ?? 5))->count();
        $lowStockCount = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')->count() + $lowAccessoryCount;

        return view('reports.stock', compact('stockLevels', 'accessoryStocks', 'totalStockItems', 'lowStockCount'));
    }

    /**
     * Display a profit and loss report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function profitLossReport(Request $request)
    {
        $startDate = $request->input('start_date', ''); // Provide default empty string
        $endDate = $request->input('end_date', '');   // Provide default empty string

        $salesQuery = Sale::activeTransaction();
        $expensesQuery = Expense::query(); // NEW: Query for expenses

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate); // Filter expenses
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate); // Filter expenses
        }

        // Eager load sale items and their associated phones to get purchase prices
        $sales = $salesQuery->with('saleItems.phone', 'saleItems.product')->get();
        $expenses = $expensesQuery->get(); // NEW: Get filtered expenses

        $totalRevenue = $sales->sum('final_amount');
        $totalCostOfGoodsSold = 0;

        foreach ($sales as $sale) {
            $totalCostOfGoodsSold += $this->saleItemsCostValue($sale->saleItems);
        }

        $totalExpenses = $expenses->sum('amount'); // NEW: Sum of all filtered expenses
        $accessoryItems = $sales->flatMap->saleItems->filter(fn ($item) => $item->product_id);
        $accessoryRevenue = $accessoryItems->sum(fn ($item) => (float) $item->unit_price * (int) ($item->quantity ?? 1));
        $accessoryCostOfGoodsSold = $this->saleItemsCostValue($accessoryItems);
        $accessoryGrossProfit = $accessoryRevenue - $accessoryCostOfGoodsSold;

        $grossProfit = $totalRevenue - $totalCostOfGoodsSold;
        $netProfit = $grossProfit - $totalExpenses;

        $grossProfitMarginPercentage = 0;
        if ($totalRevenue > 0) {
            $grossProfitMarginPercentage = ($grossProfit / $totalRevenue) * 100;
        }

        return view('reports.profit_loss', compact(
            'totalRevenue',
            'totalCostOfGoodsSold',
            'grossProfit',
            'grossProfitMarginPercentage',
            'startDate',
            'endDate',
            'totalExpenses',
            'netProfit',
            'accessoryRevenue',
            'accessoryCostOfGoodsSold',
            'accessoryGrossProfit'
        ));
    }
    private function buildGeneralReportData(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date',   Carbon::now()->toDateString());

        // ── Revenue & Sales ───────────────────────────────────────────────
        $salesQuery = Sale::activeTransaction()
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $totalRevenue         = (clone $salesQuery)->sum('final_amount');
        $totalDiscounts       = (clone $salesQuery)->sum('discount_amount');
        $totalSalesCount      = (clone $salesQuery)->count();
        $installmentSales     = (clone $salesQuery)->where('is_installment', true)->count();
        $fullPaymentSales     = (clone $salesQuery)->where('is_installment', false)->count();

        // ── Cost of Goods Sold ────────────────────────────────────────────
        $soldItems = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })->with('phone', 'product')->get();

        $totalCogs = $this->saleItemsCostValue($soldItems);
        $accessoryRevenue = $soldItems
            ->whereNotNull('product_id')
            ->sum(fn ($item) => (float) $item->unit_price * (int) ($item->quantity ?? 1));
        $accessoryCogs = $this->saleItemsCostValue($soldItems->whereNotNull('product_id'));
        $accessoryGrossProfit = $accessoryRevenue - $accessoryCogs;

        // ── Expenses ──────────────────────────────────────────────────────
        $expensesQuery = Expense::query()
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);

        $totalExpenses        = (clone $expensesQuery)->sum('amount');
        $expensesByCategory   = (clone $expensesQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ── Profit ────────────────────────────────────────────────────────
        $grossProfit          = $totalRevenue - $totalCogs;
        $netProfit            = $grossProfit - $totalExpenses;
        $profitMargin         = $totalRevenue > 0
            ? round(($netProfit / $totalRevenue) * 100, 2)
            : 0;

        // ── Inventory ─────────────────────────────────────────────────────
        $availablePhones      = Phone::where('status', 'available')->count();
        $availableAccessories = (int) AccessoryStock::where('status', 'available')->sum('quantity');
        $soldPhones           = Phone::whereIn('status', ['sold', 'under_installment'])->count();
        $inventoryValue       = $this->inventoryCostValue();

        $stockByBrand = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('count(*) as count'), DB::raw('SUM(purchase_price) as value'))
            ->with('brand')
            ->groupBy('brand_id')
            ->get();

        $lowStockItems = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        // ── Installments ──────────────────────────────────────────────────
        $activePlans          = InstallmentPlan::where('status', 'active')
            ->whereHas('sale', fn ($query) => $query->activeTransaction())
            ->with(['sale.saleReceipt', 'installmentPayments'])
            ->get();

        $pendingInstallments  = 0;
        foreach ($activePlans as $plan) {
            $receiptPaid = optional(optional($plan->sale)->saleReceipt)->paid_amount ?? 0;
            $paid = $plan->installmentPayments->sum('amount_paid') + $receiptPaid;
            $remaining = optional($plan->sale)->final_amount - $paid;
            if ($remaining > 0) {
                $pendingInstallments += $remaining;
            }
        }
        $activeInstallmentCount = $activePlans->count();

        // ── Daily Sales Trend (for chart in blade view) ───────────────────
        $dailySales = Sale::activeTransaction()
            ->select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Top Selling Brands ────────────────────────────────────────────
        $topBrands = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })
            ->leftJoin('phones', 'sale_items.phone_id', '=', 'phones.id')
            ->leftJoin('brands', 'phones.brand_id', '=', 'brands.id')
            ->select(
                DB::raw("COALESCE(brands.name, 'Unknown') as brand_name"),
                DB::raw('COUNT(*) as units_sold'),
                DB::raw('SUM(sale_items.unit_price) as revenue')
            )
            ->groupBy(DB::raw("COALESCE(brands.name, 'Unknown')"))
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        // ── Recent Sales ──────────────────────────────────────────────────
        $recentSales = Sale::activeTransaction()
            ->with('saleItems.phone.brand', 'saleItems.product')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->orderByDesc('sale_date')
            ->limit(10)
            ->get();

        return compact(
            'startDate', 'endDate',
            'totalRevenue', 'totalDiscounts', 'totalSalesCount', 'installmentSales', 'fullPaymentSales',
            'totalCogs', 'totalExpenses', 'expensesByCategory',
            'accessoryRevenue', 'accessoryCogs', 'accessoryGrossProfit',
            'grossProfit', 'netProfit', 'profitMargin',
            'availablePhones', 'availableAccessories', 'soldPhones', 'inventoryValue', 'stockByBrand', 'lowStockItems',
            'pendingInstallments', 'activeInstallmentCount',
            'dailySales', 'topBrands', 'recentSales'
        );
    }

    private function inventoryCostValue(): float
    {
        $phoneValue = (float) Phone::where('status', 'available')->sum('purchase_price');
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

    /**
     * Show the general report in the browser.
     */
    public function generalReport(Request $request)
    {
        $data = $this->buildGeneralReportData($request);
        return view('reports.general', $data);
    }

    /**
     * Download the general report as a PDF.
     */
    public function downloadGeneralReport(Request $request)
    {
        $data = $this->buildGeneralReportData($request);

        $pdf = Pdf::loadView('reports.general_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'  => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $filename = 'general-report-' . $data['startDate'] . '-to-' . $data['endDate'] . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Send the general report PDF to the admin email.
     */
    public function sendGeneralReportEmail(Request $request)
    {
        $data = $this->buildGeneralReportData($request);

        $pdf = Pdf::loadView('reports.general_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'  => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $pdfContent = $pdf->output();
        $filename   = 'general-report-' . $data['startDate'] . '-to-' . $data['endDate'] . '.pdf';
        $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'admin@example.com'));

        Mail::to($adminEmail)->send(new GeneralReportMail($data, $pdfContent, $filename));

        return back()->with('success', "General report has been emailed to {$adminEmail} successfully.");
    }

    /**
     * Get sales data for DataTable AJAX
     */
    /**
     * Get sales data for DataTable AJAX
     */
    public function getSalesData(Request $request)
    {
        try {
            // Debug: Log the incoming request
            \Log::info('DataTables Request:', $request->all());

            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Validate and set default dates if empty or invalid
            if (empty($startDate) || !strtotime($startDate)) {
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                \Log::info('Using default start_date: ' . $startDate);
            }

            if (empty($endDate) || !strtotime($endDate)) {
                $endDate = Carbon::now()->format('Y-m-d');
                \Log::info('Using default end_date: ' . $endDate);
            }

            // Debug: Log the dates being used
            \Log::info('Query with dates:', ['start' => $startDate, 'end' => $endDate]);

            // Build the query with proper date handling
            $sales = Sale::activeTransaction()
                ->with(['saleItems.phone.brand'])
                ->where('sale_date', '>=', $startDate . ' 00:00:00')
                ->where('sale_date', '<=', $endDate . ' 23:59:59')
                ->orderBy('sale_date', 'desc');

            // Debug: Check if query has any results
            $count = $sales->count();
            \Log::info('Total records found: ' . $count);

            // If using Yajra DataTables
            if (class_exists(\Yajra\DataTables\DataTables::class)) {
                return \Yajra\DataTables\DataTables::of($sales)
                    ->addColumn('phones_sold', function($sale) {
                        $phones = [];
                        foreach ($sale->saleItems as $item) {
                            if ($item->phone && $item->phone->brand) {
                                $phones[] = e($item->phone->brand->name) . ' ' . e($item->phone->model);
                            }
                        }
                        return implode('<br>', $phones);
                    })
                    ->addColumn('final_amount', function($sale) {
                        return '$' . number_format($sale->final_amount, 2);
                    })
                    ->addColumn('discount_amount', function($sale) {
                        return '$' . number_format($sale->discount_amount, 2);
                    })
                    ->addColumn('sale_date', function($sale) {
                        return $sale->sale_date instanceof Carbon ? $sale->sale_date->format('Y-m-d H:i') : date('Y-m-d H:i', strtotime($sale->sale_date));
                    })
                    ->addColumn('type', function($sale) {
                        $badgeClass = $sale->is_installment ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800';
                        $typeText = $sale->is_installment ? 'Installment' : 'Full Payment';
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $badgeClass . '">' . $typeText . '</span>';
                    })
                    ->rawColumns(['phones_sold', 'type'])
                    ->make(true);
            }

            // Fallback
            $perPage = $request->get('length', 10);
            $page = ($request->get('start', 0) / $perPage) + 1;
            $searchValue = $request->get('search')['value'] ?? '';

            if (!empty($searchValue)) {
                $sales->where(function($q) use ($searchValue) {
                    $q->where('customer_name', 'like', "%{$searchValue}%")
                        ->orWhere('id', 'like', "%{$searchValue}%");
                });
            }

            $totalRecords = $sales->count();
            $salesData = $sales->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $salesData->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'customer_name' => $sale->customer_name,
                        'phones_sold' => implode(', ', $sale->saleItems->map(function($item) {
                            return optional($item->phone)->brand->name . ' ' . optional($item->phone)->model;
                        })->toArray()),
                        'final_amount' => '$' . number_format($sale->final_amount, 2),
                        'discount_amount' => '$' . number_format($sale->discount_amount, 2),
                        'sale_date' => $sale->sale_date instanceof Carbon ? $sale->sale_date->format('Y-m-d H:i') : date('Y-m-d H:i', strtotime($sale->sale_date)),
                        'type' => $sale->is_installment ? 'Installment' : 'Full Payment'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('DataTables Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage(),
                'draw' => intval($request->get('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ], 200); // Return 200 but with error to show in DataTables
        }
    }

    /**
     * Get sales summary for AJAX
     */
    /**
     * Get sales summary for AJAX
     */
    public function getSalesSummary(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            // Use separate queries to avoid ambiguity
            $totalSalesAmount = Sale::activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->sum('final_amount');

            $totalDiscountAmount = Sale::activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->sum('discount_amount');

            $totalInstallmentSales = Sale::activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->where('is_installment', true)
                ->count();

            $totalFullPaymentSales = Sale::activeTransaction()
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->where('is_installment', false)
                ->count();

            return response()->json([
                'totalSalesAmount' => number_format($totalSalesAmount, 2),
                'totalDiscountAmount' => number_format($totalDiscountAmount, 2),
                'totalInstallmentSales' => $totalInstallmentSales,
                'totalFullPaymentSales' => $totalFullPaymentSales,
            ]);

        } catch (\Exception $e) {
            \Log::error('Summary Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'totalSalesAmount' => '0.00',
                'totalDiscountAmount' => '0.00',
                'totalInstallmentSales' => 0,
                'totalFullPaymentSales' => 0,
            ], 500);
        }
    }
}
