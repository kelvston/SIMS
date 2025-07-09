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
    }


    public function home()
    {
        // 1. Total Phones (Available in Stock)
        $totalPhones = Phone::where('status', 'available')->count();

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
            $remainingAmount = $plan->sale->final_amount - $totalPaid;
            if ($remainingAmount > 0) {
                $pendingInstallmentsAmount += $remainingAmount;
            }
        }

        // 4. Profit Margin (Current Month)
        $totalRevenueThisMonth = $monthlySales; // Already calculated

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
        $totalYearlySales = Sale::whereYear('sale_date', $currentYear)->sum('final_amount');

        // New: Total Sold Phones
        $totalSoldPhones = Phone::where('status', 'sold')->count();

        // New: Total Active Installment Plans
        $totalActiveInstallments = InstallmentPlan::where('status', 'active')->count();

        // New: Average Selling Price of ALL phones (could be refined to average *sold* price)
        $averageSellingPrice = Phone::avg('selling_price');

        // New: Recent Activities (combining sales, received, payments, expenses)
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
                    $phoneName = $firstPhone->brand->name . ' ' . $firstPhone->model;
                }
                return [
                    'type' => 'payment',
                    'description' => "💵 Installment payment received for {$phoneName} - $" . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => route('sales.show', $payment->installmentPlan->sale->id) // Link to the sale details
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
        $recentActivities = collect()
            ->concat($recentSales)
            ->concat($recentReceivedPhones)
            ->concat($recentInstallmentPayments)
            ->concat($recentExpenses) // Add recent expenses
            ->sortByDesc('date')
            ->take(8); // Limit to top 8 recent activities for display


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

        // Calculate summary statistics for stock
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
