<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\ProviderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NoteVoucherController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserDeptController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\OrderReportController;
use App\Http\Controllers\Admin\SalesReturnReportController;
use App\Http\Controllers\Admin\PurchaseReturnReportController;
use App\Http\Controllers\Admin\PurchaseReportController;
use App\Http\Controllers\Admin\CustomerReportController;
use App\Http\Controllers\Admin\EventReportController;
use App\Http\Controllers\Admin\WarehouseMovementReportController;
use App\Http\Controllers\Admin\SalesReturnController;
use App\Http\Controllers\Admin\DistributionPointSalesReportController;
use App\Http\Controllers\Admin\SellerSalesController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Permission\Models\Permission;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (!defined('PAGINATION_COUNT')) {
    define('PAGINATION_COUNT', 11);
}

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group(['prefix' => 'admin', 'middleware' => ['auth:web', 'role:admin']], function () {

        // API Routes (inside localization scope for proper locale detection)
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/products/{productId}/available-quantity', [ProductController::class, 'availableQuantity'])->name('products.available-quantity');
        Route::get('/sellers/search', [SellerController::class, 'search'])->name('sellers.search');
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/sellers/{sellerId}/events', [OrderController::class, 'getSellerEvents'])->name('sellers.events');
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');


        /*         start  update login admin                 */
        Route::get('/admin/edit/{id}', [LoginController::class, 'editlogin'])->name('admin.login.edit');
        Route::post('/admin/update/{id}', [LoginController::class, 'updatelogin'])->name('admin.login.update');
        /*         end  update login admin                */

        /// Role and permission
        Route::resource('employee', 'App\Http\Controllers\Admin\EmployeeController', ['as' => 'admin']);
        Route::get('role', 'App\Http\Controllers\Admin\RoleController@index')->name('admin.role.index');
        Route::get('role/create', 'App\Http\Controllers\Admin\RoleController@create')->name('admin.role.create');
        Route::get('role/{id}/edit', 'App\Http\Controllers\Admin\RoleController@edit')->name('admin.role.edit');
        Route::patch('role/{id}', 'App\Http\Controllers\Admin\RoleController@update')->name('admin.role.update');
        Route::post('role', 'App\Http\Controllers\Admin\RoleController@store')->name('admin.role.store');
        Route::post('admin/role/delete', 'App\Http\Controllers\Admin\RoleController@delete')->name('admin.role.delete');

        Route::get('/permissions/{guard_name}', function ($guard_name) {
            return response()->json(Permission::where('guard_name', $guard_name)->get());
        });



        // Resource Route
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('settings', SettingController::class);
        Route::resource('sellers', SellerController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('countries', CountryController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('providers', ProviderController::class);
        Route::resource('warehouses', WarehouseController::class);
        Route::get('warehouses/{id}/quantities', [WarehouseController::class, 'quantities'])->name('warehouses.quantities');
        Route::resource('noteVouchers', NoteVoucherController::class);
        Route::resource('user_depts', UserDeptController::class);
        Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);

        // Purchase Actions
        Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])->name('purchases.confirm');
        Route::post('purchases/{purchase}/mark-as-received', [PurchaseController::class, 'markAsReceived'])->name('purchases.mark-as-received');
        Route::post('purchases/responses/{response}/approve', [PurchaseController::class, 'approveResponse'])->name('purchases.responses.approve');
        Route::post('purchases/responses/{response}/reject', [PurchaseController::class, 'rejectResponse'])->name('purchases.responses.reject');

        // Sales Returns
        Route::prefix('returns')->name('admin.')->group(function () {
            Route::get('search-orders', [SalesReturnController::class, 'searchOrders'])->name('sales-returns.search-orders');
            Route::resource('sales-returns', SalesReturnController::class)->names([
                'index' => 'sales-returns.index',
                'create' => 'sales-returns.create',
                'store' => 'sales-returns.store',
                'show' => 'sales-returns.show',
                'edit' => 'sales-returns.edit',
                'update' => 'sales-returns.update',
                'destroy' => 'sales-returns.destroy',
            ]);
        });

        // Purchase Returns
        Route::prefix('returns')->name('admin.')->group(function () {
            Route::get('search-purchases', [PurchaseReturnController::class, 'searchPurchases'])->name('purchase-returns.search-purchases');
            Route::resource('purchase-returns', PurchaseReturnController::class)->names([
                'index' => 'purchase-returns.index',
                'create' => 'purchase-returns.create',
                'store' => 'purchase-returns.store',
                'show' => 'purchase-returns.show',
                'edit' => 'purchase-returns.edit',
                'update' => 'purchase-returns.update',
                'destroy' => 'purchase-returns.destroy',
            ]);
        });

        // Additional routes for user debts
        Route::post('user_depts/{userDept}/make_payment', [UserDeptController::class, 'makePayment'])->name('user_depts.make_payment');
        Route::get('user_summary/{userId}', [UserDeptController::class, 'userSummary'])->name('user_depts.user_summary');

        // Reports
        Route::get('reports/note-vouchers', [ReportController::class, 'noteVouchersReport'])->name('admin.reports.noteVouchers');
        Route::get('reports/warehouse-movement', [WarehouseMovementReportController::class, 'index'])->name('admin.reports.warehouseMovement');
        Route::get('reports/warehouse-movement/{id}', [WarehouseMovementReportController::class, 'show'])->name('admin.reports.warehouseMovement.show');
        Route::get('reports/orders', [OrderReportController::class, 'index'])->name('admin.reports.orders');
        Route::get('reports/sales-returns', [SalesReturnReportController::class, 'index'])->name('admin.reports.salesReturns');
        Route::get('reports/purchase-returns', [PurchaseReturnReportController::class, 'index'])->name('admin.reports.purchaseReturns');
        Route::get('reports/purchases', [PurchaseReportController::class, 'index'])->name('admin.reports.purchases');
        Route::get('reports/customers', [CustomerReportController::class, 'index'])->name('admin.reports.customers');
        Route::get('reports/customers/search', [CustomerReportController::class, 'search'])->name('admin.customers.search');
        Route::get('reports/customers/{customerId}/data', [CustomerReportController::class, 'getCustomerData'])->name('admin.customers.report.data');
        Route::get('reports/events', [EventReportController::class, 'index'])->name('admin.reports.events');
        Route::get('reports/events/search', [EventReportController::class, 'search'])->name('admin.events.search');
        Route::get('reports/events/{eventId}/data', [EventReportController::class, 'getEventData'])->name('admin.events.report.data');
        Route::get('reports/providers/export', 'App\Http\Controllers\Admin\ProvidersReportController@export')->name('admin.reports.providers.export');
        Route::get('reports/providers', 'App\Http\Controllers\Admin\ProvidersReportController@index')->name('admin.reports.providers.index');
        Route::get('reports/providers/search', 'App\Http\Controllers\Admin\ProvidersReportController@search')->name('admin.providers.search');
        Route::get('reports/providers/{providerId}/data', 'App\Http\Controllers\Admin\ProvidersReportController@getProviderData')->name('admin.providers.report.data');
        Route::get('reports/providers/{providerId}/book-requests', 'App\Http\Controllers\Admin\ProvidersReportController@getBookRequestsData')->name('admin.reports.providers.bookRequests');
        Route::get('reports/providers/{providerId}/purchases', 'App\Http\Controllers\Admin\ProvidersReportController@getPurchasesData')->name('admin.reports.providers.purchases');
        Route::get('reports/providers/{providerId}/distribution', 'App\Http\Controllers\Admin\ProvidersReportController@getDistributionData')->name('admin.reports.providers.distribution');
        Route::get('reports/providers/{providerId}/sales-by-warehouse', 'App\Http\Controllers\Admin\ProvidersReportController@getSalesByWarehouse')->name('admin.reports.providers.salesByWarehouse');
        Route::get('reports/providers/{providerId}/refunds', 'App\Http\Controllers\Admin\ProvidersReportController@getRefundsData')->name('admin.reports.providers.refunds');
        Route::get('reports/providers/{providerId}/sellers-payments', 'App\Http\Controllers\Admin\ProvidersReportController@getSellersPaymentsData')->name('admin.reports.providers.sellersPayments');
        Route::get('reports/providers/{providerId}/stock-balance', 'App\Http\Controllers\Admin\ProvidersReportController@getStockBalanceData')->name('admin.reports.providers.stockBalance');

        // Distribution Point Sales Report
        Route::get('reports/distribution-point-sales', [DistributionPointSalesReportController::class, 'index'])->name('admin.reports.distributionPointSales.index');
        Route::get('reports/distribution-point-sales/search', [DistributionPointSalesReportController::class, 'search'])->name('admin.reports.distributionPointSales.search');
        Route::get('reports/distribution-point-sales/data', [DistributionPointSalesReportController::class, 'getData'])->name('admin.reports.distributionPointSales.data');

        // Sales Details
        Route::get('sales/{id}/details', [DistributionPointSalesReportController::class, 'showSaleDetails'])->name('admin.sales.details');

        // Seller Product Requests
        Route::prefix('sellerProductRequests')->name('sellerProductRequests.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SellerProductRequestController::class, 'index'])->name('index');
            Route::get('/{sellerProductRequest}', [\App\Http\Controllers\Admin\SellerProductRequestController::class, 'show'])->name('show');
            Route::get('/{sellerProductRequest}/approve', [\App\Http\Controllers\Admin\SellerProductRequestController::class, 'showApprovalForm'])->name('approve.form');
            Route::post('/{sellerProductRequest}/approve', [\App\Http\Controllers\Admin\SellerProductRequestController::class, 'approve'])->name('approve');
            Route::post('/{sellerProductRequest}/reject', [\App\Http\Controllers\Admin\SellerProductRequestController::class, 'reject'])->name('reject');
        });

        // Seller Sales Management
        Route::prefix('seller-sales')->name('admin.seller-sales.')->group(function () {
            Route::get('/', [SellerSalesController::class, 'index'])->name('index');
            Route::get('/create', [SellerSalesController::class, 'create'])->name('create');
            Route::post('/', [SellerSalesController::class, 'store'])->name('store');
            Route::get('/{id}', [SellerSalesController::class, 'show'])->name('show');
            Route::get('/get-products/{sellerId}', [SellerSalesController::class, 'getSellerProducts'])->name('get-products');
        });
    });
});



Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'guest'], function () {
    Route::get('login', [LoginController::class, 'show_login_view'])->name('admin.showlogin');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
});
