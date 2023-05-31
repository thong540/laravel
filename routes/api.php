<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\PostController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['checkAge'])->group(function () {
    //
    Route::get('/test', 'App\Http\Controllers\TestController@functionTest');


});

Route::middleware(['checkToken'])->group(function () {

    Route::get('/user', 'App\Http\Controllers\UserController@getAllUsers');
    Route::post('/create-order', 'App\Http\Controllers\OrderController@createOrder');
    Route::post('/create-user', 'App\Http\Controllers\UserController@createUser');
    Route::post('/update-user', 'App\Http\Controllers\UserController@updateUser');
    Route::delete('/delete-user', 'App\Http\Controllers\UserController@deleteUser');

    Route::post('/create-category', 'App\Http\Controllers\CategoryController@createCategory');
    Route::post('/update-category', 'App\Http\Controllers\CategoryController@updateCategory');
    Route::delete('/delete-category/', 'App\Http\Controllers\CategoryController@deleteCategory');

    Route::post('/create-product', '\App\Http\Controllers\ProductController@createProduct');
    Route::post('/update-product', '\App\Http\Controllers\ProductController@updateProduct');
    Route::delete('/delete-product', '\App\Http\Controllers\ProductController@deleteProduct');

    Route::post('/create-customer', 'App\Http\Controllers\CustomerController@createCustomer');
    Route::post('/update-customer', 'App\Http\Controllers\CustomerController@updateCustomer');
    Route::get('/customer', 'App\Http\Controllers\CustomerController@getAllCustomers');
    Route::delete('/delete-customer', 'App\Http\Controllers\CustomerController@deleteCustomer');

    Route::post('/update-order', 'App\Http\Controllers\OrderController@updateOrder');
    Route::delete('/delete-order', 'App\Http\Controllers\OrderController@deleteOrder');


});

Route::get('/order', 'App\Http\Controllers\OrderController@getAllOrders');
Route::get('/category', 'App\Http\Controllers\CategoryController@getAllCategories');
Route::get('/product', '\App\Http\Controllers\ProductController@getAllProducts');

Route::post('/login', '\App\Http\Controllers\Auth1Controller@login');
Route::post('/register', '\App\Http\Controllers\Auth1Controller@register');

//Route::post('posts', 'App\Http\Controllers\Api\PostController@test');
//Route::apiResource('posts',PostController::class);
