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

use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MotorServiceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('dashboard');

// Optional: Edit/Delete phone routes
Route::middleware(['auth'])->group(function () {
    Route::get('/phones/{phone}/edit', [DashboardController::class, 'editPhone'])->middleware('permission:edit phones')->name('phones.edit');
    Route::delete('/phones/{phone}', [DashboardController::class, 'deletePhone'])->middleware('permission:delete phones')->name('phones.destroy');
    Route::put('/phones/{phone}', [DashboardController::class, 'updatePhone'])->middleware('permission:edit phones')->name('phones.update');
    Route::get('/accessories/{product}/edit', [DashboardController::class, 'editAccessory'])->middleware('permission:edit phones')->name('accessories.edit');
    Route::put('/accessories/{product}', [DashboardController::class, 'updateAccessory'])->middleware('permission:edit phones')->name('accessories.update');

});
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
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');
Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
Route::post('/orders/{order}/reserve', [OrderController::class, 'reserve'])->name('orders.reserve');
Route::get('/orders/{order}/create-sale', [OrderController::class, 'createSale'])->name('orders.create-sale');
Route::get('/motor-services/my-pending', [MotorServiceController::class, 'myPending'])->name('motor-services.my-pending');
Route::resource('motor-services', MotorServiceController::class)->except(['destroy']);
Route::get('/vehicles/create', [MotorServiceController::class, 'createVehicle'])->name('vehicles.create');
Route::post('/vehicles', [MotorServiceController::class, 'storeVehicle'])->name('vehicles.store');
// For viewing a single sale detail

// Installment Routes
Route::get('/installments/{installmentPlan}/pay', [InstallmentController::class, 'showPaymentForm'])->name('installments.pay.form');
Route::post('/installments/{installmentPlan}/pay', [InstallmentController::class, 'recordPayment'])->name('installments.pay.store');
Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
Route::post('/installment/payment', [InstallmentController::class, 'store'])->name('installment.payment.store');
// Reporting Routes
Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReport'])->name('reports.profit_loss'); // New P&L route
Route::get('/reports/expenses', [ReportController::class, 'expenseReport'])->name('reports.expenses');
Route::get('/reports/receivables', [ReportController::class, 'receivablesReport'])->name('reports.receivables');
Route::get('/reports/inventory-valuation', [ReportController::class, 'inventoryValuationReport'])->name('reports.inventory_valuation');
Route::get('/reports/product-performance', [ReportController::class, 'productPerformanceReport'])->name('reports.product_performance');
Route::get('/reports/credit', [ReportController::class, 'creditReport'])->name('reports.credit');
Route::get('/reports/cashflow', [ReportController::class, 'cashFlowReport'])->name('reports.cashflow');
Route::get('/reports/services', [ReportController::class, 'serviceReport'])->name('reports.services');
Route::get('/reports/{report}/excel', [ReportController::class, 'export'])->whereIn('report',['sales','stock','profit-loss','general','expenses','receivables','inventory','product-performance','credit','cashflow','services'])->name('reports.excel');
Route::get('/reports/sales-data', [ReportController::class, 'getSalesData'])->name('reports.sales.data');
Route::get('/reports/sales-summary', [ReportController::class, 'getSalesSummary'])->name('reports.sales.summary');
//Route::get('/index', [UserController::class, 'index'])->name('users.index');
//Route::get('/users', [UserController::class, 'create'])->name('users.create');
//Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
//Route::get('/edit', [UserController::class, 'edit'])->name('users.edit');
//Route::put('/destroy', [UserController::class, 'destroy'])->name('users.destroy');
//Route::put('/update', [UserController::class, 'update'])->name('users.update');
// Display user creation form
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/create_permission', [RoleController::class, 'createPermission'])->name('roles.create_permission');
Route::post('/store_permission', [RoleController::class, 'storePermission'])->name('roles.store_permission');
Route::resource('roles', RoleController::class);
Route::post('/brands/bulk-upload', [BrandController::class, 'bulkUpload'])
    ->name('brands.bulk.upload');

Route::get('/brands/bulk-template', [BrandController::class, 'bulkTemplate'])
    ->name('brands.bulk.template');
Route::resource('brands', BrandController::class);
Route::resource('expenses', ExpenseController::class);

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/general',          [ReportController::class, 'generalReport'])         ->name('general');
    Route::get('/general/download', [ReportController::class, 'downloadGeneralReport']) ->name('general.download');
    Route::post('/general/email',   [ReportController::class, 'sendGeneralReportEmail'])->name('general.email');
});


// To view all installment plans






require __DIR__.'/auth.php';
