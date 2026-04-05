<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Carbon;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'pending' => Order::whereIn('status', ['pending', 'cooking', 'ready'])->count(),
            'completed_today' => Order::where('status', 'completed')
                ->whereDate('updated_at', $today)
                ->count(),
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'revenue_today' => Order::where('status', 'completed')
                ->whereDate('updated_at', $today)
                ->sum('total_price'),
        ];

        $pending_queue = Order::with(['table', 'payment'])
            ->whereIn('status', ['pending', 'cooking', 'ready'])
            ->oldest('created_at')
            ->take(5)
            ->get();

        return view('kasir.dashboard', compact('stats', 'pending_queue'));
    }

    /**
     * API: return current pending count and latest order id for polling
     */
    public function checkNewOrders()
    {
        $pendingCount = Order::whereIn('status', ['pending', 'cooking', 'ready'])->count();
        $latestOrder = Order::latest()->first();

        return response()->json([
            'pending_count' => $pendingCount,
            'latest_order_id' => $latestOrder?->id,
            'latest_order_time' => $latestOrder?->created_at?->toIso8601String(),
        ]);
    }
}
