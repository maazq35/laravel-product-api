<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;

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

Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

// public product endpoints
Route::get('products', [ProductController::class,'index']);
Route::get('products/{id}', [ProductController::class,'show']);


Route::group(['middleware'=>'auth:api'], function(){
    Route::post('logout', [AuthController::class,'logout']);
    Route::get('me', [AuthController::class,'me']);

    // product management
    Route::post('products', [ProductController::class,'store']);
    Route::put('products/{id}', [ProductController::class,'update']);
    Route::delete('products/{id}', [ProductController::class,'destroy']);
    Route::delete('product-image/{id}', [ProductController::class,'deleteImage']);

    // Cart endpoints that require auth (optional)
    Route::post('cart/add-auth', [CartController::class,'addToCart']); // if you want to use auth user instead of hardcoded
});

// Cart endpoints accessible without auth (as per your PHASE 2 using hardcoded user id)
Route::post('cart/add', [CartController::class,'addToCart']);
Route::get('cart/backend-list', [CartController::class,'listForBackend']);
Route::delete('cart/{id}', [CartController::class,'remove']);