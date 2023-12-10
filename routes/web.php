<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Dealer\DashboardController as DealerDashboardController;
use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Trader\DashboardController as TraderDashboardController;
use App\Http\Controllers\Ops\DashboardController as OpsDashboardController;
use App\Http\Controllers\BackOffice\DashboardController as BackOfficeDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AmcController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\TicketController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

// Redirects
Route::redirect('/register', '/login');
Route::redirect('/', '/login');

// Home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Routes
Route::middleware(['auth', 'isAdmin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // User (Employee) Management
    Route::resource('/admin/employees', UserController::class);

    // Toggle Employee STatus
    Route::get('/admin/toggle/status', [UserController::class, 'togglestatus'])->name('admin.employee.togglestatus');

    // AMC Management
    Route::resource('/admin/amcs', AmcController::class);
    Route::resource('/admin/securities', SecurityController::class);
    Route::resource('/admin/tickets', TicketController::class);
});

// Dealer Dashboard
Route::middleware(['auth', 'isDealer'])->group(function () {
    Route::get('/dealer/dashboard', [DealerDashboardController::class, 'index'])->name('dealer.dashboard');
});

// Accounts Dashboard
Route::middleware(['auth', 'isAccounts'])->group(function () {
    Route::get('/accounts/dashboard', [AccountDashboardController::class, 'index'])->name('accounts.dashboard');
});

// Trader Dashboard
Route::middleware(['auth', 'isTrader'])->group(function () {
    Route::get('/trader/dashboard', [TraderDashboardController::class, 'index'])->name('trader.dashboard');
});

// Ops Dashboard
Route::middleware(['auth', 'isOps'])->group(function () {
    Route::get('/ops/dashboard', [OpsDashboardController::class, 'index'])->name('ops.dashboard');
});

// Backoffice Dashboard
Route::middleware(['auth', 'isBackoffice'])->group(function () {
    Route::get('/backoffice/dashboard', [BackOfficeDashboardController::class, 'index'])->name('backOffice.dashboard');
});
