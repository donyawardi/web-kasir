<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::get('/menu', function () {
	return Product::query()
		->where('available', true)
		->select(['id', 'name', 'description', 'price', 'available'])
		->orderBy('name')
		->get();
});

Route::post('/order', [OrderController::class, 'storeOrder'])->name('api.order.store');
Route::post('/payment', [PaymentController::class, 'createPayment']);
Route::post('/midtrans/notification', [PaymentController::class, 'notification']);
Route::get('/payment/{order}/status', [PaymentController::class, 'checkStatus']);
