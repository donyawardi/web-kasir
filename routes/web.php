<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Panel\OrderController as PanelOrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Spatie\Permission\Middleware\RoleMiddleware;

// ─────────────────────────────────────────────
// Halaman utama
// ─────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));

// ─────────────────────────────────────────────
// PANEL (Admin + Kasir)
// ─────────────────────────────────────────────
Route::middleware(['auth', RoleMiddleware::using('admin|kasir')])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/check-orders', [DashboardController::class, 'checkNewOrders'])->name('api.check-orders');

    // Produk
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])->name('products.toggle-availability');

    // Meja
    Route::resource('tables', TableController::class);
    Route::post('tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])->name('tables.regenerate_qr');
    Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQr'])->name('tables.download_qr');
    Route::post('tables/regenerate-all', [TableController::class, 'regenerateAll'])->name('tables.regenerate_all');

    // Pesanan
    Route::resource('orders', PanelOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('orders/{order}/process-payment', [PanelOrderController::class, 'processPayment'])->name('orders.process-payment');
    Route::patch('orders/{order}/update-status', [PanelOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/receipt', [PanelOrderController::class, 'receipt'])->name('orders.receipt');

    // Transaksi
    Route::resource('transactions', TransactionController::class);

    // Payment panel
    Route::get('payment/{order}/detail', [PaymentController::class, 'show'])->name('payment.show');
    Route::put('payment/{order}', [PaymentController::class, 'complete'])->name('payment.complete');

    // ── Admin only ──────────────────────────────────────────────────
    Route::middleware(RoleMiddleware::using('admin'))->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::post('store/toggle', [DashboardController::class, 'toggleStore'])->name('store.toggle');
        Route::post('store/hours', [DashboardController::class, 'updateStoreHours'])->name('store.hours');
    });
});

// ─────────────────────────────────────────────
// CUSTOMER (public, no auth required)
// ─────────────────────────────────────────────
Route::get('/order/{table}', [OrderController::class, 'orderPage'])->name('order.show');
Route::post('/order', [OrderController::class, 'storeOrder'])->name('order.store');
Route::get('/order-status/{order}', [OrderController::class, 'trackOrder'])->name('order.track');
Route::get('/api/order-status/{order}', [OrderController::class, 'orderStatus'])->name('api.order.status');
Route::get('/payment/{order_id}', [PaymentController::class, 'paymentPage'])->name('payment.page');
Route::post('/payment/{order}/cash', [PaymentController::class, 'chooseCash'])->name('payment.choose-cash');
Route::get('/tables/{table}/qr', [TableController::class, 'showQrCode'])->name('tables.qr');

