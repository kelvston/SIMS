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
use App\Models\MotorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
 use Barryvdh\DomPDF\Facade\Pdf;
 use Illuminate\Support\Facades\Mail;
 use App\Mail\GeneralReportMail;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    public function __construct()
    {
        // Protect report related actions
        $this->middleware('auth'); // All reports require authentication
        $this->middleware('permission:view sales reports')->only('salesReport');
        $this->middleware('permission:view sales reports')->only(['receivablesReport', 'productPerformanceReport', 'creditReport', 'cashFlowReport']);
        $this->middleware('permission:view service reports')->only('serviceReport');
        $this->middleware('permission:view stock reports')->only('stockReport');
        $this->middleware('permission:view stock reports')->only('inventoryValuationReport');
        $this->middleware('permission:view profit loss reports')->only('profitLossReport');
        $this->middleware('permission:view profit loss reports')->only('expenseReport');
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
                    'description' => "✔️ {$phoneNames} sold to {$sale->customer_name} - Tsh " . number_format($sale->final_amount, 2),
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
                    'description' => "💵 Installment payment received for {$phoneName} - Tsh " . number_format($payment->amount_paid, 2),
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
                    'description' => "💸 Expense: {$expense->description} ({$expense->category}) - Tsh " . number_format($expense->amount, 2),
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

        $stockLevels = StockLevel::with('brand','phone')->orderBy('current_stock', 'asc')->paginate(10);
        // Calculate summary statistics for stock
        $totalStockItems = StockLevel::sum('current_stock') ;
        $lowStockCount = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')->count();

        return view('reports.stock', compact('stockLevels',  'totalStockItems', 'lowStockCount'));
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

    public function expenseReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $query = Expense::with('user')->whereDate('expense_date', '>=', $startDate)->whereDate('expense_date', '<=', $endDate);
        $totalExpenses = (clone $query)->sum('amount');
        $byCategory = (clone $query)->select('category', DB::raw('SUM(amount) as total'))->groupBy('category')->orderByDesc('total')->get();
        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();
        return view('reports.expenses', compact('startDate', 'endDate', 'totalExpenses', 'byCategory', 'expenses'));
    }

    public function receivablesReport()
    {
        $plans = InstallmentPlan::where('status', 'active')->with(['sale.saleReceipt', 'installmentPayments'])->orderBy('next_payment_date')->get();
        $plans->each(function ($plan) {
            $paid = $plan->installmentPayments->sum('amount_paid') + (optional($plan->sale->saleReceipt)->paid_amount ?? 0);
            $plan->outstanding_balance = max(0, (float) $plan->sale->final_amount - $paid);
        });
        $totalOutstanding = $plans->sum('outstanding_balance');
        $overdueCount = $plans->filter(fn ($plan) => $plan->next_payment_date && $plan->next_payment_date->isPast())->count();
        return view('reports.receivables', compact('plans', 'totalOutstanding', 'overdueCount'));
    }

    public function inventoryValuationReport()
    {
        $items = Phone::where('status', 'available')->with('brand')->orderBy('brand_id')->orderBy('model')->get();
        $inventoryCost = $items->sum(fn ($item) => $item->purchase_price * $item->quantity);
        $inventoryRetail = $items->sum(fn ($item) => $item->selling_price * $item->quantity);
        return view('reports.inventory_valuation', compact('items', 'inventoryCost', 'inventoryRetail'));
    }

    public function productPerformanceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $items = SaleItem::whereHas('sale', fn ($q) => $q->activeTransaction()->whereDate('sale_date', '>=', $startDate)->whereDate('sale_date', '<=', $endDate))
            ->with(['phone.brand', 'product'])->get()
            ->groupBy(fn ($item) => $item->phone_id ? 'phone-'.$item->phone_id : 'product-'.$item->product_id)
            ->map(function ($group) {
                $first = $group->first();
                $name = $first->phone ? trim(($first->phone->brand->name ?? '') . ' ' . $first->phone->model) : ($first->product->name ?? 'Removed item');
                return (object) ['name' => $name, 'units' => $group->sum('quantity'), 'revenue' => $group->sum(fn ($item) => $item->unit_price * $item->quantity)];
            })->sortByDesc('revenue')->values();
        return view('reports.product_performance', compact('startDate', 'endDate', 'items'));
    }

    public function creditReport(Request $request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $credits = Sale::activeTransaction()->where('payment_option', 'credit')->where('amount_due', '>', 0)
            ->when($startDate, fn ($q) => $q->whereDate('sale_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('sale_date', '<=', $endDate))
            ->orderBy('credit_due_date')->get();
        $outstanding = $credits->sum('amount_due');
        $overdue = $credits->filter(fn ($sale) => $sale->credit_due_date?->isBefore(today()))->sum('amount_due');
        return view('reports.credit', compact('credits', 'outstanding', 'overdue', 'startDate', 'endDate'));
    }

    public function cashFlowReport(Request $request)
    {
        $data = $this->cashFlowData($request);
        return view('reports.cashflow', $data);
    }

    public function serviceReport(Request $request)
    {
        $data = $this->serviceReportData($request);

        return view('reports.services', $data);
    }

    public function export(Request $request, string $report)
    {
        $permission = ['sales'=>'view sales reports', 'receivables'=>'view sales reports', 'product-performance'=>'view sales reports', 'credit'=>'view sales reports', 'cashflow'=>'view sales reports', 'stock'=>'view stock reports', 'inventory'=>'view stock reports', 'profit-loss'=>'view profit loss reports', 'expenses'=>'view profit loss reports', 'general'=>'view general reports', 'services'=>'view service reports'][$report];
        abort_unless($request->user()->can($permission), 403);
        $data = $this->exportDataset($request, $report);
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle(substr($data['title'], 0, 31));
        $sheet->fromArray([[$data['title']]], null, 'A1');
        $summaryRow = 2;
        foreach ($data['summary'] as $label => $value) {
            $sheet->fromArray([[$label, $value]], null, 'A' . $summaryRow++);
        }
        $headerRow = count($data['summary']) + 3;
        $sheet->fromArray($data['headers'], null, 'A' . $headerRow);
        $sheet->fromArray($data['rows'], null, 'A' . ($headerRow + 1));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $headerRow . ':' . $sheet->getHighestColumn() . $headerRow)->getFont()->setBold(true);
        $sheet->freezePane('A' . ($headerRow + 1));
        foreach (range('A', $sheet->getHighestColumn()) as $column) $sheet->getColumnDimension($column)->setAutoSize(true);
        $writer = new Xlsx($sheet->getParent());
        $name = $report.'-report.xlsx';
        return response()->streamDownload(fn()=> $writer->save('php://output'), $name, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /** The export data deliberately mirrors each report's on-screen filters and totals. */
    private function exportDataset(Request $request, string $report): array
    {
        [$start, $end] = [$request->input('start_date', ''), $request->input('end_date', '')];
        $sales = fn () => Sale::activeTransaction()->when($start, fn ($q) => $q->whereDate('sale_date', '>=', $start))->when($end, fn ($q) => $q->whereDate('sale_date', '<=', $end));
        $expenses = fn () => Expense::query()->when($start, fn ($q) => $q->whereDate('expense_date', '>=', $start))->when($end, fn ($q) => $q->whereDate('expense_date', '<=', $end));

        return match ($report) {
            'sales' => (function () use ($sales) { $items = $sales()->with('saleItems.phone.brand', 'saleItems.product')->latest('sale_date')->get(); return ['title'=>'Sales report','summary'=>['Total sales'=>$items->sum('final_amount'),'Discounts'=>$items->sum('discount_amount'),'Installment sales'=>$items->where('is_installment', true)->count(),'Full-payment sales'=>$items->where('is_installment', false)->count()],'headers'=>['Sale ID','Customer','Items','Sale date','Payment type','Final amount'],'rows'=>$items->map(fn($s)=>[$s->id,$s->customer_name,$s->saleItems->map(fn($i)=>$i->phone ? trim(($i->phone->brand->name ?? '').' '.$i->phone->model).' x'.$i->quantity : (($i->product->name ?? 'Removed item').' x'.$i->quantity))->implode(', '),optional($s->sale_date)->format('Y-m-d H:i'),$s->sale_type_label,(float)$s->final_amount])->all()]; })(),
            'stock' => (function () { $items = StockLevel::with('brand','phone')->orderBy('current_stock')->get(); return ['title'=>'Stock report','summary'=>['Total stock units'=>$items->sum('current_stock'),'Low-stock items'=>$items->filter(fn($i)=>$i->current_stock <= $i->low_stock_threshold)->count()],'headers'=>['Brand','Model','Current stock','Low-stock threshold','Status','Last updated'],'rows'=>$items->map(fn($i)=>[$i->brand->name ?? 'N/A',$i->model,$i->current_stock,$i->low_stock_threshold,$i->current_stock <= $i->low_stock_threshold ? 'Low stock':'Sufficient',optional($i->last_updated_at)->format('Y-m-d H:i')])->all()]; })(),
            'expenses' => (function () use ($expenses) { $items = $expenses()->latest('expense_date')->get(); return ['title'=>'Expense report','summary'=>['Total expenses'=>$items->sum('amount'),'Expense count'=>$items->count()],'headers'=>['Date','Category','Description','Amount'],'rows'=>$items->map(fn($e)=>[Carbon::parse($e->expense_date)->format('Y-m-d'),$e->category,$e->description,(float)$e->amount])->all()]; })(),
            'inventory' => (function () { $items = Phone::where('status','available')->with('brand')->orderBy('brand_id')->orderBy('model')->get(); $cost=$items->sum(fn($i)=>(float)$i->purchase_price*$i->quantity); $retail=$items->sum(fn($i)=>(float)$i->selling_price*$i->quantity); return ['title'=>'Inventory valuation report','summary'=>['Stock cost'=>$cost,'Potential retail value'=>$retail,'Potential margin'=>$retail-$cost],'headers'=>['Brand','Model','Units','Cost value','Retail value'],'rows'=>$items->map(fn($i)=>[$i->brand->name ?? 'N/A',$i->model,$i->quantity,(float)$i->purchase_price*$i->quantity,(float)$i->selling_price*$i->quantity])->all()]; })(),
            'credit' => (function () use ($sales) { $items=$sales()->where('payment_option','credit')->where('amount_due','>',0)->orderBy('credit_due_date')->get(); return ['title'=>'Credit report','summary'=>['Total outstanding'=>$items->sum('amount_due'),'Overdue balance'=>$items->filter(fn($s)=>$s->credit_due_date?->isBefore(today()))->sum('amount_due')],'headers'=>['Sale ID','Customer','Phone','Sale date','Due date','Outstanding balance'],'rows'=>$items->map(fn($s)=>[$s->id,$s->customer_name,$s->customer_phone,optional($s->sale_date)->format('Y-m-d'),optional($s->credit_due_date)->format('Y-m-d'),(float)$s->amount_due])->all()]; })(),
            'receivables' => $this->receivablesExportDataset(),
            'product-performance' => $this->productPerformanceExportDataset($request),
            'profit-loss' => $this->profitLossExportDataset($request),
            'general' => $this->generalExportDataset($request),
            'cashflow' => $this->cashFlowExportDataset($request),
            'services' => $this->serviceExportDataset($request),
        };
    }

    private function serviceReportData(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $status = $request->input('status', '');

        $services = MotorService::with(['vehicle', 'mechanic'])
            ->whereDate('service_date', '>=', $startDate)
            ->whereDate('service_date', '<=', $endDate)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('service_date')
            ->paginate(20)
            ->withQueryString();

        $totals = MotorService::whereDate('service_date', '>=', $startDate)
            ->whereDate('service_date', '<=', $endDate)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->selectRaw('COUNT(*) as jobs, COALESCE(SUM(total_amount), 0) as billed, COALESCE(SUM(amount_paid), 0) as collected')
            ->first();

        return compact('services', 'startDate', 'endDate', 'status', 'totals');
    }

    private function cashFlowData(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $saleCollections = Sale::activeTransaction()->whereDate('sale_date','>=',$startDate)->whereDate('sale_date','<=',$endDate)->where('amount_paid','>',0)->get()->map(fn($s)=>(object)['date'=>$s->sale_date,'type'=>'Sale collection','reference'=>'Sale #'.$s->id.' — '.$s->customer_name,'inflow'=>(float)$s->amount_paid,'outflow'=>0]);
        $installments = InstallmentPayment::with('installmentPlan.sale')->whereDate('payment_date','>=',$startDate)->whereDate('payment_date','<=',$endDate)->get()->map(fn($p)=>(object)['date'=>$p->payment_date,'type'=>'Installment collection','reference'=>'Sale #'.($p->installmentPlan->sale_id ?? 'N/A').' — '.($p->installmentPlan->sale->customer_name ?? 'Unknown'),'inflow'=>(float)$p->amount_paid,'outflow'=>0]);
        $outflows = Expense::whereDate('expense_date','>=',$startDate)->whereDate('expense_date','<=',$endDate)->get()->map(fn($e)=>(object)['date'=>Carbon::parse($e->expense_date),'type'=>'Expense','reference'=>$e->category.($e->description ? ' — '.$e->description : ''),'inflow'=>0,'outflow'=>(float)$e->amount]);
        $rows = $saleCollections->concat($installments)->concat($outflows)->sortByDesc('date')->values();
        return ['startDate'=>$startDate,'endDate'=>$endDate,'rows'=>$rows,'cashIn'=>$rows->sum('inflow'),'cashOut'=>$rows->sum('outflow'),'netCashFlow'=>$rows->sum('inflow')-$rows->sum('outflow')];
    }

    private function cashFlowExportDataset(Request $request): array { $d=$this->cashFlowData($request); return ['title'=>'Cash flow report','summary'=>['Cash in'=>$d['cashIn'],'Cash out'=>$d['cashOut'],'Net cash flow'=>$d['netCashFlow']],'headers'=>['Date','Type','Reference','Cash in','Cash out','Net movement'],'rows'=>$d['rows']->map(fn($r)=>[optional($r->date)->format('Y-m-d H:i'),$r->type,$r->reference,$r->inflow,$r->outflow,$r->inflow-$r->outflow])->all()]; }
    private function serviceExportDataset(Request $request): array { $d=$this->serviceReportData($request); $items=MotorService::with(['vehicle','mechanic'])->whereDate('service_date','>=',$d['startDate'])->whereDate('service_date','<=',$d['endDate'])->when($d['status'],fn($q)=>$q->where('status',$d['status']))->latest('service_date')->get(); return ['title'=>'Service report','summary'=>['Service jobs'=>$items->count(),'Amount billed'=>$items->sum('total_amount'),'Amount collected'=>$items->sum('amount_paid'),'Outstanding balance'=>$items->sum(fn($item)=>(float)$item->total_amount-(float)$item->amount_paid)],'headers'=>['Job number','Service date','Vehicle','Customer','Mechanic','Status','Billed','Paid','Balance'],'rows'=>$items->map(fn($item)=>[$item->job_number,optional($item->service_date)->format('Y-m-d'),$item->vehicle?->registration_number,$item->vehicle?->customer_name,$item->mechanic?->name,ucwords(str_replace('_',' ',$item->status)),(float)$item->total_amount,(float)$item->amount_paid,(float)$item->total_amount-(float)$item->amount_paid])->all()]; }
    private function receivablesExportDataset(): array { $plans=InstallmentPlan::where('status','active')->with(['sale.saleReceipt','installmentPayments'])->orderBy('next_payment_date')->get(); $plans->each(function($p){$p->outstanding_balance=max(0,(float)$p->sale->final_amount-$p->installmentPayments->sum('amount_paid')-(optional($p->sale->saleReceipt)->paid_amount ?? 0));}); return ['title'=>'Receivables report','summary'=>['Outstanding balance'=>$plans->sum('outstanding_balance'),'Overdue plans'=>$plans->filter(fn($p)=>$p->next_payment_date?->isPast())->count()],'headers'=>['Sale ID','Customer','Next payment','Installment amount','Outstanding'],'rows'=>$plans->map(fn($p)=>[$p->sale_id,$p->sale->customer_name ?? 'N/A',optional($p->next_payment_date)->format('Y-m-d'),(float)$p->installment_amount,(float)$p->outstanding_balance])->all()]; }
    private function productPerformanceExportDataset(Request $request): array { $start=$request->input('start_date',Carbon::now()->startOfMonth()->toDateString());$end=$request->input('end_date',Carbon::now()->toDateString());$items=SaleItem::whereHas('sale',fn($q)=>$q->activeTransaction()->whereDate('sale_date','>=',$start)->whereDate('sale_date','<=',$end))->with(['phone.brand','product'])->get()->groupBy(fn($i)=>$i->phone_id?'phone-'.$i->phone_id:'product-'.$i->product_id)->map(function($g){$f=$g->first();return [ $f->phone ? trim(($f->phone->brand->name ?? '').' '.$f->phone->model) : ($f->product->name ?? 'Removed item'),$g->sum('quantity'),$g->sum(fn($i)=>(float)$i->unit_price*$i->quantity)];})->sortByDesc(fn($r)=>$r[2])->values();return ['title'=>'Product performance report','summary'=>['Units sold'=>$items->sum(fn($r)=>$r[1]),'Revenue'=>$items->sum(fn($r)=>$r[2])],'headers'=>['Product','Units sold','Revenue'],'rows'=>$items->all()]; }
    private function profitLossExportDataset(Request $request): array { $start=$request->input('start_date','');$end=$request->input('end_date','');$sales=Sale::activeTransaction()->when($start,fn($q)=>$q->whereDate('sale_date','>=',$start))->when($end,fn($q)=>$q->whereDate('sale_date','<=',$end))->with('saleItems.phone','saleItems.product')->get();$expense=Expense::when($start,fn($q)=>$q->whereDate('expense_date','>=',$start))->when($end,fn($q)=>$q->whereDate('expense_date','<=',$end))->get();$revenue=$sales->sum('final_amount');$cogs=$this->saleItemsCostValue($sales->flatMap->saleItems);$totalExpenses=$expense->sum('amount');return ['title'=>'Profit and loss report','summary'=>['Revenue'=>$revenue,'COGS'=>$cogs,'Gross profit'=>$revenue-$cogs,'Expenses'=>$totalExpenses,'Net profit'=>$revenue-$cogs-$totalExpenses],'headers'=>['Date','Entry type','Reference','Revenue','COGS','Expense'],'rows'=>$sales->map(fn($s)=>[optional($s->sale_date)->format('Y-m-d'),'Sale #'.$s->id,$s->customer_name,(float)$s->final_amount,$this->saleItemsCostValue($s->saleItems),0])->concat($expense->map(fn($e)=>[Carbon::parse($e->expense_date)->format('Y-m-d'),'Expense',$e->category.': '.$e->description,0,0,(float)$e->amount]))->sortByDesc(fn($r)=>$r[0])->values()->all()]; }
    private function generalExportDataset(Request $request): array { $d=$this->buildGeneralReportData($request);$sales=Sale::activeTransaction()->whereDate('sale_date','>=',$d['startDate'])->whereDate('sale_date','<=',$d['endDate'])->orderByDesc('sale_date')->get();return ['title'=>'General business report','summary'=>['Revenue'=>$d['totalRevenue'],'COGS'=>$d['totalCogs'],'Expenses'=>$d['totalExpenses'],'Net profit'=>$d['netProfit'],'Sales count'=>$d['totalSalesCount']],'headers'=>['Sale ID','Customer','Date','Payment type','Amount'],'rows'=>$sales->map(fn($s)=>[$s->id,$s->customer_name,optional($s->sale_date)->format('Y-m-d'),$s->sale_type_label,(float)$s->final_amount])->all()]; }
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
        $availablePhones      = Phone::where('status', 'available')->sum('quantity');
        $soldPhones           = Phone::whereIn('status', ['sold', 'under_installment'])->count();
        $inventoryValue       = $this->inventoryCostValue();

        $stockByBrand = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('SUM(quantity) as count'), DB::raw('SUM(purchase_price * quantity) as value'))
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
                DB::raw('sale_items.quantity as units_sold'),
                DB::raw('SUM(sale_items.unit_price * sale_items.quantity) as revenue')
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
            'availablePhones',  'soldPhones', 'inventoryValue', 'stockByBrand', 'lowStockItems',
            'pendingInstallments', 'activeInstallmentCount',
            'dailySales', 'topBrands', 'recentSales'
        );
    }

    private function inventoryCostValue(): float
    {
        $phoneValue = (float) Phone::where('status', 'available')->sum('quantity * purchase_price');
        return $phoneValue ;
    }

    private function saleItemsCostValue($saleItems): float
    {
        return (float) $saleItems->sum(function ($item) {
            $quantity = (float) ($item->quantity ?? 1);

            $unitCost = $item->unit_cost ?? $item->phone?->purchase_price ?? 0;
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
        abort_unless($request->user()->can('view sales reports'), 403);
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
                        return 'Tsh ' . number_format($sale->final_amount, 2);
                    })
                    ->addColumn('discount_amount', function($sale) {
                        return 'Tsh ' . number_format($sale->discount_amount, 2);
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
                        'final_amount' => 'Tsh ' . number_format($sale->final_amount, 2),
                        'discount_amount' => 'Tsh ' . number_format($sale->discount_amount, 2),
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
        abort_unless($request->user()->can('view sales reports'), 403);
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
