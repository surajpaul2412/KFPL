<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Dealer\DashboardController as DealerDashboardController;
use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Trader\DashboardController as TraderDashboardController;
use App\Http\Controllers\Ops\DashboardController as OpsDashboardController;
use App\Http\Controllers\BackOffice\DashboardController as BackOfficeDashboardController;

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

Route::get('/', function () {
    echo "TEST";
	//return view('welcome');
});
//->middleware('isAdmin');

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'isDealer'])->group(function () {
    Route::get('/dealer/dashboard', [DealerDashboardController::class, 'index'])->name('dealer.dashboard');
});

Route::middleware(['auth', 'isAccounts'])->group(function () {
    Route::get('/accounts/dashboard', [AccountDashboardController::class, 'index'])->name('accounts.dashboard');
});

Route::middleware(['auth', 'isTrader'])->group(function () {
    Route::get('/trader/dashboard', [TraderDashboardController::class, 'index'])->name('trader.dashboard');
});

Route::middleware(['auth', 'isOps'])->group(function () {
    Route::get('/ops/dashboard', [OpsDashboardController::class, 'index'])->name('ops.dashboard');
});

Route::middleware(['auth', 'isBackoffice'])->group(function () {
    Route::get('/backoffice/dashboard', [BackOfficeDashboardController::class, 'index'])->name('backOffice.dashboard');
});
