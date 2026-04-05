<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Kasir\KasirDashboardController;
use App\Http\Controllers\KasirOrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Kasir\KasirReportController;

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

    $roleQuery = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('model_has_roles.model_id', $user->id)
        ->where('model_has_roles.model_type', get_class($user));

    if ((clone $roleQuery)->where('roles.name', 'admin')->exists()) {
        return redirect()->route('admin.dashboard');
    }

    if ((clone $roleQuery)->where('roles.name', 'kasir')->exists()) {
        return redirect()->route('kasir.dashboard');
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
        // Regenerate QR code for a specific table
        Route::post('tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])->name('tables.regenerate_qr');
        // Download QR for a table
        Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQr'])->name('tables.download_qr');
        // Bulk regenerate all table QR codes
        Route::post('tables/regenerate-all', [TableController::class, 'regenerateAll'])->name('tables.regenerate_all');
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])->name('products.toggle-availability');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'create', 'store', 'show']);
        Route::patch('orders/{order}/update-status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    // Admin: full CRUD transaksi
    Route::middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('admin'))->group(function () {
        Route::resource('transactions', TransactionController::class);
    });

    // Rute untuk kasir (panel kasir) — inside auth group
    Route::prefix('kasir')->name('kasir.')->middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('kasir'))->group(function () {
        Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/check-orders', [KasirDashboardController::class, 'checkNewOrders'])->name('api.check-orders');
        Route::resource('orders', KasirOrderController::class)->except(['edit', 'update', 'destroy']);
        Route::post('orders/{order}/process-payment', [KasirOrderController::class, 'processPayment'])->name('orders.process-payment');
        Route::resource('products', ProductController::class);
        Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])->name('products.toggle-availability');
        Route::get('reports', [KasirReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [KasirReportController::class, 'export'])->name('reports.export');
    });

    // Order management routes (admin/kasir authenticated)
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::patch('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/payment/{order}/detail', [PaymentController::class, 'show'])->name('payment.show');
    Route::put('/payment/{order}', [PaymentController::class, 'complete'])->name('payment.complete');
});

// Rute pelanggan (public - read only + order creation)
Route::get('/order/{table}', [OrderController::class, 'orderPage'])->name('order.show');
Route::post('/order', [OrderController::class, 'storeOrder'])->name('order.store');
Route::get('/order-status/{order}', [OrderController::class, 'trackOrder'])->name('order.track');
Route::get('/api/order-status/{order}', [OrderController::class, 'orderStatus'])->name('api.order.status');
Route::get('/payment/{order_id}', [PaymentController::class, 'paymentPage'])->name('payment.page');
Route::post('/payment/{order}/cash', [PaymentController::class, 'chooseCash'])->name('payment.choose-cash');
Route::get('/tables/{table}/qr', [TableController::class, 'showQrCode'])->name('tables.qr');
