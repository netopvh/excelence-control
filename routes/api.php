<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|@headlessui-float/react
@headlessui/react
*/




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('customer')->group(function () {
    Route::post('/', [CustomerController::class, 'store']);
});

Route::prefix('user')->group(function () {
    Route::get('employees', [UserController::class, 'getEmployees']);
});

Route::prefix('order')->group(function () {
    Route::get('products/{orderId}', [OrderController::class, 'productsOrder']);
    Route::post('/{id}/store', [OrderController::class, 'updateStatusAndStep']);
    Route::post('/{id}/info', [OrderController::class, 'updateInfo']);
});

Route::prefix('purchase')->group(function () {
    Route::get('/', [PurchaseController::class, 'index']);
    Route::get('/{id}/viewed', [PurchaseController::class, 'checkUserViewed']);
    Route::post('/{id}/view', [PurchaseController::class, 'userViewed']);
    Route::get('/{id}/show', [PurchaseController::class, 'show']);
    Route::get('/{id}/items', [PurchaseController::class, 'orderItems']);
});
