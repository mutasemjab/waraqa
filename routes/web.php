<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\TripTypeController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserSalesController;
use App\Models\Booking;
use App\Models\User;
use Asciisd\Knet\Http\Controllers\ReceiptController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use IZaL\Knet\KnetBilling;
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
        // User Login Routes
        Route::get('/user/login', [UserAuthController::class, 'showUserLoginForm'])->name('user.login');
        Route::post('/user/login', [UserAuthController::class, 'loginUser']);

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

    // User Dashboard Routes (using 'web' guard - the default)
    Route::middleware(['auth:web'])->prefix('user')->name('user.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        
        // Profile Management
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [UserDashboardController::class, 'updatePassword'])->name('password.update');
        
        // Orders Management
        Route::get('/orders', [UserDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [UserDashboardController::class, 'orderShow'])->name('orders.show');
        
        // Debts Management
        Route::get('/debts', [UserDashboardController::class, 'debts'])->name('debts');
        
        // Sales Management
        Route::get('/sales', [UserSalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [UserSalesController::class, 'create'])->name('sales.create');
        Route::post('/sales', [UserSalesController::class, 'store'])->name('sales.store');
        Route::get('/sales/{id}', [UserSalesController::class, 'show'])->name('sales.show');
        Route::get('/sales-report', [UserSalesController::class, 'salesReport'])->name('sales.report');
        
        // Warehouse Management
        Route::get('/warehouse', [UserSalesController::class, 'warehouse'])->name('warehouse');
        
        // Analytics & Reports
        Route::get('/analytics', [UserDashboardController::class, 'analytics'])->name('analytics');
        Route::get('/report', [UserDashboardController::class, 'generateReport'])->name('report');
        
        // Notifications
        Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications');
        
 
    });

    // Provider Dashboard Routes (using 'provider' guard)
Route::middleware(['auth:provider'])->prefix('provider')->name('provider.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [ProviderDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Products Management
    Route::get('/products', [ProviderDashboardController::class, 'products'])->name('products');
    Route::get('/products/{product}/details', [ProviderDashboardController::class, 'productDetails'])->name('products.details');
    
    // Orders Management
    Route::get('/orders', [ProviderDashboardController::class, 'orders'])->name('orders');
    
    // Users/Customers Management
    Route::get('/users', [ProviderDashboardController::class, 'users'])->name('users');
    Route::get('/users/{user}/details', [ProviderDashboardController::class, 'userDetails'])->name('users.details');
    
    // Analytics & Reports
    Route::get('/analytics', [ProviderDashboardController::class, 'analytics'])->name('analytics');
});

});