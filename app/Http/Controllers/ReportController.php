<?php



namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use App\Models\Phone;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view sales reports')->only('salesReport');
        $this->middleware('permission:view stock reports')->only('stockReport');
        $this->middleware('permission:view profit loss reports')->only('profitLossReport');
        $this->middleware('permission:view dashboard')->only('home');
    }


    public function home()
    {

        $totalPhones = Phone::where('status', 'available')->count();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlySales = Sale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('final_amount');
        $pendingInstallmentsAmount = 0;
        $activeInstallmentPlans = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        foreach ($activeInstallmentPlans as $plan) {
            $totalPaid = $plan->installmentPayments->sum('amount_paid');
            $remainingAmount = $plan->sale->final_amount - $totalPaid;
            if ($remainingAmount > 0) {
                $pendingInstallmentsAmount += $remainingAmount;
            }
        }
        $totalRevenueThisMonth = $monthlySales;

        $totalCogsThisMonth = 0;
        $soldPhonesThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })
            ->with('phone')
            ->get();

        foreach ($soldPhonesThisMonth as $saleItem) {
            if ($saleItem->phone) {
                $totalCogsThisMonth += $saleItem->phone->purchase_price;
            }
        }
        $totalMonthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        $grossProfit = $totalRevenueThisMonth - $totalCogsThisMonth - $totalMonthlyExpenses;
        $profitMarginPercentage = 0;
        if ($totalRevenueThisMonth > 0) {
            $profitMarginPercentage = ($grossProfit / $totalRevenueThisMonth) * 100;
        }
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
        $inventoryDistribution = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('count(*) as count'))
            ->with('brand')
            ->groupBy('brand_id')
            ->get();

        $inventoryChartLabels = $inventoryDistribution->pluck('brand.name')->toArray();
        $inventoryChartData = $inventoryDistribution->pluck('count')->toArray();

        $lowStockProducts = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();
        $notificationCount = $lowStockProducts->count();
        $totalYearlySales = Sale::whereYear('sale_date', $currentYear)->sum('final_amount');
        $totalSoldPhones = Phone::where('status', 'sold')->count();
        $totalActiveInstallments = InstallmentPlan::where('status', 'active')->count();
        $averageSellingPrice = Phone::avg('selling_price');
        $recentSales = Sale::with('saleItems.phone.brand')
            ->latest('sale_date')
            ->take(5)
            ->get()
            ->map(function($sale) {
                $phoneNames = $sale->saleItems->map(fn($item) => $item->phone->brand->name . ' ' . $item->phone->model)->implode(', ');
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
                    'description' => "📦 1 {$phone->brand->name} {$phone->model} ({$phone->color}) received into inventory (IMEI: {$phone->imei})",
                    'date' => $phone->received_at,
                    'link' => route('phones.index')
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
                    $phoneName = $firstPhone->brand->name . ' ' . $firstPhone->model;
                }
                return [
                    'type' => 'payment',
                    'description' => "💵 Installment payment received for {$phoneName} - $" . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => route('sales.show', $payment->installmentPlan->sale->id)
                ];
            });


        $recentExpenses = Expense::latest('expense_date')
            ->take(5)
            ->get()
            ->map(function($expense) {
                return [
                    'type' => 'expense',
                    'description' => "💸 Expense: {$expense->description} ({$expense->category}) - $" . number_format($expense->amount, 2),
                    'date' => $expense->expense_date,
                    'link' => route('expenses.index')
                ];
            });


        $recentActivitiesCollection = collect()
            ->concat($recentSales)
            ->concat($recentReceivedPhones)
            ->concat($recentInstallmentPayments)
            ->concat($recentExpenses)
            ->sortByDesc('date')
            ->values();


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
            'totalYearlySales',
            'totalSoldPhones',
            'totalActiveInstallments',
            'averageSellingPrice',
            'recentActivities',
            'totalMonthlyExpenses'
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

        $sales = $salesQuery->with('saleItems.phone.brand')
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
    public function stockReport()
    {
        $stockLevels = StockLevel::with('brand')->orderBy('current_stock', 'asc')->paginate(10);

        $totalStockItems = StockLevel::sum('current_stock');
        $lowStockCount = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')->count();

        return view('reports.stock', compact('stockLevels', 'totalStockItems', 'lowStockCount'));
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

        // Eager load sale items and their associated phones to get purchase prices
        $sales = $salesQuery->with('saleItems.phone')->get();
        $expenses = $expensesQuery->get(); // NEW: Get filtered expenses

        $totalRevenue = $sales->sum('final_amount');
        $totalCostOfGoodsSold = 0;

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $saleItem) {
                // Ensure the phone relationship exists before accessing purchase_price
                if ($saleItem->phone) {
                    $totalCostOfGoodsSold += $saleItem->phone->purchase_price;
                }
            }
        }

        $totalExpenses = $expenses->sum('amount'); // NEW: Sum of all filtered expenses

        $grossProfit = $totalRevenue - $totalCostOfGoodsSold - $totalExpenses; // Deduct total expenses

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
            'totalExpenses' // NEW: Pass total expenses to the report
        ));
    }
}
