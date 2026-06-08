<?php



namespace App\Http\Controllers;

use App\Models\Cashew;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockAdjustment;
use App\Models\StockLevel;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
 use Barryvdh\DomPDF\Facade\Pdf;
 use Illuminate\Support\Facades\Mail;
 use App\Mail\GeneralReportMail;
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

    public function index()
    {
        return view('reports.index');
    }


    public function home()
    {
        // 1. Total Phones (Available in Stock)
        $totalPhones = Cashew::where('status', 'available')->count();

        // 2. Monthly Sales (Current Month's Revenue)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlySales = Sale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('final_amount');

        // 3. Pending Installments Amount
        $pendingInstallmentsAmount = 0;
        $activeInstallmentPlans = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        foreach ($activeInstallmentPlans as $plan) {
            $totalPaid = $plan->installmentPayments->sum('amount_paid');
            $remainingAmount = (optional($plan->sale)->final_amount ?? 0) - $totalPaid;
            if ($remainingAmount > 0) {
                $pendingInstallmentsAmount += $remainingAmount;
            }
        }

        // 4. Profit Margin (Current Month)
        $totalRevenueThisMonth = $monthlySales; // Already calculated

        $totalCogsThisMonth = 0;

        $soldCashewsThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })
            ->with('cashew')
            ->get();

        foreach ($soldCashewsThisMonth as $saleItem) {
            if ($saleItem->cashew) {
                $totalCogsThisMonth += $saleItem->cashew->unit_price * $saleItem->quantity;
            }
        }

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
        $salesData = Sale::select(
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
        $inventoryDistribution = Cashew::select('product_id', DB::raw('count(*) as count'))
            ->with('product')
            ->groupBy('product_id')
            ->get();

        $inventoryChartLabels = $inventoryDistribution->pluck('products.name')->toArray();
        $inventoryChartData = $inventoryDistribution->pluck('count')->toArray();

        // 7. Low Stock Products
        $lowStockProducts = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('product')
            ->get();

        // Count for notifications (e.g., low stock items)
        $notificationCount = $lowStockProducts->count();

        // New: Total Sales for the Year
        $totalYearlySales = Sale::whereYear('sale_date', $currentYear)->sum('final_amount');

        // New: Total Sold Phones
        $totalSoldPhones = Cashew::where('status', 'sold')->count();

        // New: Total Active Installment Plans
        $totalActiveInstallments = InstallmentPlan::where('status', 'active')->count();

        // New: Average Selling Price of ALL cashews (could be refined to average *sold* price)
        $averageSellingPrice = Cashew::avg('selling_price');

        // New: Recent Activities (combining sales, received, payments, expenses)
        $recentSales = Sale::with('saleItems.cashew.product')
            ->latest('sale_date')
            ->take(5)
            ->get()
            ->map(function($sale) {
                $cashewNames = $sale->saleItems
                    ->map(fn ($item) => optional($item->product)->name ?? optional(optional($item->cashews)->product)->name)
                    ->filter()
                    ->implode(', ');

                return [
                    'type' => 'sale',
                    'description' => "{$cashewNames} sold to {$sale->customer_name} - Tsh " . number_format($sale->final_amount, 2),
                    'date' => $sale->sale_date,
                    'link' => route('sales.show', $sale->id)
                ];
            });

        $recentReceivedPhones = Cashew::with('product')
            ->latest('received_at')
            ->take(5)
            ->get()
            ->map(function($cashew) {
                return [
                    'date' => $cashew->received_at,
                    'link' => route('cashews.index')
                ];
            });

        $recentInstallmentPayments = InstallmentPayment::with('installmentPlan.sale.saleItems.product')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function($payment) {
                $phoneName = 'N/A';
                if ($payment->installmentPlan && $payment->installmentPlan->sale && $payment->installmentPlan->sale->saleItems->isNotEmpty()) {
                    $firstItem = $payment->installmentPlan->sale->saleItems->first();
                    $phoneName = optional($firstItem->product)->name
                        ?? optional(optional($firstItem->cashews)->product)->name
                        ?? 'N/A';
                }
                return [
                    'type' => 'payment',
                    'description' => "Installment payment received for {$phoneName} - Tsh " . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => $payment->installmentPlan?->sale
                        ? route('sales.show', $payment->installmentPlan->sale->id)
                        : route('installments.index')
                ];
            });

        // NEW: Recent Expenses
        $recentExpenses = Expense::latest('expense_date')
            ->take(5)
            ->get()
            ->map(function($expense) {
                return [
                    'type' => 'expense',
                    'description' => "Expense: {$expense->description} ({$expense->category}) - Tsh " . number_format($expense->amount, 2),
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
        $product_count =Cashew::all()->count();


        return view('dashboard', compact(
            'product_count',
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

        $salesQuery = Sale::query();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
        }

        if ($request->boolean('download')) {
            $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.csv';
            $salesForExport = (clone $salesQuery)->with(['saleItems.product', 'saleItems.cashews.product'])->orderBy('sale_date', 'desc')->get();

            return response()->streamDownload(function () use ($salesForExport) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Sale ID', 'Date', 'Customer', 'Item', 'Quantity', 'Cost', 'Selling Price', 'Profit', 'Payment Type', 'Payment Option']);

                foreach ($salesForExport as $sale) {
                    foreach ($sale->saleItems as $item) {
                        $quantity = (int) $item->quantity;
                        $unitCost = (float) ($item->unit_cost ?? $item->cashews?->unit_price ?? 0);
                        $unitPrice = (float) ($item->unit_price ?? $item->cashews?->selling_price ?? 0);

                        fputcsv($handle, [
                            $sale->id,
                            optional($sale->sale_date)->format('Y-m-d'),
                            $sale->customer_name,
                            $item->product->name ?? $item->cashews?->product?->name ?? 'N/A',
                            $quantity,
                            number_format($unitCost * $quantity, 2, '.', ''),
                            number_format($unitPrice * $quantity, 2, '.', ''),
                            number_format(($unitPrice - $unitCost) * $quantity, 2, '.', ''),
                            $sale->is_installment ? 'Installment' : 'Full Payment',
                            $sale->payment_option,
                        ]);
                    }
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $sales = $salesQuery->with(['saleItems.product', 'saleItems.cashews.product'])
            ->orderBy('sale_date', 'desc')
            ->paginate(10);

        // Calculate summary statistics
        // Re-run the query for aggregates to ensure they reflect the filtered results
        $filteredSalesForSummary = Sale::query();
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
//    public function stockReport()
//    {
//
//        $stockLevels = StockLevel::with('brand')->orderBy('current_stock', 'asc')->paginate(10);
//
//        // Calculate summary statistics for stock
//        $totalStockItems = StockLevel::sum('current_stock');
//        $lowStockCount = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')->count();
//
//        return view('reports.stock', compact('stockLevels', 'totalStockItems', 'lowStockCount'));
//    }

    public function stockReport(Request $request)
    {
        $productId = $request->input('product_id');

        // For the filter dropdown
        $products = Product::orderBy('name')->get();

        // Medicine stock query
        $medicineQuery = Cashew::with('product'); // adjust model/relationship as needed
        if ($productId) {
            $medicineQuery->where('product_id', $productId);
        }
        $medicineStock = $medicineQuery->get();

        if ($request->boolean('download')) {
            $filename = 'stock-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($medicineStock) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'Product',
                    'Quantity',
                    'Cost Value',
                    'Selling Price',
                    'Profit',
                    'Profit %',
                ]);

                foreach ($medicineStock as $stock) {
                    $costValue = $stock->unit_price * $stock->quantity;
                    $sellingValue = $stock->selling_price * $stock->quantity;
                    $profit = $sellingValue - $costValue;
                    $profitPercent = $stock->unit_price > 0
                        ? (($stock->selling_price - $stock->unit_price) / $stock->unit_price) * 100
                        : 0;

                    fputcsv($handle, [
                        $stock->product->name ?? 'N/A',
                        $stock->quantity,
                        number_format($costValue, 2, '.', ''),
                        number_format($sellingValue, 2, '.', ''),
                        number_format($profit, 2, '.', ''),
                        number_format($profitPercent, 1, '.', '') . '%',
                    ]);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        // Summary statistics from the same stock source used by the table.
        $totalStockItems = $medicineStock->sum(fn ($stock) => (int) $stock->quantity);
        $totalStockValue = $medicineStock->sum(fn ($stock) => (float) $stock->unit_price * (int) $stock->quantity);
        $lowStockCount = $medicineStock
            ->filter(fn ($stock) => (int) $stock->quantity <= (int) $stock->low_stock_threshold)
            ->count();
        $totalMedicineItems = $medicineStock->count();

        return view('reports.stock', compact(
            'products',
            'medicineStock',
            'totalStockItems',
            'totalStockValue',
            'lowStockCount',
            'totalMedicineItems',
        ));
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

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query(); // NEW: Query for expenses

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate); // Filter expenses
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate); // Filter expenses
        }

        // Eager load sale items and their associated cashews to get purchase prices
        $sales = $salesQuery->with('saleItems.cashews')->get();;
        $expenses = $expensesQuery->get(); // NEW: Get filtered expenses

        $totalRevenue = $sales->sum('final_amount');
        $totalCostOfGoodsSold = 0;

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $saleItem) {
                if ($saleItem->cashews) {
                    $unitCost = $saleItem->unit_cost !== null
                        ? (float) $saleItem->unit_cost
                        : (float) $saleItem->cashews->unit_price;
                    $totalCostOfGoodsSold += $unitCost * $saleItem->quantity;
                }
            }
        }

        $totalExpenses = $expenses->sum('amount'); // NEW: Sum of all filtered expenses

        $grossProfit = $totalRevenue - $totalCostOfGoodsSold;

        $grossProfitMarginPercentage = 0;
        if ($totalRevenue > 0) {
            $grossProfitMarginPercentage = ($grossProfit / $totalRevenue) * 100;
        }
        $netProfit = $grossProfit - $totalExpenses;

        if ($request->boolean('download')) {
            $filename = 'profit-loss-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use (
                $sales,
                $expenses,
                $totalRevenue,
                $totalCostOfGoodsSold,
                $grossProfit,
                $totalExpenses,
                $netProfit
            ) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, ['Summary']);
                fputcsv($handle, ['Revenue', number_format($totalRevenue, 2, '.', '')]);
                fputcsv($handle, ['COGS', number_format($totalCostOfGoodsSold, 2, '.', '')]);
                fputcsv($handle, ['Gross Profit', number_format($grossProfit, 2, '.', '')]);
                fputcsv($handle, ['Expenses', number_format($totalExpenses, 2, '.', '')]);
                fputcsv($handle, ['Net Profit', number_format($netProfit, 2, '.', '')]);
                fputcsv($handle, []);

                fputcsv($handle, ['Sales Transactions']);
                fputcsv($handle, ['Date', 'Customer', 'Amount']);
                foreach ($sales as $sale) {
                    fputcsv($handle, [
                        optional($sale->sale_date)->format('Y-m-d'),
                        $sale->customer_name,
                        number_format($sale->final_amount, 2, '.', ''),
                    ]);
                }
                fputcsv($handle, []);

                fputcsv($handle, ['Expense Transactions']);
                fputcsv($handle, ['Date', 'Description', 'Category', 'Amount']);
                foreach ($expenses as $expense) {
                    fputcsv($handle, [
                        $expense->expense_date ? Carbon::parse($expense->expense_date)->format('Y-m-d') : null,
                        $expense->description,
                        $expense->category,
                        number_format($expense->amount, 2, '.', ''),
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return view('reports.profit_loss', compact(
            'totalRevenue',
            'totalCostOfGoodsSold',
            'grossProfit',
            'grossProfitMarginPercentage',
            'startDate',
            'endDate',
            'totalExpenses',
        'expenses',
        'sales',
        'netProfit'// NEW: Pass total expenses to the report
        ));
    }

    public function expensesReport()
    {
        $expenses = Expense::orderByDesc('expense_date')->paginate(10);
        $totalExpenses = Expense::sum('amount');

        return view('reports.expenses', compact('expenses', 'totalExpenses'));
    }

    public function installmentsReport(Request $request)
    {
        $status = $request->input('status');

        $plansQuery = InstallmentPlan::with(['sale.saleItems.product', 'sale.saleItems.cashews.product', 'installmentPayments'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at');

        $allPlans = (clone $plansQuery)->get();

        $totalPlans = $allPlans->count();
        $totalCollectedAmount = $allPlans->sum(fn ($plan) => $plan->installmentPayments->sum('amount_paid'));
        $totalPendingAmount = $allPlans->sum(function ($plan) {
            $totalPaid = $plan->installmentPayments->sum('amount_paid');
            return max((optional($plan->sale)->final_amount ?? 0) - $totalPaid, 0);
        });
        $totalPlansCompleted = $allPlans->whereIn('status', ['paid', 'completed'])->count();
        $totalPlansDefaulted = $allPlans->where('status', 'defaulted')->count();

        if ($request->boolean('download')) {
            $filename = 'installments-report-' . now()->format('Y-m-d-His') . '.csv';

            return response()->streamDownload(function () use ($allPlans) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Customer', 'Total Amount', 'Paid Amount', 'Remaining', 'Last Payment', 'Status']);

                foreach ($allPlans as $plan) {
                    $totalPaid = $plan->installmentPayments->sum('amount_paid');
                    $remaining = max((optional($plan->sale)->final_amount ?? 0) - $totalPaid, 0);
                    $lastPayment = $plan->installmentPayments->sortByDesc('payment_date')->first();

                    fputcsv($handle, [
                        optional($plan->sale)->customer_name ?? 'N/A',
                        number_format(optional($plan->sale)->final_amount ?? 0, 2, '.', ''),
                        number_format($totalPaid, 2, '.', ''),
                        number_format($remaining, 2, '.', ''),
                        optional(optional($lastPayment)->payment_date)->format('Y-m-d') ?? 'N/A',
                        $plan->status,
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $installmentPlans = $plansQuery->paginate(10)->withQueryString();

        return view('reports.installments', compact(
            'installmentPlans',
            'totalPlans',
            'totalCollectedAmount',
            'totalPendingAmount',
            'totalPlansCompleted',
            'totalPlansDefaulted'
        ));
    }

    public function usersReport()
    {
        $recentSales = Sale::with('saleItems.product')
            ->latest('sale_date')
            ->take(25)
            ->get()
            ->map(fn ($sale) => [
                'type' => 'sale',
                'description' => 'Sale #' . $sale->id . ' recorded for ' . ($sale->customer_name ?: 'Walk-in customer'),
                'date' => $sale->sale_date,
            ]);

        $recentPayments = InstallmentPayment::with('installmentPlan.sale')
            ->latest('payment_date')
            ->take(25)
            ->get()
            ->map(fn ($payment) => [
                'type' => 'payment',
                'description' => 'Installment payment of Tsh ' . number_format($payment->amount_paid, 2) . ' recorded',
                'date' => $payment->payment_date,
            ]);

        $recentExpenses = Expense::latest('expense_date')
            ->take(25)
            ->get()
            ->map(fn ($expense) => [
                'type' => 'expense',
                'description' => $expense->description,
                'date' => $expense->expense_date,
            ]);

        $allActivitiesCollection = collect()
            ->concat($recentSales)
            ->concat($recentPayments)
            ->concat($recentExpenses)
            ->sortByDesc('date')
            ->values();

        return view('reports.users', compact('allActivitiesCollection'));
    }

    public function creditSaleReport()
    {
        $creditSales = Sale::where('amount_due', '>', 0)
            ->where('is_installment', false)
            ->orderByDesc('sale_date')
            ->get();

        return view('reports.credit_sale', compact('creditSales'));
    }

    public function customersReport()
    {
        $sales = Sale::with(['saleItems.product', 'saleItems.cashews.product'])
            ->whereNotNull('customer_name')
            ->orderByDesc('sale_date')
            ->get();

        $customers = $sales
            ->groupBy(fn ($sale) => $sale->customer_name . '|' . ($sale->customer_phone ?? '') . '|' . ($sale->customer_email ?? ''))
            ->map(function ($customerSales) {
                $first = $customerSales->first();

                return (object) [
                    'customer_name' => $first->customer_name,
                    'customer_phone' => $first->customer_phone,
                    'customer_email' => $first->customer_email,
                    'sales' => $customerSales->map(fn ($sale) => [
                        'id' => $sale->id,
                        'date' => optional($sale->sale_date)->format('Y-m-d'),
                        'cashews' => $sale->saleItems
                            ->map(fn ($item) => optional($item->product)->name ?? optional(optional($item->cashews)->product)->name)
                            ->filter()
                            ->values()
                            ->all(),
                        'accessories' => [],
                    ])->values()->all(),
                ];
            })
            ->values();

        return view('reports.customers', compact('customers'));
    }

    public function stockAdjustmentReport()
    {
        $stock_adjustment = StockAdjustment::with(['cashew.product', 'adjustedBy'])
            ->latest()
            ->get();

        return view('reports.stock_adjustment', compact('stock_adjustment'));
    }
    private function buildGeneralReportData(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date',   Carbon::now()->toDateString());

        // ── Revenue & Sales ───────────────────────────────────────────────
        $salesQuery = Sale::query()
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $totalRevenue         = (clone $salesQuery)->sum('final_amount');
        $totalDiscounts       = (clone $salesQuery)->sum('discount_amount');
        $totalSalesCount      = (clone $salesQuery)->count();
        $installmentSales     = (clone $salesQuery)->where('is_installment', true)->count();
        $fullPaymentSales     = (clone $salesQuery)->where('is_installment', false)->count();

        $productCostMap = Cashew::query()
            ->select(
                'product_id',
                DB::raw('SUM(CAST(quantity AS REAL)) as stock_quantity'),
                DB::raw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as stock_cost')
            )
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(function ($stock) {
                $stockQuantity = (float) $stock->stock_quantity;
                $stockCost = (float) $stock->stock_cost;

                return [
                    $stock->product_id => $stockQuantity > 0 ? $stockCost / $stockQuantity : 0,
                ];
            });

        // ── Cost of Goods Sold ────────────────────────────────────────────
        $soldItems = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })->with('product')->get();

        $totalCogs = $soldItems->sum(function ($item) use ($productCostMap) {
            $quantity = (int) $item->quantity;
            $unitCost = $item->unit_cost !== null
                ? (float) $item->unit_cost
                : (float) ($productCostMap[$item->product_id] ?? 0);

            return $unitCost * $quantity;
        });

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
        $availableStockUnits = (int) Cashew::where('status', 'available')->sum('quantity');
        $soldUnitsInPeriod = (int) $soldItems->sum(fn ($item) => (int) $item->quantity);
        $inventoryValue = Cashew::where('status', 'available')
            ->selectRaw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as total_value')
            ->value('total_value') ?? 0;

        $stockByProduct = Cashew::where('status', 'available')
            ->select(
                'product_id',
                DB::raw('SUM(CAST(quantity AS REAL)) as count'),
                DB::raw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as value')
            )
            ->with('product')
            ->groupBy('product_id')
            ->get();

        $lowStockItems = Cashew::with('product')
            ->where('status', 'available')
            ->whereRaw('CAST(quantity AS INTEGER) <= low_stock_threshold')
            ->get();

        // ── Installments ──────────────────────────────────────────────────
        $activePlans          = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        $pendingInstallments  = 0;
        foreach ($activePlans as $plan) {
            $paid = $plan->installmentPayments->sum('amount_paid');
            $remaining = optional($plan->sale)->final_amount - $paid;
            if ($remaining > 0) {
                $pendingInstallments += $remaining;
            }
        }
        $activeInstallmentCount = $activePlans->count();

        // ── Daily Sales Trend (for chart in blade view) ───────────────────
        $dailySales = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Top Selling Products ──────────────────────────────────────────
        $topProducts = $soldItems
            ->groupBy('product_id')
            ->map(function ($items) {
                $firstItem = $items->first();

                return (object) [
                    'product_name' => optional($firstItem->product)->name ?? 'Unknown',
                    'units_sold' => (int) $items->sum(fn ($item) => (int) $item->quantity),
                    'revenue' => (float) $items->sum(fn ($item) => (float) $item->unit_price * (int) $item->quantity),
                ];
            })
            ->sortByDesc('units_sold')
            ->take(5)
            ->values();

        // ── Recent Sales ──────────────────────────────────────────────────
        $recentSales = Sale::with('saleItems.product')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->orderByDesc('sale_date')
            ->limit(10)
            ->get();

        $stockAdjustments = StockAdjustment::with('cashew','cashew.product','adjustedBy')
            ->get();

        $availablePhones = $availableStockUnits;
        $soldPhones = $soldUnitsInPeriod;
        $stockByBrand = $stockByProduct;
        $topBrands = $topProducts;

        return compact(
            'startDate', 'endDate',
            'totalRevenue', 'totalDiscounts', 'totalSalesCount', 'installmentSales', 'fullPaymentSales',
            'totalCogs', 'totalExpenses', 'expensesByCategory',
            'grossProfit', 'netProfit', 'profitMargin',
            'availablePhones', 'soldPhones', 'inventoryValue', 'stockByBrand', 'lowStockItems',
            'pendingInstallments', 'activeInstallmentCount',
            'dailySales', 'topBrands', 'recentSales','stockAdjustments'
        );
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
            $sales = Sale::with(['saleItems.product', 'saleItems.cashews.product'])
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
                            $productName = $item->product->name ?? $item->cashews?->product?->name;
                            if ($productName) {
                                $phones[] = e($productName);
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
                            return $item->product->name ?? $item->cashews?->product?->name;
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
            $totalSalesAmount = Sale::whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->sum('final_amount');

            $totalDiscountAmount = Sale::whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->sum('discount_amount');

            $totalInstallmentSales = Sale::whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->where('is_installment', true)
                ->count();

            $totalFullPaymentSales = Sale::whereDate('sale_date', '>=', $startDate)
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

    public function stockUpdate(Request $request)
    {
        try {
            $request->validate([
                'id'           => 'required',
                'new_quantity' => 'required|integer|min:0',
                'comment'      => 'nullable|string|max:500',
            ]);

            $stock = Cashew::findOrFail($request->id);
            $oldQuantity = $stock->quantity;

            $stock->quantity = $request->new_quantity;
            $stock->save();

            StockAdjustment::create([
                'stock_item_id'       => $stock->id,
                'old_quantity'        => $oldQuantity,
                'new_quantity'        => $request->new_quantity,
                'comment'             => $request->comment,
                'adjusted_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => "Stock updated from {$oldQuantity} to {$request->new_quantity} units.",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
