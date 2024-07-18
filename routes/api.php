<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductionController;
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

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
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
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('products/{orderId}', [OrderController::class, 'productsOrder']);
    Route::put('/{id}', [OrderController::class, 'updateStatusAndStep']);
    Route::post('/{id}/info', [OrderController::class, 'updateInfo']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
    Route::get('/{id}/item/{itemId}', [OrderController::class, 'showProductOrder']);

    Route::post('/{id}/design', [OrderController::class, 'uploadDesign']);
    Route::post('/{id}/design/{productId}', [OrderController::class, 'removeDesign']);
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

Route::prefix('production')->group(function () {
    Route::get('/', [ProductionController::class, 'index']);
    Route::get('/{id}/show', [ProductionController::class, 'show']);
    Route::post('/{id}/item/{itemId}', [ProductionController::class, 'updateOrderItem']);
    Route::get('/{id}/viewed', [ProductionController::class, 'checkUserViewed']);
    Route::post('/{id}/view', [ProductionController::class, 'userViewed']);
});

Route::prefix('import')->group(function () {
    Route::post('/product', [ImportController::class, 'importProducts']);
});
