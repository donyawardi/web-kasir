<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\KasirOrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Route "dashboard" compatibility: redirect setelah login berdasarkan role
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

    if (method_exists($user, 'hasRole')) {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('kasir')) {
            return redirect()->route('orders.index');
        }
    }

    // fallback
    return redirect('/');
})->name('dashboard');

// Rute untuk admin
Route::middleware(['auth'])->group(function () {

    // Hanya admin yang bisa akses dashboard & manajemen
    Route::prefix('admin')->name('admin.')->middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin'))->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('tables', TableController::class);
        Route::resource('products', ProductController::class);
    });

    // Admin dan kasir bisa akses transaksi
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using(['admin', 'kasir']))->group(function () {
        Route::resource('transactions', TransactionController::class);
    });

});

// Rute untuk kasir (panel kasir)
Route::prefix('kasir')->name('kasir.')->middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('kasir'))->group(function () {
    Route::resource('orders', KasirOrderController::class)->except(['edit', 'update', 'destroy']);
});

// Rute pelanggan
Route::get('/order/{table}', [OrderController::class, 'orderPage'])->name('order.show');
Route::get('/payment/{order_id}', [PaymentController::class, 'paymentPage'])->name('payment.page');
Route::get('/tables/{table}/qr', [TableController::class, 'showQrCode'])->name('tables.qr');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/order', [OrderController::class, 'storeOrder'])->name('order.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::patch('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

Route::get('/payment/{order}', [PaymentController::class, 'show'])->name('payment.show');
Route::put('/payment/{order}', [PaymentController::class, 'complete'])->name('payment.complete');
