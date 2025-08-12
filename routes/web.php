<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\SaleController; // Import SaleController
use App\Http\Controllers\InstallmentController; // Import InstallmentController
use App\Http\Controllers\ReportController; // Import InstallmentController
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController ;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\BarcodeController;

use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
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

    $grossProfit = $totalRevenueThisMonth - $totalCogsThisMonth;
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

    // New: Recent Activities (combining sales, received, payments)
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

    // Combine all recent activities and sort by date
    $recentActivities = collect()
        ->concat($recentSales)
        ->concat($recentReceivedPhones)
        ->concat($recentInstallmentPayments)
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
        'recentActivities' // New dynamic activity list
    ));

})->middleware(['auth', 'verified', 'permission:view dashboard']) ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// ... other routes ...

Route::get('/phones/receive', [PhoneController::class, 'showReceiveForm'])->name('phones.receive.form');
Route::post('/phones/receive', [PhoneController::class, 'storeReceivedPhones'])->name('phones.receive.store');
Route::get('/phones', [PhoneController::class, 'index'])->name('phones.index');

// Sales Routes
Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::get('/sales/return', [SaleController::class, 'showReturnPage'])->name('sales.return');
Route::get('/sales/print', [SaleController::class, 'printReceipt'])->name('sales.print');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show'); // For viewing a single sale detail
Route::get('/sales/{sale}/pay',[SaleController::class, 'payForm'])->name('sales.pay.form');
Route::post('/sales/{sale}/pay',[SaleController::class, 'storePayment'])->name('sales.pay.store');
Route::get('/sales/receipt/{receipt}', [SaleController::class, 'printSingleReceipt'])->name('sales.print-receipt-single');
//Route::get('/sales/return', [SaleController::class, 'showReturnPage'])->name('sales.return');
Route::post('/sales/search', [SaleController::class, 'search'])->name('sales.search');
Route::post('/sales/return/{sale}', [SaleController::class, 'processReturn'])->name('sales.processReturn');
Route::get('/api/scan-item/{code}', [SaleController::class, 'find']);


// Installment Routes
Route::get('/installments/{installmentPlan}/pay', [InstallmentController::class, 'showPaymentForm'])->name('installments.pay.form');
Route::post('/installments/{installmentPlan}/pay', [InstallmentController::class, 'recordPayment'])->name('installments.pay.store');
Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
Route::post('/installment/payment', [InstallmentController::class, 'store'])->name('installment.payment.store');
// Reporting Routes
//Route::get('/reports/index', [ReportController::class, 'index'])->name('reports.index');
//Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
//Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
//Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReport'])->name('reports.profit_loss'); // New P&L route
//Route::get('/index', [UserController::class, 'index'])->name('users.index');

Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/create_permission', [RoleController::class, 'createPermission'])->name('roles.create_permission');
Route::post('/store_permission', [RoleController::class, 'storePermission'])->name('roles.store_permission');
Route::resource('roles', RoleController::class);
Route::resource('brands', BrandController::class);
Route::resource('categories', categoryController::class);
Route::resource('expenses', ExpenseController::class);


Route::prefix('manage')->name('manage.')->group(function () {
    Route::get('/', [ManageController::class, 'index'])->name('index');
});



Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
    Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profitloss');
    Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
    Route::get('/installments', [ReportController::class, 'installments'])->name('installments');
    Route::get('/users', [ReportController::class, 'users'])->name('users');
    Route::get('/general', [ReportController::class, 'general'])->name('general');
    Route::get('/credit_sale', [ReportController::class, 'creditSale'])->name('credit_sale');
    Route::get('/customers', [ReportController::class, 'CustomerReport'])->name('customers');
    Route::get('/sale_adjustment', [ReportController::class, 'SaleAdjustment'])->name('sale_adjustment');
    Route::get('/download', [ReportController::class, 'download'])->name('download');
    Route::post('/reports/send-all', [ReportController::class, 'sendAllReports'])->name('send_all');

});
Route::post('/reports/stock/update-quantity', [ReportController::class, 'updateStockQuantity'])->name('reports.stock.update-quantity');
//Route::get('/accessories', [AccessoryController::class, 'index'])->name('accessories.index');

Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
Route::get('/reports/detailed-stock', [ReportController::class, 'detailedStock'])->name('reports.detailed-stock');
Route::post('/reports/stock/update-accessory/{id}', [ReportController::class, 'updateAccessoryStock'])->name('stock.update-accessory');
Route::post('/reports/stock/remove-phone/{id}', [ReportController::class, 'removePhone'])->name('stock.remove.phone');
Route::get('/dashboard', [ReportController::class, 'home'])->name('dashboard');

// To view all installment plans
Route::get('/barcodes/generate/form', [BarcodeController::class, 'generateForm'])->name('barcodes.generate.form');
Route::post('/barcodes/generate', [BarcodeController::class, 'generate'])->name('barcodes.generate');
Route::post('/barcodes/download', [BarcodeController::class, 'download'])->name('barcodes.download');





require __DIR__.'/auth.php';
