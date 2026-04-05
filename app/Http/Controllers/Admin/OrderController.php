<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $range = $request->query('range');
        $tableNumber = $request->query('table_number');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $allowedStatuses = ['pending', 'cooking', 'ready', 'completed', 'cancelled'];

        if ($range === 'today') {
            $dateFrom = Carbon::today()->toDateString();
            $dateTo = Carbon::today()->toDateString();
        } elseif ($range === 'yesterday') {
            $dateFrom = Carbon::yesterday()->toDateString();
            $dateTo = Carbon::yesterday()->toDateString();
        } elseif ($range === 'this_week') {
            $dateFrom = Carbon::now()->startOfWeek()->toDateString();
            $dateTo = Carbon::now()->endOfWeek()->toDateString();
        }

        $orders = Order::with(['table', 'orderItems.product', 'payment'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(filled($tableNumber), function ($query) use ($tableNumber) {
                $query->whereHas('table', function ($q) use ($tableNumber) {
                    $q->where('table_number', 'like', '%' . $tableNumber . '%');
                });
            })
            ->when(filled($dateFrom), function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when(filled($dateTo), function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByRaw("CASE WHEN status IN ('pending','cooking','ready') THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Statistik ringkasan
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'cooking' => Order::where('status', 'cooking')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $products = Product::where('available', true)->orderBy('name')->get();
        $tables = Table::orderBy('table_number')->get();
        return view('admin.orders.create', compact('products', 'tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => $request->boolean('is_takeaway') ? 'nullable' : 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = array_filter($request->items, function ($item) {
            return isset($item['quantity']) && (int) $item['quantity'] > 0;
        });

        if (count($items) === 0) {
            return back()->with('error', 'Minimal pilih 1 produk.');
        }

        $order = Order::create([
            'table_id' => $request->table_id,
            'is_takeaway' => $request->boolean('is_takeaway'),
            'status' => 'pending',
            'total_price' => 0,
        ]);

        $totalPrice = 0;
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);
            $totalPrice += $subtotal;
        }

        if ($order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'occupied']);
        }
        $order->update(['total_price' => $totalPrice]);

        Payment::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'payment_method' => 'later',
        ]);

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'table', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,ready,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'completed' || $request->status === 'cancelled') {
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }
        }

        return back()->with('success', 'Status pesanan berhasil diubah.');
    }
}
