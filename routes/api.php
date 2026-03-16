<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/menu', [OrderController::class, 'menu']);
Route::post('/order', [OrderController::class, 'createOrder']);
Route::post('/payment', [PaymentController::class, 'createPayment']);
Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment']);
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// // Authenticated routes
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
// });

// // Public API routes
// Route::prefix('menu')->group(function () {
//     Route::get('/', [OrderController::class, 'menu']);
// });

// Route::prefix('order')->group(function () {
//     Route::post('/', [OrderController::class, 'createOrder'])->name('order.store');
//     // Menghapus route store yang duplikat
// });

// Route::prefix('payment')->group(function () {
//     Route::post('/', [PaymentController::class, 'createPayment']);
//     Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
// });
