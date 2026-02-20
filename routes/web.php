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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])
    ->name('dashboard');

// Optional: Edit/Delete phone routes
Route::middleware(['auth'])->group(function () {
    Route::get('/phones/{phone}/edit', [DashboardController::class, 'editPhone'])->name('phones.edit');
    Route::delete('/phones/{phone}', [DashboardController::class, 'deletePhone'])->name('phones.destroy');
    Route::put('/phones/{phone}', [DashboardController::class, 'updatePhone'])->name('phones.update'); // <--- Add this

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
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show'); // For viewing a single sale detail

// Installment Routes
Route::get('/installments/{installmentPlan}/pay', [InstallmentController::class, 'showPaymentForm'])->name('installments.pay.form');
Route::post('/installments/{installmentPlan}/pay', [InstallmentController::class, 'recordPayment'])->name('installments.pay.store');
Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
Route::post('/installment/payment', [InstallmentController::class, 'store'])->name('installment.payment.store');
// Reporting Routes
Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReport'])->name('reports.profit_loss'); // New P&L route
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
Route::resource('brands', BrandController::class);
Route::resource('expenses', ExpenseController::class);




Route::get('/dashboard', [ReportController::class, 'home'])->name('dashboard');
// To view all installment plans






require __DIR__.'/auth.php';
