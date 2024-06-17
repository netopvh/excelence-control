<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Example Routes
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

Route::prefix('/dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/list', [DashboardController::class, 'list'])->name('list');
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/', [ImportController::class, 'import'])->name('store');
    });
    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::post('/{id}/update/status', [OrderController::class, 'updateStatus'])->name('update.status');
        Route::post('/{id}/update/employee', [OrderController::class, 'updateEmployee'])->name('update.employee');
        Route::post('/{id}/update/arrived', [OrderController::class, 'updateArrived'])->name('update.arrived');
        Route::post('/{id}/upload/preview', [OrderController::class, 'uploadPreview'])->name('upload.preview');
        Route::post('/{id}/upload/design', [OrderController::class, 'uploadDesign'])->name('upload.design');
    });
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');
    });
});
Route::view('/pages/slick', 'pages.slick');
Route::view('/pages/datatables', 'pages.datatables');
Route::view('/pages/blank', 'pages.blank');
