<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashewController;
use App\Http\Controllers\SaleController; // Import SaleController
use App\Http\Controllers\InstallmentController; // Import InstallmentController
use App\Http\Controllers\ReportController; // Import InstallmentController
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;

use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BackupController;



Route::get('/settings/backup/run', [BackupController::class, 'runNow'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.run');

Route::get('/settings/backup/download', [BackupController::class, 'download'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.download');


Route::get('/settings/backup/run', [BackupController::class, 'runNow'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.run');

Route::post('/settings/backup/run-ajax', [BackupController::class, 'runNowAjax'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.run.ajax');

Route::get('/settings/backup/download', [BackupController::class, 'download'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.download');

Route::post('/settings/backup/dismiss', [BackupController::class, 'dismissNotify'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.dismiss');

Route::get('/', function () {
    return view('welcome');
});
Route::post('/settings/backup/run-ajax', [BackupController::class, 'runNowAjax'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('backup.run.ajax');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('dashboard');
Route::get('/settings', [SettingsController::class, 'edit'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('settings.edit');

Route::put('/settings', [SettingsController::class, 'update'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('settings.update');

// Optional: Edit/Delete phone routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cashews/{cashew}/edit', [CashewController::class, 'edit'])->name('cashews.edit');
    Route::delete('/cashews/{cashew}', [CashewController::class, 'destroy'])->name('cashews.destroy');
    Route::put('/cashews/{cashew}', [CashewController::class, 'update'])->name('cashews.update');

});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// ... other routes ...

Route::get('/cashew/receive', [CashewController::class, 'showReceiveForm'])->name('cashew.receive.form');
Route::post('/cashew/receive', [CashewController::class, 'storeReceivedcashews'])->name('cashews.receive.store');
Route::get('/cashew', [CashewController::class, 'index'])->name('cashews.index');

// Sales Routes
Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
Route::get('/sales/return', [SaleController::class, 'showReturnPage'])->name('sales.return');
Route::get('/sales/print/{id}', [SaleController::class, 'printReceipt'])->name('sales.print');
// For viewing a single sale detail

// Installment Routes
Route::get('/installments/{installmentPlan}/pay', [InstallmentController::class, 'showPaymentForm'])->name('installments.pay.form');
Route::post('/installments/{installmentPlan}/pay', [InstallmentController::class, 'recordPayment'])->name('installments.pay.store');
Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
Route::post('/installment/payment', [InstallmentController::class, 'store'])->name('installment.payment.store');
// Reporting Routes
Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
Route::post('/reports/stock/update-quantity', [ReportController::class, 'stockUpdate'])->name('reports.stock.update-quantity');


Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReport'])->name('reports.profit_loss'); // New P&L route
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
Route::resource('products', ProductController::class);
Route::resource('expenses', ExpenseController::class);

    Route::get('/reports/general',          [ReportController::class, 'generalReport'])->name('reports.general');
    Route::get('/general/download', [ReportController::class, 'downloadGeneralReport']) ->name('general.download');
    Route::post('/general/email',   [ReportController::class, 'sendGeneralReportEmail'])->name('general.email');

// To view all installment plans

Route::get('/cashews/search-for-sale', [CashewController::class, 'searchForSale'])
    ->name('cashews.searchForSale');




require __DIR__.'/auth.php';
