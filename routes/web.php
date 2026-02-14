<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserSalesController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group whichf
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    // Redirect root to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Authentication Routes (accessible when not logged in)
    Route::middleware('guest')->group(function () {
        // Seller Login Routes
        Route::get('/seller/login', [UserAuthController::class, 'showUserLoginForm'])->name('user.login');
        Route::post('/seller/login', [UserAuthController::class, 'loginUser']);

        // Provider Login Routes
        Route::get('/provider/login', [UserAuthController::class, 'showProviderLoginForm'])->name('provider.login');
        Route::post('/provider/login', [UserAuthController::class, 'loginProvider']);

        // Redirect old login route to user login
        Route::get('/login', function () {
            return redirect()->route('user.login');
        })->name('login');
    });

    // Logout route (accessible when logged in)
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

    // Seller Dashboard Routes (using 'web' guard with 'seller' role)
    Route::middleware(['auth:web', 'role:seller'])->prefix('seller')->name('user.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Profile Management
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [UserDashboardController::class, 'updatePassword'])->name('password.update');

        // Orders Management
        Route::get('/orders', [UserDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [UserDashboardController::class, 'orderShow'])->name('orders.show');

        // Sales Management
        Route::get('/sales', [UserSalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [UserSalesController::class, 'create'])->name('sales.create');
        Route::post('/sales', [UserSalesController::class, 'store'])->name('sales.store');
        Route::get('/sales/{id}', [UserSalesController::class, 'show'])->name('sales.show');
        Route::get('/sales-report', [UserSalesController::class, 'salesReport'])->name('sales.report');

        // Warehouse Management
        Route::get('/warehouse', [UserSalesController::class, 'warehouse'])->name('warehouse');
        Route::post('/warehouse/create', [UserSalesController::class, 'createWarehouse'])->name('warehouse.create');

        // Reports
        Route::get('/report', [UserDashboardController::class, 'generateReport'])->name('report');

        // Notifications
        Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications');

        // Seller Product Requests
        Route::resource('sellerProductRequests', \App\Http\Controllers\User\SellerProductRequestController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    // Provider Dashboard Routes (using 'web' guard with 'provider' role)
    Route::middleware(['auth:web', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');

        // Profile Management
        Route::get('/profile', [ProviderDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update');

        // Orders Management
        Route::get('/orders', [ProviderDashboardController::class, 'orders'])->name('orders');
        Route::get('/purchases', [ProviderDashboardController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/{id}', [ProviderDashboardController::class, 'showPurchase'])->name('purchases.show');

        // Book Requests (Pending Book Requests list)
        Route::get('/book-requests', [ProviderDashboardController::class, 'bookRequests'])->name('bookRequests');

        // Book Requests Response
        Route::get('/bookRequests/{bookRequestItem}/respond', [
            \App\Http\Controllers\Provider\BookRequestController::class, 'createResponse'
        ])->name('bookRequests.respond');
        Route::post('/bookRequests/{bookRequestItem}/respond', [
            \App\Http\Controllers\Provider\BookRequestController::class, 'storeResponse'
        ])->name('bookRequests.storeResponse');
    });

    // Search Items Route (for search-select component)
    Route::get('/search/items', [SearchController::class, 'searchItems'])->name('search.items');
});
