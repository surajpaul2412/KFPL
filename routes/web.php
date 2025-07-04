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
use App\Http\Controllers\Admin\SenderemailController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Trader\TicketController as TraderTicketController;
use App\Http\Controllers\Admin\MisController as AdminMisController;
use App\Http\Controllers\Account\MisController as AccountMisController;
use App\Http\Controllers\Dealer\MisController as DealerMisController;
use App\Http\Controllers\Ops\MisController as OpsMisController;
use App\Http\Controllers\Trader\MisController;
use App\Http\Controllers\Ops\TicketController as OpsTicketController;
use App\Http\Controllers\Account\TicketController as AccountsTicketController;
use App\Http\Controllers\Dealer\TicketController as DealerTicketController;
use App\Http\Controllers\Dealer\QuickTicketController as DealerQuickTicketController;
use App\Http\Controllers\Trader\QuickTicketController as TraderQuickTicketController;
use App\Http\Controllers\Admin\QuickTicketController as AdminQuickTicketController;
use App\Http\Controllers\Admin\EmailtemplateController as EmailtemplateController;

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
Route::get('/check-new-ticket', [App\Http\Controllers\TicketController::class, 'checkNewTicket']);
Route::get('/check-new-quick-ticket', [App\Http\Controllers\TicketController::class, 'checkNewQuickTicket']);


Route::get('/check-ops-ticket', [App\Http\Controllers\TicketController::class, 'checkOpsTicket']);
Route::get('/check-accounts-ticket', [App\Http\Controllers\TicketController::class, 'checkAccountsTicket']);
Route::get('/check-dealer-ticket', [App\Http\Controllers\TicketController::class, 'checkDealerTicket']);

// Admin Routes
Route::middleware(['auth', 'isAdmin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // MIS
	Route::get('/admin/mis/ajax', [AdminMisController::class, 'getMisData'])->name('admin.mis.ajax');
    Route::get('/admin/mis/csvexport', [AdminMisController::class, 'exportMisDataToCSV'])->name('admin.mis.csvexport');

    Route::resource('/admin/mis', AdminMisController::class)->names([
        'index' => 'admin.mis.index',
        'show' => 'admin.mis.show',
    ]);
    // User (Employee) Management
    Route::resource('/admin/employees', UserController::class);
    Route::get('/admin/toggle/status', [UserController::class, 'togglestatus'])->name('admin.employee.togglestatus');
    // Ticket
    Route::post('/admin-calculate-purchase-nav', [AdminDashboardController::class, 'calculatePurchaseNav'])->name('admin-calculate.purchase.nav');
    Route::delete('/admin/tickets/{ticket}', [AdminTicketController::class, 'destroy'])->name('admin.tickets.destroy');
    Route::resource('/admin/tickets', AdminTicketController::class)->names([
        'index' => 'admin.tickets.index',
        'create' => 'admin.tickets.create',
        'store' => 'admin.tickets.store',
        'edit' => 'admin.tickets.edit',
        'update' => 'admin.tickets.update',
        'show' => 'admin.tickets.show',
        'destroy' => 'admin.tickets.destroy'
    ])->except([
        // 'destroy' // Exclude the destroy method from the resourceful routes
    ]);
    Route::get('/admin/tickets/{ticket}/mail', [AdminTicketController::class, 'mail'])->name('admin.tickets.mail');
	Route::get('/admin/tickets/{ticket}/mailtoself', [AdminTicketController::class, 'mailtoself'])->name('admin.tickets.mailtoself');
	
    Route::get('/admin/tickets/{ticket}/skip', [AdminTicketController::class, 'skip'])->name('admin.tickets.skip');
    Route::get('/admin/tickets/{ticket}/statusUpdate', [AdminTicketController::class, 'statusUpdate'])->name('admin.tickets.statusUpdate');
    // AMC Management
    Route::resource('/admin/amcs', AmcController::class);
    Route::resource('/admin/senderemail', SenderemailController::class);
    Route::post('/admin/upload-securities', [SecurityController::class, 'uploadCSV']);
    Route::get('/admin/download-csv', [SecurityController::class, 'downloadCSV'])->name('download.csv');
    Route::resource('/admin/securities', SecurityController::class);
    Route::resource('/admin/emailtemplates', EmailtemplateController::class)->names('admin.emailtemplates');
    // Disputes
    Route::resource('/admin/disputes', DisputeController::class);
    // quick ticket
    Route::resource('/admin/quick_tickets', AdminQuickTicketController::class)->names([
        'index' => 'admin.quick_tickets.index',
        'show' => 'admin.quick_tickets.show',
    ]);
});

// Dealer Dashboard
Route::middleware(['auth', 'isDealer'])->group(function () {
    Route::get('/dealer/dashboard', [DealerDashboardController::class, 'index'])->name('dealer.dashboard');
    Route::post('/calculate-purchase-nav', [DealerDashboardController::class, 'calculatePurchaseNav'])->name('calculate.purchase.nav');
    Route::post('/dealer-calculate-purchase-nav', [DealerDashboardController::class, 'calculatePurchaseNavByRequest'])->name('dealer-calculate.purchase.nav');
    Route::resource('/dealer/tickets', DealerTicketController::class)->names([
        'index' => 'dealer.tickets.index',
        'edit' => 'dealer.tickets.edit',
        'show' => 'dealer.tickets.show',
        'update' => 'dealer.tickets.update',
    ]);
    Route::get('/dealer/tickets/{ticket}/statusUpdate', [DealerTicketController::class, 'statusUpdate'])->name('dealer.tickets.statusUpdate');
    // MIS
    Route::get('/dealer/mis/ajax', [DealerMisController::class, 'getMisData'])->name('dealer.mis.ajax');
    Route::resource('/dealer/mis', DealerMisController::class)->names([
        'index' => 'dealer.mis.index',
        'show' => 'dealer.mis.show',
    ]);
    // quick ticket
    Route::resource('/dealer/quick_tickets', DealerQuickTicketController::class)->names([
        'index' => 'dealer.quick_tickets.index',
        'create' => 'dealer.quick_tickets.create',
        'store' => 'dealer.quick_tickets.store',
        'edit' => 'dealer.quick_tickets.edit',
        'show' => 'dealer.quick_tickets.show',
        'update' => 'dealer.quick_tickets.update',
    ]);
});

// Accounts Dashboard
Route::middleware(['auth', 'isAccounts'])->group(function () {
    Route::get('/accounts/dashboard', [AccountDashboardController::class, 'index'])->name('accounts.dashboard');
    // MIS
    Route::get('/accounts/mis/ajax', [AccountMisController::class, 'getMisData'])->name('accounts.mis.ajax');
    Route::resource('/accounts/mis', AccountMisController::class)->names([
        'index' => 'accounts.mis.index',
        'show' => 'accounts.mis.show',
    ]);    
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
    // MIS
    Route::get('/trader/mis/ajax', [MisController::class, 'getMisData'])->name('trader.mis.ajax');
    Route::get('/trader/mis/csvexport', [MisController::class, 'exportMisDataToCSV'])->name('trader.mis.csvexport');
    Route::resource('/trader/mis', MisController::class)->names([
        'index' => 'trader.mis.index',
        'show' => 'trader.mis.show',
    ]);
    Route::resource('/trader/tickets', TraderTicketController::class)->names([
        'index' => 'trader.tickets.index',
        'create' => 'trader.tickets.create',
        'store' => 'trader.tickets.store',
        'edit' => 'trader.tickets.edit',
        'update' => 'trader.tickets.update',
        'show' => 'trader.tickets.show',
    ]);
    Route::get('/trader/get-security-details/{id}', [TraderTicketController::class, 'getSecurityDetails'])
    ->name('trader.get-security-details');
    // quick ticket
    Route::resource('/trader/quick_tickets', TraderQuickTicketController::class)->names([
        'index' => 'trader.quick_tickets.index',
        'show' => 'trader.quick_tickets.show',
    ]);
});

// Ops Dashboard
Route::middleware(['auth', 'isOps'])->group(function () {
    Route::get('/ops/dashboard', [OpsDashboardController::class, 'index'])->name('ops.dashboard');
    // MIS
    Route::get('/ops/mis/ajax', [OpsMisController::class, 'getMisData'])->name('ops.mis.ajax');
    Route::get('/ops/mis/csvexport', [OpsMisController::class, 'exportMisDataToCSV'])->name('ops.mis.csvexport');

    Route::resource('/ops/mis', OpsMisController::class)->names([
        'index' => 'ops.mis.index',
        'show' => 'ops.mis.show',
    ]);
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
	Route::get('/ops/tickets/{ticket}/mailtoself', [OpsTicketController::class, 'mailtoself'])->name('ops.tickets.mailtoself');
    Route::get('/ops/tickets/{ticket}/skip', [OpsTicketController::class, 'skip'])->name('ops.tickets.skip');
});

// Backoffice Dashboard
Route::middleware(['auth', 'isBackoffice'])->group(function () {
    Route::get('/backoffice/dashboard', [BackOfficeDashboardController::class, 'index'])->name('backoffice.dashboard');
});


// Ajax
Route::get('/get-security-details/{id}', [TraderTicketController::class, 'getSecurityDetails'])->name('get-security-details');
