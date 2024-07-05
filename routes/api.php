<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\RoleController;
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

Route::prefix('role')->group(function () {
    Route::get('/list', [RoleController::class, 'getRoles']);
});

Route::prefix('user')->group(function () {
    Route::post('/', [UserController::class, 'store']);
    Route::get('list', [UserController::class, 'getUsers']);
    Route::get('employees', [UserController::class, 'getEmployees']);
    Route::post('/{id}/password', [UserController::class, 'updatePassword']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
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
    Route::post('/{id}/product/{productId}', [PurchaseController::class, 'updateProductInfo']);
    Route::get('/{id}/product/{productId}/show', [PurchaseController::class, 'showProductInfo']);
});
