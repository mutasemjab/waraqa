<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\ProviderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NoteVoucherController;
use App\Http\Controllers\Admin\NoteVoucherTypeController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserDeptController;
use App\Http\Controllers\Admin\WalletTransactionController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WithdrawalRequestController;
use App\Http\Controllers\Admin\BookRequestController;
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

define('PAGINATION_COUNT',11);

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

 Route::group(['prefix'=>'admin','middleware'=>'auth:admin'],function(){

// API Routes (inside localization scope for proper locale detection)
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{productId}/available-quantity', [ProductController::class, 'availableQuantity'])->name('products.available-quantity');
 Route::get('/',[DashboardController::class,'index'])->name('admin.dashboard');
 Route::get('logout',[LoginController::class,'logout'])->name('admin.logout');


/*         start  update login admin                 */
Route::get('/admin/edit/{id}',[LoginController::class,'editlogin'])->name('admin.login.edit');
Route::post('/admin/update/{id}',[LoginController::class,'updatelogin'])->name('admin.login.update');
/*         end  update login admin                */

/// Role and permission
Route::resource('employee', 'App\Http\Controllers\Admin\EmployeeController',[ 'as' => 'admin']);
Route::get('role', 'App\Http\Controllers\Admin\RoleController@index')->name('admin.role.index');
Route::get('role/create', 'App\Http\Controllers\Admin\RoleController@create')->name('admin.role.create');
Route::get('role/{id}/edit', 'App\Http\Controllers\Admin\RoleController@edit')->name('admin.role.edit');
Route::patch('role/{id}', 'App\Http\Controllers\Admin\RoleController@update')->name('admin.role.update');
Route::post('role', 'App\Http\Controllers\Admin\RoleController@store')->name('admin.role.store');
Route::post('admin/role/delete', 'App\Http\Controllers\Admin\RoleController@delete')->name('admin.role.delete');

Route::get('/permissions/{guard_name}', function($guard_name){
    return response()->json(Permission::where('guard_name',$guard_name)->get());
});



// Resource Route
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('settings', SettingController::class);
Route::resource('users', UserController::class);
Route::resource('countries', CountryController::class);
Route::resource('orders', OrderController::class);
Route::resource('providers', ProviderController::class);
Route::resource('warehouses', WarehouseController::class);
Route::resource('noteVoucherTypes', NoteVoucherTypeController::class);
Route::resource('noteVouchers', NoteVoucherController::class);
Route::resource('user_depts', UserDeptController::class);
Route::resource('bookRequests', BookRequestController::class);

// Additional routes for user debts
Route::post('user_depts/{userDept}/make_payment', [UserDeptController::class, 'makePayment'])->name('user_depts.make_payment');
Route::get('user_summary/{userId}', [UserDeptController::class, 'userSummary'])->name('user_depts.user_summary');


});
});



Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'guest:admin'],function(){
    Route::get('login',[LoginController::class,'show_login_view'])->name('admin.showlogin');
    Route::post('login',[LoginController::class,'login'])->name('admin.login');

});







