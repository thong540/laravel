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

Route::middleware(['auth'])->group(function () {

});
Route::post('/category', 'App\Http\Controllers\CategoriesController@createCategory');
Route::put('/category/{id}', 'App\Http\Controllers\CategoriesController@updateCategory');
Route::get('/category', 'App\Http\Controllers\CategoriesController@getAllCategory');
Route::delete('/category/{id}', 'App\Http\Controllers\CategoriesController@deleteCategory');

Route::post('/product', '\App\Http\Controllers\ProductController@addNewProduct');
Route::get('/product', '\App\Http\Controllers\ProductController@getAllProducts');
Route::put('/product/{id}', '\App\Http\Controllers\ProductController@updateProduct');
Route::delete('/product/{id}', '\App\Http\Controllers\ProductController@deleteProduct');

Route::post('/login', '\App\Http\Controllers\LoginController@login');
Route::post('/register', '\App\Http\Controllers\RegisterController@register');

//Route::post('posts', 'App\Http\Controllers\Api\PostController@test');
//Route::apiResource('posts',PostController::class);
