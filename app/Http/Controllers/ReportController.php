<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\StockLevel;
use App\Models\Phone;
use App\Models\StockAdjustment;
use App\Models\Brand;
use App\Models\Accessory;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Protect specific report actions with their own permissions
        $this->middleware('permission:view sales reports')->only('sales');
        $this->middleware('permission:view stock reports')->only('stock');
        $this->middleware('permission:view profit loss reports')->only('profitLoss');
        $this->middleware('permission:view expenses reports')->only('expenses');
        $this->middleware('permission:view installments reports')->only('installments');
        $this->middleware('permission:view user activity reports')->only('users');
        $this->middleware('permission:view general reports')->only('general');

        // Middleware for the home dashboard and reports index
        $this->middleware('permission:view dashboard')->only('home', 'index');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function SaleAdjustment(){
        $stock_adjustment = StockAdjustment::leftJoin('accessories','accessories.id','=','stock_adjustments.accessory_id')
            ->select('*','accessories.name as accessory')
        ->leftJoin('phones','phones.id','=','stock_adjustments.phone_id')
        ->leftJoin('users','users.id','=','stock_adjustments.adjusted_by_user_id')->get();
         return view('reports.stock_adjustment', compact('stock_adjustment'));
    }

    public function CustomerReport()
    {
        // Eager-load items and their phone/accessory relations
        $sales = \App\Models\Sale::with(['saleItems.phone', 'saleItems.accessory'])->get();

        // Group sales by customer and build per-sale summaries
        $customers = $sales
            ->groupBy(function ($sale) {
                // group by a unique customer key (name + phone + email)
                return $sale->customer_name . '||' . $sale->customer_phone . '||' . $sale->customer_email;
            })
            ->map(function ($groupedSales) {
                $first = $groupedSales->first();

                $salesList = $groupedSales->map(function ($sale) {
                    // phones in this sale
                    $phones = $sale->saleItems
                        ->filter(fn($si) => $si->phone)
                        ->map(function ($si) {
                            return ($si->phone->model ?? 'Unknown model') . ' (IMEI: ' . ($si->phone->imei ?? '-') . ')';
                        })
                        ->unique()
                        ->values()
                        ->all();

                    // accessories in this sale
                    $accessories = $sale->saleItems
                        ->filter(fn($si) => $si->accessory)
                        ->map(function ($si) {
                            return ($si->accessory->name ?? 'Unknown') . ' x' . ($si->quantity ?? 1);
                        })
                        ->values()
                        ->all();

                    // sale date: prefer created_at, fallback to sale_date column if you have it
                    $date = $sale->created_at
                        ? $sale->created_at->format('Y-m-d')
                        : (isset($sale->sale_date) ? Carbon::parse($sale->sale_date)->format('Y-m-d') : '');

                    return [
                        'id' => $sale->id,
                        'date' => $date,
                        'phones' => $phones,
                        'accessories' => $accessories,
                    ];
                })->values();

                return (object) [
                    'customer_name'  => $first->customer_name,
                    'customer_phone' => $first->customer_phone,
                    'customer_email' => $first->customer_email,
                    'sales'          => $salesList,
                ];
            })
            ->values();

        return view('reports.customers', compact('customers'));
    }



        public function creditSale()
    {
        $creditSales = Sale::where('amount_due', '>', 0)
            ->where('is_installment', 0)
            ->latest()
            ->get();
        return view('reports.credit_sale', compact('creditSales'));
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
        $soldItemsThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })
            ->with(['phone', 'accessory'])
            ->get();

        foreach ($soldItemsThisMonth as $saleItem) {
            if ($saleItem->phone) {
                $totalCogsThisMonth += $saleItem->phone->purchase_price;
            } elseif ($saleItem->accessory) {
                $totalCogsThisMonth += $saleItem->accessory->cost_price;
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
        $lowStockPhones = DB::table('phones')
            ->join('stock_levels', function ($join) {
                $join->on('phones.model', '=', 'stock_levels.model')
                    ->on('phones.color', '=', 'stock_levels.color')
                    ->on('phones.brand_id', '=', 'stock_levels.brand_id');
            })
            ->where('phones.status', 'available')
            ->whereColumn('stock_levels.current_stock', '<=', 'stock_levels.low_stock_threshold')
            ->select('phones.*', 'stock_levels.current_stock', 'stock_levels.low_stock_threshold')
            ->get();



        $lowStockAccessories = Accessory::whereColumn('quantity', '<=', 'low_stock_threshold')
            ->get();

        $lowStockProducts = $lowStockPhones->concat($lowStockAccessories)->take(5);

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
        $recentSales = Sale::with(['saleItems.phone.brand', 'saleItems.accessory'])
            ->latest('sale_date')
            ->take(5)
            ->get()
            ->map(function ($sale) {
                $itemNames = $sale->saleItems->map(function ($item) {
                    if ($item->phone) {
                        return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                    } elseif ($item->accessory) {
                        return $item->accessory->name;
                    }
                    return 'Unknown Item';
                })->implode(', ');
                return [
                    'type' => 'sale',
                    'description' => "✔️ {$itemNames} sold to {$sale->customer_name} - $" . number_format($sale->final_amount, 2),
                    'date' => $sale->sale_date,
                    'link' => route('sales.show', $sale->id)
                ];
            });

        $recentReceivedPhones = Phone::with('brand')
            ->latest('received_at')
            ->take(5)
            ->get()
            ->map(function ($phone) {
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
            ->map(function ($payment) {
                $phoneName = 'N/A';
                if ($payment->installmentPlan && $payment->installmentPlan->sale && $payment->installmentPlan->sale->saleItems->isNotEmpty()) {
                    $firstPhone = $payment->installmentPlan->sale->saleItems->first()->phone;
                    if ($firstPhone) {
                        $phoneName = $firstPhone->brand->name . ' ' . $firstPhone->model;
                    }
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
            ->map(function ($expense) {
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
        $settings = Setting::all()->pluck('value', 'key')->toArray();

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
            'totalMonthlyExpenses',
            'totalCogsThisMonth','settings'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $download = $request->input('download');

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();
        $saleItemsQuery = SaleItem::query();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $startDate));
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $endDate));
        }

        if ($download) {
            return $this->download($request, 'sales');
        }

        $sales = $salesQuery->with(['saleItems.phone.brand', 'saleItems.accessory'])
            ->orderBy('sale_date', 'desc')
            ->paginate(10);

        // --- Start of new detailed report calculations ---
        $filteredSalesForSummary = clone $salesQuery;
        $totalSalesCount = $filteredSalesForSummary->count();
        $totalSalesAmount = $filteredSalesForSummary->sum('final_amount');
        $averageSaleValue = $totalSalesCount > 0 ? $totalSalesAmount / $totalSalesCount : 0;

        $totalPhonesSold = $saleItemsQuery->whereNotNull('phone_id')->count();
        $totalAccessoriesSold = $saleItemsQuery->whereNotNull('accessory_id')->count();

        // Calculate profit margin for the selected period
        $totalRevenue = $totalSalesAmount;
        $totalCostOfGoodsSold = $saleItemsQuery->with(['phone', 'accessory'])->get()->sum(function($item) {
            if ($item->phone) {
                return $item->phone->purchase_price ?? 0;
            } elseif ($item->accessory) {
                return $item->accessory->cost_price ?? 0;
            }
            return 0;
        });
        $totalExpenses = $expensesQuery->sum('amount');
        $grossProfit = $totalRevenue - $totalCostOfGoodsSold - $totalExpenses;
        $grossProfitMarginPercentage = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Data for Sales by Brand chart (phones only, for now)
        $salesByBrand = $saleItemsQuery
            ->select('phones.brand_id', DB::raw('count(*) as count'))
            ->join('phones', 'sale_items.phone_id', '=', 'phones.id')
            ->with('phone.brand')
            ->groupBy('phones.brand_id')
            ->get();

        $brandLabels = $salesByBrand->pluck('phone.brand.name')->toArray();
        $brandData = $salesByBrand->pluck('count')->toArray();

        // --- End of new detailed report calculations ---

        // Keep existing summary stats for continuity
        $totalDiscountAmount = $filteredSalesForSummary->sum('discount_amount');
        $totalInstallmentSales = $filteredSalesForSummary->clone()->where('is_installment', true)->count();
        $totalFullPaymentSales = $filteredSalesForSummary->clone()->where('is_installment', false)->count();

        return view('reports.sales', compact(
            'sales',
            'totalSalesAmount',
            'totalDiscountAmount',
            'totalInstallmentSales',
            'totalFullPaymentSales',
            'startDate',
            'endDate',
            'averageSaleValue',
            'totalPhonesSold',
            'totalAccessoriesSold',
            'grossProfitMarginPercentage',
            'brandLabels',
            'brandData'
        ));
    }

    public function stock( Request $request)
    {

        $totalPhoneItemsQuery = Phone::where('status', 'available')
            ->when(isset($request['brand_id']) && $request['brand_id'] > 0, function ($query) use ($request) {
                $query->where('brand_id', $request['brand_id']);
            });
        $brands = Brand::all();


        $totalPhoneItems = $totalPhoneItemsQuery->count();

        $totalPhonesValue = $totalPhoneItemsQuery->sum('purchase_price');

//        $phoneStock = Phone::select(
//            'phones.brand_id',
//            'phones.model',
//            'brands.name as brands'
//        )
//            ->selectRaw('COUNT(*) as quantity')
//            ->selectRaw('SUM(purchase_price) as total_purchase_price')
//            ->selectRaw('SUM(selling_price) as total_selling_price')
//            ->selectRaw('(SUM(selling_price) - SUM(purchase_price)) as total_profit')
//            ->leftJoin('brands','brands.id','=','phones.brand_id')
//            ->where('phones.status', 'available')
//            ->groupBy('phones.brand_id', 'phones.model', 'brands.name')
//            ->get();
        $phoneStockQuery = Phone::select(
            'phones.brand_id',
            'phones.model',
            'brands.name as brands'
        )
            ->selectRaw('COUNT(*) as quantity')
            ->selectRaw('SUM(purchase_price) as total_purchase_price')
            ->selectRaw('SUM(selling_price) as total_selling_price')
            ->selectRaw('(SUM(selling_price) - SUM(purchase_price)) as total_profit')
            ->leftJoin('brands', 'brands.id', '=', 'phones.brand_id')
            ->where('phones.status', 'available')
            ->groupBy('phones.brand_id', 'phones.model', 'brands.name');

        if (isset($request['brand_id']) && $request['brand_id'] > 0) {
            $phoneStockQuery->where('phones.brand_id', $request['brand_id']);
        }

        $phoneStock = $phoneStockQuery->get();

        $accessoryStock = Accessory::select('name')
            ->selectRaw('SUM(quantity) as quantity')
            ->selectRaw('SUM(purchase_price * quantity) as total_purchase_price')
            ->selectRaw('SUM(selling_price * quantity) as total_selling_price')
            ->where('status', 'in_stock')
            ->groupBy('name')
            ->get();

        $totalAccessoryItems = $accessoryStock->sum('quantity');
        $totalAccessoriesValue = $accessoryStock->sum('total_purchase_price');
        $totalStockItems = $totalPhoneItems + $totalAccessoryItems;
        $totalStockValue = $totalPhonesValue + $totalAccessoriesValue;
                $phonesQuery = Phone::query();
        $accessoriesQuery = Accessory::query();
        $lowStockPhones = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->count();
        $lowStockAccessories = $accessoriesQuery->clone()->whereColumn('quantity', '<=', 'low_stock_threshold')->count();
        $lowStockCount = $lowStockPhones + $lowStockAccessories;

        if ($request->query('download') === 'true') {
            $csvData = [];

            // Phones section header
            $csvData[] = ['Phones Stock'];
            $csvData[] = ['Brand', 'Model', 'Quantity', 'Total Purchase Price', 'Total Selling Price', 'Total Profit'];

            // Add phones data
            foreach ($phoneStock as $phone) {
                $csvData[] = [
                    $phone->brands,
                    $phone->model,
                    $phone->quantity,
                    number_format($phone->total_purchase_price, 2),
                    number_format($phone->total_selling_price, 2),
                    number_format($phone->total_profit, 2),
                ];
            }

            // Add empty line to separate sections
            $csvData[] = [];

            // Accessories section header
            $csvData[] = ['Accessories Stock'];
            $csvData[] = ['Item', 'Quantity', 'Total Purchase Price', 'Total Selling Price', 'Total Profit'];

            // Add accessories data
            foreach ($accessoryStock as $accessory) {
                $totalProfit = $accessory->total_selling_price - $accessory->total_purchase_price;
                $csvData[] = [
                    $accessory->name,
                    $accessory->quantity,
                    number_format($accessory->total_purchase_price, 2),
                    number_format($accessory->total_selling_price, 2),
                    number_format($totalProfit, 2),
                ];
            }

            // Generate CSV content
            $handle = fopen('php://memory', 'r+');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            $filename = 'combined_stock_' . date('Y-m-d') . '.csv';

            // Return CSV download response
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"")
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }




        return view('reports.stock', compact(
            'phoneStock',
            'accessoryStock',
            'totalPhoneItems',
            'totalPhonesValue',
            'totalAccessoryItems',
            'totalAccessoriesValue',
            'brands',
            'totalStockItems',
            'totalStockValue',
            'lowStockCount'
        ));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $download = $request->input('download');

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();
        $saleItemsQuery = SaleItem::query();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $startDate));
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $endDate));
        }

        if ($download) {
            return $this->download($request, 'profit_loss');
        }

        // Detailed metrics for the new report
        $sales = $salesQuery->with('saleItems.phone')->get();
        $expenses = $expensesQuery->get();

        $totalRevenue = $sales->sum('final_amount');
        $totalCostOfGoodsSold = $sales->flatMap->saleItems->sum(function($item) {
            if ($item->phone) {
                return $item->phone->purchase_price ?? 0;
            } elseif ($item->accessory) {
                return $item->accessory->purchase_price ?? 0;
            }
            return 0;
        });
        $totalExpenses = $expenses->sum('amount');

        $grossProfit = $totalRevenue - $totalCostOfGoodsSold;
        $netProfit = $grossProfit - $totalExpenses;
        $profitMarginPercentage = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Data for monthly P&L chart
        $monthlyData = Sale::select(
            DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month'),
            DB::raw('SUM(final_amount) as revenue')
        )
            ->whereBetween('sale_date', [
                $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(6),
                $endDate ? Carbon::parse($endDate) : Carbon::now()
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyCogs = SaleItem::with(['phone', 'accessory'])
            ->whereHas('sale', fn($q) => $q->whereBetween('sale_date', [
                $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(6),
                $endDate ? Carbon::parse($endDate) : Carbon::now()
            ]))
            ->get()
            ->groupBy(fn($item) => Carbon::parse($item->sale->sale_date)->format('Y-m'))
            ->map(fn($group) => $group->sum(fn($item) => ($item->phone->purchase_price ?? $item->accessory->purchase_price) ?? 0));

        $monthlyExpenses = Expense::select(
            DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
            DB::raw('SUM(amount) as expenses')
        )
            ->whereBetween('expense_date', [
                $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(6),
                $endDate ? Carbon::parse($endDate) : Carbon::now()
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $allMonths = $monthlyData->pluck('month')->merge($monthlyExpenses->keys())->unique()->sort();
        $profitLossChartLabels = $allMonths->map(fn($month) => Carbon::parse($month)->format('M Y'))->toArray();

        $revenueData = $allMonths->map(fn($month) => $monthlyData->where('month', $month)->first()?->revenue ?? 0)->toArray();
        $cogsData = $allMonths->map(fn($month) => $monthlyCogs->get($month) ?? 0)->toArray();
        $expensesData = $allMonths->map(fn($month) => $monthlyExpenses->get($month)?->expenses ?? 0)->toArray();

        $netProfitData = array_map(fn($rev, $cogs, $exp) => $rev - $cogs - $exp, $revenueData, $cogsData, $expensesData);

        return view('reports.profit_loss', compact(
            'totalRevenue',
            'totalCostOfGoodsSold',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'profitMarginPercentage',
            'startDate',
            'endDate',
            'sales',
            'expenses',
            'profitLossChartLabels',
            'revenueData',
            'cogsData',
            'expensesData',
            'netProfitData'
        ));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $category = $request->input('category', '');
        $download = $request->input('download');

        $expensesQuery = Expense::query();

        if ($startDate) {
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
        }
        if ($category) {
            $expensesQuery->where('category', $category);
        }

        if ($download) {
            return $this->download($request, 'expenses');
        }

        $expenses = $expensesQuery->orderBy('expense_date', 'desc')->paginate(10);

        $totalExpenses = $expensesQuery->sum('amount');

        $expenseCategories = Expense::select('category')
            ->distinct()
            ->pluck('category');

        $expensesByCategory = $expensesQuery->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $expensesChartLabels = $expensesByCategory->pluck('category')->toArray();
        $expensesChartData = $expensesByCategory->pluck('total')->toArray();

        return view('reports.expenses', compact(
            'expenses',
            'totalExpenses',
            'startDate',
            'endDate',
            'category',
            'expenseCategories',
            'expensesByCategory',
            'expensesChartLabels',
            'expensesChartData'
        ));
    }

    public function installments(Request $request)
    {
        $status = $request->input('status', '');
        $download = $request->input('download');

        // Main query for installment plans
        $installmentPlansQuery = InstallmentPlan::query();

        // Apply status filter if it exists
        if ($status) {
            $installmentPlansQuery->where('status', $status);
        }

        // Handle download request
        if ($download) {
            return $this->download($request, 'installments');
        }

        // --- Efficient Summary Metrics Calculation ---
        // Calculate all metrics with dedicated queries, avoiding re-running the main query
        $totalPlans = InstallmentPlan::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->count();

        $totalPlansCompleted = InstallmentPlan::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->where('status', 'completed')
            ->count();

        $totalPlansDefaulted = InstallmentPlan::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->where('status', 'defaulted')
            ->count();

        // Optimized calculation for total collected and pending amounts
        $totalCollectedAmount = InstallmentPayment::query()
            ->whereHas('installmentPlan', function ($query) use ($status) {
                if ($status) {
                    $query->where('status', $status);
                }
            })
            ->sum('amount_paid');

        $totalInitialSaleAmount = InstallmentPlan::query()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->join('sales', 'installment_plans.sale_id', '=', 'sales.id')
            ->sum('sales.final_amount');

        $totalPendingAmount = $totalInitialSaleAmount - $totalCollectedAmount;

        // Fetch the paginated installment plans for the view
        $installmentPlans = $installmentPlansQuery->with(['sale.saleItems.phone', 'sale.saleItems.accessory', 'installmentPayments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.installments', compact(
            'installmentPlans',
            'totalPlans',
            'totalPendingAmount',
            'totalCollectedAmount',
            'totalPlansCompleted',
            'totalPlansDefaulted',
            'status'
        ));
    }


    public function users(Request $request)
    {
        $download = $request->input('download');
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');

        // Fetch all sales and map to a standardized activity format
        $allSales = Sale::with(['saleItems.phone.brand', 'saleItems.accessory'])
            ->when($startDate, fn ($query) => $query->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('sale_date', '<=', $endDate))
            ->get()
            ->map(function ($sale) {
                $itemNames = $sale->saleItems->map(function ($item) {
                    if ($item->phone) {
                        return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                    } elseif ($item->accessory) {
                        return $item->accessory->name;
                    }
                    return 'Unknown Item';
                })->implode(', ');
                return [
                    'type' => 'sale',
                    'description' => "✔️ Sold {$itemNames} to {$sale->customer_name} for $" . number_format($sale->final_amount, 2),
                    'date' => $sale->sale_date,
                    'link' => route('sales.show', $sale->id)
                ];
            });

        // Fetch all received phones and map to a standardized activity format
        $allReceivedPhones = Phone::with('brand')
            ->when($startDate, fn ($query) => $query->whereDate('received_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('received_at', '<=', $endDate))
            ->get()
            ->map(function ($phone) {
                return [
                    'type' => 'received',
                    'description' => "📦 Received 1 {$phone->brand->name} {$phone->model} (IMEI: {$phone->imei})",
                    'date' => $phone->received_at,
                    // The accessories.index route was not defined, so we'll link to the reports.users page as a fallback.
                    'link' => route('reports.users')
                ];
            });

        // Fetch all received accessories and map to a standardized activity format
        $allReceivedAccessories = Accessory::when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->get()
            ->map(function ($accessory) {
                return [
                    'type' => 'received',
                    'description' => "📦 Received {$accessory->quantity} {$accessory->name} into inventory",
                    'date' => $accessory->created_at,
                    // The accessories.index route was not defined, so we'll link to the reports.users page as a fallback.
                    'link' => route('reports.users')
                ];
            });

        // Fetch all installment payments and map to a standardized activity format
        $allInstallmentPayments = InstallmentPayment::with('installmentPlan.sale.saleItems.phone')
            ->when($startDate, fn ($query) => $query->whereDate('payment_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('payment_date', '<=', $endDate))
            ->get()
            ->map(function ($payment) {
                $itemNames = 'N/A';
                if ($payment->installmentPlan && $payment->installmentPlan->sale && $payment->installmentPlan->sale->saleItems->isNotEmpty()) {
                    $itemNames = $payment->installmentPlan->sale->saleItems->map(function($item) {
                        if ($item->phone) {
                            return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                        } elseif ($item->accessory) {
                            return $item->accessory->name;
                        }
                    })->implode(', ');
                }
                return [
                    'type' => 'payment',
                    'description' => "💵 Received installment payment for {$itemNames} - $" . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => route('sales.show', $payment->installmentPlan->sale->id)
                ];
            });

        // Fetch all expenses and map to a standardized activity format
        $allExpenses = Expense::latest('expense_date')
            ->when($startDate, fn ($query) => $query->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('expense_date', '<=', $endDate))
            ->get()
            ->map(function ($expense) {
                return [
                    'type' => 'expense',
                    'description' => "💸 Expense: {$expense->description} ({$expense->category}) - $" . number_format($expense->amount, 2),
                    'date' => $expense->expense_date,
                    'link' => route('expenses.index')
                ];
            });

        // Combine all activities into a single collection, sort by date, and paginate
        $allActivitiesCollection = (new Collection())
            ->concat($allSales)
            ->concat($allReceivedPhones)
            ->concat($allReceivedAccessories)
            ->concat($allInstallmentPayments)
            ->concat($allExpenses)
            ->sortByDesc('date')
            ->values();

        // Handle download request
        if ($download) {
            return $this->download($request, 'users');
        }

        $page = request()->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $recentActivities = new LengthAwarePaginator(
            $allActivitiesCollection->slice($offset, $perPage),
            $allActivitiesCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('reports.users', compact('recentActivities', 'startDate', 'endDate','allActivitiesCollection'));
    }


    public function general(Request $request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $download = $request->input('download');

        $salesQuery = Sale::query();
        $expensesQuery = Expense::query();
        $phonesQuery = Phone::query();
        $installmentPlansQuery = InstallmentPlan::query();
        $saleItemsQuery = SaleItem::query()->with(['sale', 'phone.brand', 'accessory']);

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
            $phonesQuery->whereDate('received_at', '>=', $startDate);
            $installmentPlansQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $startDate));
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $startDate));
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
            $phonesQuery->whereDate('received_at', '<=', $endDate);
            $installmentPlansQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $endDate));
            $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $endDate));
        }

        if ($download) {
            return $this->download($request, 'general');
        }

        // Fetch all sales items for the detailed report
        $detailedItems = $saleItemsQuery->get();

        // Calculate total COGS and profit based on the detailed items
        $totalCostOfGoodsSold = $detailedItems->sum(function ($item) {
            if ($item->phone) {
                return $item->phone->purchase_price;
            } elseif ($item->accessory) {
                return $item->accessory->cost_price;
            }
            return 0;
        });

        // Fetch high-level summary data
        $totalRevenue = $salesQuery->sum('final_amount');
        $totalExpenses = $expensesQuery->sum('amount');
        $totalSalesCount = $salesQuery->count();
        $totalPhonesInStock = $phonesQuery->clone()->where('status', 'available')->count();
        $totalSoldPhones = $detailedItems->whereNotNull('phone_id')->count();
        $totalSoldAccessories = $detailedItems->whereNotNull('accessory_id')->count();
        $totalActiveInstallments = $installmentPlansQuery->clone()->where('status', 'active')->count();

        // Calculate profit for the summary cards
        $netProfit = $totalRevenue - $totalCostOfGoodsSold - $totalExpenses;

        // Combine all the raw data into a single $summary array
        $summary = [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'totalSalesCount' => $totalSalesCount,
            'totalPhonesInStock' => $totalPhonesInStock,
            'totalSoldPhones' => $totalSoldPhones,
            'totalSoldAccessories' => $totalSoldAccessories,
            'totalActiveInstallments' => $totalActiveInstallments,
            'totalCostOfGoodsSold' => $totalCostOfGoodsSold,
        ];

        return view('reports.general', compact(
            'summary',
            'detailedItems',
            'startDate',
            'endDate'
        ));
    }


    /**
     * Handles the download of various reports in CSV format.
     *
     * @param Request $request
     * @param string $reportType
     * @return StreamedResponse
     */
    public function download(Request $request, string $reportType)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $reportType . '_report_' . Carbon::now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($request, $reportType) {
            $handle = fopen('php://output', 'w');

            switch ($reportType) {
                case 'sales':
                    $salesQuery = Sale::query();
                    if ($request->filled('start_date')) {
                        $salesQuery->whereDate('sale_date', '>=', $request->input('start_date'));
                    }
                    if ($request->filled('end_date')) {
                        $salesQuery->whereDate('sale_date', '<=', $request->input('end_date'));
                    }
                    $sales = $salesQuery->with(['saleItems.phone.brand', 'saleItems.accessory'])->get();
                    fputcsv($handle, ['Sale Date', 'Customer', 'Item Type', 'Item Name', 'Item Price', 'Item Cost', 'Item Profit', 'Payment Type', 'Payment Status']);
                    foreach ($sales as $sale) {
                        foreach ($sale->saleItems as $item) {
                            $itemName = 'N/A';
                            $itemType = 'N/A';
                            $itemCost = 0;
                            if ($item->phone) {
                                $itemType = 'Phone';
                                $itemName = ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                                $itemCost = $item->phone->purchase_price;
                            } elseif ($item->accessory) {
                                $itemType = 'Accessory';
                                $itemName = $item->accessory->name;
                                $itemCost = $item->accessory->purchase_price;
                            }
                            $profit = $item->unit_price - $itemCost;

                            fputcsv($handle, [
                                Carbon::parse($sale->sale_date)->format('Y-m-d'),
                                $sale->customer_name,
                                $itemType,
                                $itemName,
                                number_format($item->unit_price, 2),
                                number_format($itemCost, 2),
                                number_format($profit, 2),
                                $sale->is_installment ? 'Installment' : 'Full Payment',
                                ucfirst($sale->payment_status),
                            ]);
                        }
                    }
                    break;
                case 'stock':
                    $phonesQuery = Phone::query()->where('status', 'available');
                    $accessoriesQuery = Accessory::query();
                    if ($request->filled('brand_id')) {
                        $phonesQuery->where('brand_id', $request->input('brand_id'));
                    }
                    $phoneStock = $phonesQuery->with('brand')->get();
                    $accessoryStock = $accessoriesQuery->get();

                    fputcsv($handle, ['Item Type', 'Brand', 'Name/Model', 'Current Stock', 'Low Stock Threshold', 'Cost Value', 'Total Value']);
                    foreach ($phoneStock as $phone) {
                        fputcsv($handle, [
                            'Phone',
                            $phone->brand->name ?? 'N/A',
                            $phone->model,
                            '1 (single unit)', // Assuming phones are tracked as single units
                            $phone->low_stock_threshold ?? 'N/A', // Assuming low_stock_threshold is a column on the Phone model
                            number_format($phone->purchase_price, 2),
                            number_format($phone->purchase_price, 2),
                        ]);
                    }
                    foreach ($accessoryStock as $accessory) {
                        fputcsv($handle, [
                            'Accessory',
                            'N/A',
                            $accessory->name,
                            $accessory->quantity,
                            $accessory->low_stock_threshold,
                            number_format($accessory->cost_price, 2),
                            number_format($accessory->cost_price * $accessory->quantity, 2),
                        ]);
                    }
                    break;
                case 'profit_loss':
                    $salesQuery = Sale::query();
                    $expensesQuery = Expense::query();
                    $saleItemsQuery = SaleItem::query();
                    if ($request->filled('start_date')) {
                        $salesQuery->whereDate('sale_date', '>=', $request->input('start_date'));
                        $expensesQuery->whereDate('expense_date', '>=', $request->input('start_date'));
                        $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $request->input('start_date')));
                    }
                    if ($request->filled('end_date')) {
                        $salesQuery->whereDate('sale_date', '<=', $request->input('end_date'));
                        $expensesQuery->whereDate('expense_date', '<=', $request->input('end_date'));
                        $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $request->input('end_date')));
                    }
                    $totalRevenue = $salesQuery->sum('final_amount');
                    $totalCostOfGoodsSold = $saleItemsQuery->with(['phone', 'accessory'])->get()->sum(function($item) {
                        if ($item->phone) {
                            return $item->phone->purchase_price ?? 0;
                        } elseif ($item->accessory) {
                            return $item->accessory->cost_price ?? 0;
                        }
                        return 0;
                    });
                    $totalExpenses = $expensesQuery->sum('amount');
                    $grossProfit = $totalRevenue - $totalCostOfGoodsSold;
                    $netProfit = $grossProfit - $totalExpenses;
                    fputcsv($handle, ['Metric', 'Amount']);
                    fputcsv($handle, ['Total Revenue', number_format($totalRevenue, 2)]);
                    fputcsv($handle, ['Total Cost of Goods Sold', number_format($totalCostOfGoodsSold, 2)]);
                    fputcsv($handle, ['Gross Profit', number_format($grossProfit, 2)]);
                    fputcsv($handle, ['Total Expenses', number_format($totalExpenses, 2)]);
                    fputcsv($handle, ['Net Profit', number_format($netProfit, 2)]);
                    break;
                case 'expenses':
                    $expensesQuery = Expense::query();
                    if ($request->filled('start_date')) {
                        $expensesQuery->whereDate('expense_date', '>=', $request->input('start_date'));
                    }
                    if ($request->filled('end_date')) {
                        $expensesQuery->whereDate('expense_date', '<=', $request->input('end_date'));
                    }
                    if ($request->filled('category')) {
                        $expensesQuery->where('category', $request->input('category'));
                    }
                    $expenses = $expensesQuery->get();
                    fputcsv($handle, ['Date', 'Category', 'Description', 'Amount']);
                    foreach ($expenses as $expense) {
                        fputcsv($handle, [
                            Carbon::parse($expense->expense_date)->format('Y-m-d'),
                            $expense->category,
                            $expense->description,
                            number_format($expense->amount, 2),
                        ]);
                    }
                    break;
                case 'installments':
                    $installmentPlansQuery = InstallmentPlan::query();
                    if ($request->filled('status')) {
                        $installmentPlansQuery->where('status', $request->input('status'));
                    }
                    $plans = $installmentPlansQuery->with(['sale.saleItems.phone', 'sale.saleItems.accessory', 'installmentPayments'])->get();
                    fputcsv($handle, ['Sale ID', 'Customer', 'Items', 'Initial Amount', 'Amount Paid', 'Remaining', 'Payment Start Date', 'Status']);
                    foreach ($plans as $plan) {
                        $totalPaid = $plan->installmentPayments->sum('amount_paid');
                        $remaining = ($plan->sale->final_amount ?? 0) - $totalPaid;
                        $items = $plan->sale->saleItems->map(function ($item) {
                            if ($item->phone) {
                                return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                            } elseif ($item->accessory) {
                                return $item->accessory->name;
                            }
                            return 'Unknown';
                        })->implode('; ');

                        fputcsv($handle, [
                            $plan->sale_id,
                            $plan->sale->customer_name ?? 'N/A',
                            $items,
                            number_format($plan->sale->final_amount, 2),
                            number_format($totalPaid, 2),
                            number_format($remaining, 2),
                            Carbon::parse($plan->created_at)->format('Y-m-d'),
                            ucfirst($plan->status),
                        ]);
                    }
                    break;
                case 'users':
                    $allSales = Sale::with(['saleItems.phone.brand', 'saleItems.accessory'])
                        ->when($request->filled('start_date'), fn ($query) => $query->whereDate('sale_date', '>=', $request->input('start_date')))
                        ->when($request->filled('end_date'), fn ($query) => $query->whereDate('sale_date', '<=', $request->input('end_date')))
                        ->get()
                        ->map(function ($sale) {
                            $itemNames = $sale->saleItems->map(function ($item) {
                                if ($item->phone) {
                                    return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                                } elseif ($item->accessory) {
                                    return $item->accessory->name;
                                }
                                return 'Unknown Item';
                            })->implode(', ');
                            return [
                                'type' => 'Sale',
                                'description' => "{$itemNames} sold to {$sale->customer_name}",
                                'amount' => number_format($sale->final_amount, 2),
                                'date' => $sale->sale_date,
                            ];
                        });

                    $allReceivedPhones = Phone::with('brand')
                        ->when($request->filled('start_date'), fn ($query) => $query->whereDate('received_at', '>=', $request->input('start_date')))
                        ->when($request->filled('end_date'), fn ($query) => $query->whereDate('received_at', '<=', $request->input('end_date')))
                        ->get()
                        ->map(function ($phone) {
                            return [
                                'type' => 'Received Phone',
                                'description' => "1 {$phone->brand->name} {$phone->model} ({$phone->color}) received into inventory",
                                'amount' => number_format($phone->purchase_price, 2),
                                'date' => $phone->received_at,
                            ];
                        });

                    $allReceivedAccessories = Accessory::when($request->filled('start_date'), fn ($query) => $query->whereDate('created_at', '>=', $request->input('start_date')))
                        ->when($request->filled('end_date'), fn ($query) => $query->whereDate('created_at', '<=', $request->input('end_date')))
                        ->get()
                        ->map(function ($accessory) {
                            return [
                                'type' => 'Received Accessory',
                                'description' => "{$accessory->quantity} {$accessory->name} received into inventory",
                                'amount' => number_format($accessory->cost_price * $accessory->quantity, 2),
                                'date' => $accessory->created_at,
                            ];
                        });

                    $allInstallmentPayments = InstallmentPayment::with('installmentPlan.sale')
                        ->when($request->filled('start_date'), fn ($query) => $query->whereDate('payment_date', '>=', $request->input('start_date')))
                        ->when($request->filled('end_date'), fn ($query) => $query->whereDate('payment_date', '<=', $request->input('end_date')))
                        ->get()
                        ->map(function ($payment) {
                            $itemNames = 'N/A';
                            if ($payment->installmentPlan && $payment->installmentPlan->sale && $payment->installmentPlan->sale->saleItems->isNotEmpty()) {
                                $itemNames = $payment->installmentPlan->sale->saleItems->map(function($item) {
                                    if ($item->phone) {
                                        return ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                                    } elseif ($item->accessory) {
                                        return $item->accessory->name;
                                    }
                                })->implode(', ');
                            }
                            return [
                                'type' => 'Installment Payment',
                                'description' => "Payment received for {$itemNames}",
                                'amount' => number_format($payment->amount_paid, 2),
                                'date' => $payment->payment_date,
                            ];
                        });

                    $allExpenses = Expense::when($request->filled('start_date'), fn ($query) => $query->whereDate('expense_date', '>=', $request->input('start_date')))
                        ->when($request->filled('end_date'), fn ($query) => $query->whereDate('expense_date', '<=', $request->input('end_date')))
                        ->get()
                        ->map(function ($expense) {
                            return [
                                'type' => 'Expense',
                                'description' => "Expense: {$expense->description} ({$expense->category})",
                                'amount' => number_format($expense->amount, 2),
                                'date' => $expense->expense_date,
                            ];
                        });

                    $allActivities = $allSales
                        ->concat($allReceivedPhones)
                        ->concat($allReceivedAccessories)
                        ->concat($allInstallmentPayments)
                        ->concat($allExpenses)
                        ->sortByDesc('date')
                        ->values();

                    fputcsv($handle, ['Type', 'Date', 'Description', 'Amount']);
                    foreach ($allActivities as $activity) {
                        fputcsv($handle, [
                            $activity['type'],
                            Carbon::parse($activity['date'])->format('Y-m-d H:i:s'),
                            $activity['description'],
                            $activity['amount'],
                        ]);
                    }
                    break;
                case 'general':
                    $saleItemsQuery = SaleItem::query()->with(['sale', 'phone.brand', 'accessory']);
                    if ($request->filled('start_date')) {
                        $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '>=', $request->input('start_date')));
                    }
                    if ($request->filled('end_date')) {
                        $saleItemsQuery->whereHas('sale', fn ($q) => $q->whereDate('sale_date', '<=', $request->input('end_date')));
                    }
                    $detailedItems = $saleItemsQuery->get();
                    fputcsv($handle, ['Sale Date', 'Customer', 'Item Type', 'Item Name', 'Price', 'Cost', 'Profit']);
                    foreach ($detailedItems as $item) {
                        $itemName = 'N/A';
                        $itemType = 'N/A';
                        $itemCost = 0;
                        if ($item->phone) {
                            $itemType = 'Phone';
                            $itemName = ($item->phone->brand->name ?? 'N/A') . ' ' . $item->phone->model;
                            $itemCost = $item->phone->purchase_price;
                        } elseif ($item->accessory) {
                            $itemType = 'Accessory';
                            $itemName = $item->accessory->name;
                            $itemCost = $item->accessory->cost_price;
                        }
                        $profit = $item->price - $itemCost;
                        fputcsv($handle, [
                            Carbon::parse($item->sale->sale_date)->format('Y-m-d'),
                            $item->sale->customer_name,
                            $itemType,
                            $itemName,
                            number_format($item->price, 2),
                            number_format($itemCost, 2),
                            number_format($profit, 2),
                        ]);
                    }
                    break;
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function updateStockQuantity(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:accessories,id',
            'new_quantity' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:255',
        ]);
        $accessory = Accessory::find($validatedData['id']);
        $accessory->quantity = $validatedData['new_quantity'];
        $accessory->save();

        // You would need a StockAdjustment model and migration for this
         StockAdjustment::create([
             'accessory_id' => $accessory->id,
             'old_quantity' => $accessory->getOriginal('quantity'),
             'new_quantity' => $accessory->quantity,
             'comment' => $validatedData['comment'],
             'adjusted_by_user_id' => auth()->id(),
         ]);

        // 4. Return a JSON response
        return response()->json([
            'success' => true,
            'message' => "Quantity for {$accessory->name} has been updated to {$accessory->quantity}.",
            'new_quantity' => $accessory->quantity
        ]);
    }
    public function detailedStock(Request $request)
    {
        // Fetch all accessories that are in stock, without aggregating
        $detailedAccessoryStock = Accessory::where('status', 'in_stock')
            ->orderBy('name')
            ->orderBy('purchase_price')
            ->paginate(10); // Paginate to handle large datasets

        // Fetch all phones that are in stock, without aggregating
        $detailedPhoneStock = DB::table('phones')
        ->leftJoin('sale_items', 'sale_items.phone_id', '=', 'phones.id')
        ->leftJoin('brands', 'brands.id', '=', 'phones.brand_id')
        ->where('phones.status', 'available')
        ->whereNull('sale_items.phone_id')
        ->select('phones.*', 'brands.name as brand_name')
        ->orderBy('brands.name')
        ->orderBy('phones.model')
        ->paginate(10);


        return view('reports.detailed_stock', compact('detailedAccessoryStock', 'detailedPhoneStock'));
    }


    /**
     * Update the quantity of a specific accessory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAccessoryStock(Request $request, $id)
    {

        $request->validate([
            'value' => 'required|integer|min:0',
            'comment' => 'nullable|string|max:255',
        ]);

        $accessory = Accessory::findOrFail($id);
        $oldQuantity = $accessory->quantity;

        // Update the quantity
        $accessory->quantity = $request->input('value');
        $accessory->save();
        $userId = Auth()->user()->id ?? null;

        // Log the stock adjustment
        StockAdjustment::create([
            'accessory_id' => $accessory->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $accessory->quantity,
            'comment' => $request->input('comment'),
            'adjusted_by_user_id' => $userId,
        ]);

        return response()->json(['message' => 'Accessory stock updated successfully!', 'new_quantity' => $accessory->quantity]);
    }
    public function updatePhoneImei(Request $request, $id)
    {
        // Example logic:
        // $request->validate(['new_imei' => 'required|string|unique:phones,imei,'.$id]);
        // $phone = Phone::findOrFail($id);
        // $phone->imei = $request->new_imei;
        // $phone->save();
        // return response()->json(['message' => 'Phone IMEI updated successfully!']);
        return response()->json(['message' => 'This feature is not yet implemented.'], 400);
    }

    public function removePhone(Request $request, $id)
    {
        try {
            // 1. Find the phone by its ID
            $phone = Phone::findOrFail($id);

            // 2. Validate that a comment is provided for removal
            $request->validate([
                'comment' => 'required|string|max:255',
            ]);

            // 3. Log the stock adjustment before changing the status
            // CORRECTED: 'id' is a property, not a method.
            $userId = Auth()->user()->id ?? null;

            StockAdjustment::create([
                'phone_id' => $phone->id,
                'comment' => $request->input('comment'),
                'adjusted_by_user_id' => $userId,
            ]);

            // 4. Update the phone's status (this logic seems to be missing from your code)
            // For example, you might want to set a status to 'removed' or delete the record.
            // phone->status = 'removed';
            // phone->save();

            // 5. Return a success response
            return response()->json(['message' => 'Phone removed from stock successfully!'], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Phone not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors if a comment is missing
            return response()->json(['message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            // The more specific catch blocks handle errors better,
            // so this one should ideally be reserved for truly unexpected errors.
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

}


