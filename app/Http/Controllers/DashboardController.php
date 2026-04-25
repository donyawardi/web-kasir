<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $storeIsOpen  = StoreSetting::get('is_open', '1') === '1';
        $storeOpenTime  = StoreSetting::get('open_time', '08:00');
        $storeCloseTime = StoreSetting::get('close_time', '22:00');
        $closedNote     = StoreSetting::get('closed_note', 'Toko sedang tutup. Silahkan datang kembali sesuai jam operasional kami.');

        if ($isAdmin) {
            $stats = [
                'total_orders'       => Order::count(),
                'pending_orders'     => Order::where('status', 'pending')->count(),
                'completed_orders'   => Order::where('status', 'completed')->count(),
                'total_products'     => Product::count(),
                'available_products' => Product::where('available', true)->count(),
                'total_tables'       => Table::count(),
                'total_revenue'      => Order::where('status', 'completed')->sum('total_price'),
            ];

            $recent_orders = Order::with(['table', 'orderItems.product'])
                ->latest()
                ->take(10)
                ->get();

            return view('dashboard', compact('stats', 'recent_orders', 'isAdmin', 'storeIsOpen', 'storeOpenTime', 'storeCloseTime', 'closedNote'));
        }

        // Kasir
        $today = Carbon::today();

        $stats = [
            'pending'         => Order::whereIn('status', ['pending', 'cooking', 'ready'])->count(),
            'completed_today' => Order::where('status', 'completed')->whereDate('updated_at', $today)->count(),
            'orders_today'    => Order::whereDate('created_at', $today)->count(),
            'revenue_today'   => Order::where('status', 'completed')->whereDate('updated_at', $today)->sum('total_price'),
        ];

        $pending_queue = Order::with(['table', 'payment'])
            ->whereIn('status', ['pending', 'cooking', 'ready'])
            ->oldest('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'pending_queue', 'isAdmin', 'storeIsOpen', 'storeOpenTime', 'storeCloseTime', 'closedNote'));
    }

    public function checkNewOrders()
    {
        $pendingCount = Order::whereIn('status', ['pending', 'cooking', 'ready'])->count();
        $latestOrder  = Order::latest()->first();

        return response()->json([
            'pending_count'     => $pendingCount,
            'latest_order_id'   => $latestOrder?->id,
            'latest_order_time' => $latestOrder?->created_at?->toIso8601String(),
        ]);
    }

    public function toggleStore(Request $request)
    {
        $current = StoreSetting::get('is_open', '1');
        $new     = $current === '1' ? '0' : '1';
        StoreSetting::set('is_open', $new);

        $label = $new === '1' ? 'dibuka' : 'ditutup';
        return redirect()->route('dashboard')->with('success', "Toko berhasil $label.");
    }

    public function updateStoreHours(Request $request)
    {
        $request->validate([
            'open_time'   => 'required|date_format:H:i',
            'close_time'  => 'required|date_format:H:i|after:open_time',
            'closed_note' => 'nullable|string|max:200',
        ]);
        StoreSetting::set('open_time',  $request->open_time);
        StoreSetting::set('close_time', $request->close_time);
        if ($request->filled('closed_note')) {
            StoreSetting::set('closed_note', $request->closed_note);
        }
        return redirect()->route('dashboard')->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
