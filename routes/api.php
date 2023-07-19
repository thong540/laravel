<?php


use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['checkToken'])->group(function () {

    Route::get('/user', 'App\Http\Controllers\UserController@getListUser');
    Route::post('/create-user', 'App\Http\Controllers\UserController@createUser');
    Route::put('/update-user', 'App\Http\Controllers\UserController@updateUser');
    Route::delete('/delete-user', 'App\Http\Controllers\UserController@deleteUser');

    Route::post('/create-category', 'App\Http\Controllers\CategoryController@createCategory');
    Route::post('/update-category', 'App\Http\Controllers\CategoryController@updateCategory');
    Route::delete('/delete-category', 'App\Http\Controllers\CategoryController@deleteCategory');

    Route::post('/create-product', 'App\Http\Controllers\ProductController@createProduct');
    Route::post('/update-product', 'App\Http\Controllers\ProductController@updateProduct');
    Route::delete('/delete-product', 'App\Http\Controllers\ProductController@deleteProduct');

    Route::post('/create-customer', 'App\Http\Controllers\CustomerController@createCustomer');
    Route::post('/update-customer', 'App\Http\Controllers\CustomerController@updateCustomer');
    Route::get('/customer', 'App\Http\Controllers\CustomerController@getListCustomer');
    Route::delete('/delete-customer', 'App\Http\Controllers\CustomerController@deleteCustomer');

    Route::post('/create-order', 'App\Http\Controllers\OrderController@createOrder');
    Route::delete('/delete-order', 'App\Http\Controllers\OrderController@deleteOrder');
    Route::get('/get-detail-order', 'App\Http\Controllers\OrderController@getDetailOrder');
    Route::post('/update-status-order', 'App\Http\Controllers\OrderController@updateStatusOrder');
    Route::post('/update-products-in-order', 'App\Http\Controllers\OrderController@updateProductInOrder');
    Route::put('/update-order', 'App\Http\Controllers\OrderController@updateOrder');

    Route::post('/find-order-by-many-field', 'App\Http\Controllers\OrderController@findOrderByManyField');
    Route::post('/find-order-by-customer', 'App\Http\Controllers\OrderController@findOrderByInforCustomer');


});

Route::get('/order', 'App\Http\Controllers\OrderController@getAllOrders');
Route::post('/get-status-order', 'App\Http\Controllers\OrderController@getStatusOrder');

Route::get('/category', 'App\Http\Controllers\CategoryController@getListCategory');
Route::get('/product', 'App\Http\Controllers\ProductController@getAllProducts');

Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/register', 'App\Http\Controllers\AuthController@register');
Route::get('/get-role', 'App\Http\Controllers\RoleController@getListRole');

Route::post('/test', 'App\Http\Controllers\TestController@test');
//Route::post('posts', 'App\Http\Controllers\Api\PostController@test');
//Route::apiResource('posts',PostController::class);
//Route::post('/edit-information-customer-in-order','\App\Http\Controllers\OrderController@updateProductInOrder');
