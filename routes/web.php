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
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Trader\TicketController as TraderTicketController;
use App\Http\Controllers\Ops\TicketController as OpsTicketController;
use App\Http\Controllers\Account\TicketController as AccountsTicketController;
use App\Http\Controllers\Dealer\TicketController as DealerTicketController;

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
    Route::get('/admin/toggle/status', [UserController::class, 'togglestatus'])->name('admin.employee.togglestatus');
    // Ticket
    Route::resource('/admin/tickets', AdminTicketController::class)->names([
        'index' => 'admin.tickets.index',
        'create' => 'admin.tickets.create',
        'store' => 'admin.tickets.store',
        'edit' => 'admin.tickets.edit',
        'update' => 'admin.tickets.update',
    ]);
    // AMC Management
    Route::resource('/admin/amcs', AmcController::class);
    Route::post('/admin/upload-securities', [SecurityController::class, 'uploadCSV']);
    Route::resource('/admin/securities', SecurityController::class);
});

// Dealer Dashboard
Route::middleware(['auth', 'isDealer'])->group(function () {
    Route::get('/dealer/dashboard', [DealerDashboardController::class, 'index'])->name('dealer.dashboard');
    Route::resource('/dealer/tickets', DealerTicketController::class)->names([
        'index' => 'dealer.tickets.index',
        'edit' => 'dealer.tickets.edit',
        'show' => 'dealer.tickets.show',
        'update' => 'dealer.tickets.update',
    ]);
    Route::get('/dealer/tickets/{ticket}/statusUpdate', [DealerTicketController::class, 'statusUpdate'])->name('dealer.tickets.statusUpdate');
});

// Accounts Dashboard
Route::middleware(['auth', 'isAccounts'])->group(function () {
    Route::get('/accounts/dashboard', [AccountDashboardController::class, 'index'])->name('accounts.dashboard');
    Route::resource('/accounts/tickets', AccountsTicketController::class)->names([
        'index' => 'accounts.tickets.index',
        'create' => 'accounts.tickets.create',
        'store' => 'accounts.tickets.store',
        'edit' => 'accounts.tickets.edit',
        'update' => 'accounts.tickets.update',
        'destroy' => 'accounts.tickets.destroy',
    ]);
});

// Trader Dashboard
Route::middleware(['auth', 'isTrader'])->group(function () {
    Route::get('/trader/dashboard', [TraderDashboardController::class, 'index'])->name('trader.dashboard');
    Route::resource('/trader/tickets', TraderTicketController::class)->names([
        'index' => 'trader.tickets.index',
        'create' => 'trader.tickets.create',
        'store' => 'trader.tickets.store',
        'edit' => 'trader.tickets.edit',
        'update' => 'trader.tickets.update',
    ]);
    Route::get('/trader/get-security-details/{id}', [TraderTicketController::class, 'getSecurityDetails'])
    ->name('trader.get-security-details');
});

// Ops Dashboard
Route::middleware(['auth', 'isOps'])->group(function () {
    Route::get('/ops/dashboard', [OpsDashboardController::class, 'index'])->name('ops.dashboard');
    Route::resource('/ops/tickets', OpsTicketController::class)->names([
        'index' => 'ops.tickets.index',
        'create' => 'ops.tickets.create',
        'store' => 'ops.tickets.store',
        'show' => 'ops.tickets.show',
        'edit' => 'ops.tickets.edit',
        'update' => 'ops.tickets.update',
        'destroy' => 'ops.tickets.destroy',
    ]);
    Route::get('/ops/tickets/{ticket}/mail', [OpsTicketController::class, 'mail'])->name('ops.tickets.mail');
});

// Backoffice Dashboard
Route::middleware(['auth', 'isBackoffice'])->group(function () {
    Route::get('/backoffice/dashboard', [BackOfficeDashboardController::class, 'index'])->name('backOffice.dashboard');
});


// Ajax
Route::get('/get-security-details/{id}', [TraderTicketController::class, 'getSecurityDetails'])->name('get-security-details');
