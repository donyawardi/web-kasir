<?php

namespace App\Http\Controllers\Panel;

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
        $isAdmin     = auth()->user()->hasRole('admin');
        $status      = $request->query('status');
        $range       = $request->query('range');
        $tableNumber = $request->query('table_number');
        $dateFrom    = $request->query('date_from');
        $dateTo      = $request->query('date_to');
        $allowedStatuses = ['pending', 'cooking', 'ready', 'completed', 'cancelled'];

        // Kasir defaults to today
        if (!$isAdmin && !$range && !$dateFrom && !$dateTo) {
            $range = 'today';
        }

        if ($range === 'today') {
            $dateFrom = Carbon::today()->toDateString();
            $dateTo   = Carbon::today()->toDateString();
        } elseif ($range === 'yesterday') {
            $dateFrom = Carbon::yesterday()->toDateString();
            $dateTo   = Carbon::yesterday()->toDateString();
        } elseif ($range === 'this_week') {
            $dateFrom = Carbon::now()->startOfWeek()->toDateString();
            $dateTo   = Carbon::now()->endOfWeek()->toDateString();
        }

        $orders = Order::with(['table', 'orderItems.product', 'payment'])
            ->when(in_array($status, $allowedStatuses, true), fn($q) => $q->where('status', $status))
            ->when(filled($tableNumber), fn($q) => $q->whereHas('table', fn($tq) => $tq->where('table_number', 'like', '%' . $tableNumber . '%')))
            ->when(filled($dateFrom), fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when(filled($dateTo),   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderByRaw("CASE WHEN status IN ('pending','cooking','ready') THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'asc')
            ->paginate($isAdmin ? 15 : 10)
            ->withQueryString();

        $stats = null;
        if ($isAdmin) {
            $stats = [
                'total'     => Order::count(),
                'pending'   => Order::where('status', 'pending')->count(),
                'cooking'   => Order::where('status', 'cooking')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ];
        }

        return view('orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $products = Product::orderByDesc('available')->orderBy('name')->get();
        $tables   = Table::orderBy('table_number')->get();
        return view('orders.create', compact('products', 'tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id'              => $request->boolean('is_takeaway') ? 'nullable' : 'required|exists:tables,id',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        $items = array_filter($request->items ?? [], fn($i) => isset($i['quantity']) && (int) $i['quantity'] > 0);

        if (count($items) === 0) {
            return back()->with('error', 'Minimal pilih 1 produk.');
        }

        $order = Order::create([
            'table_id'    => $request->table_id,
            'is_takeaway' => $request->boolean('is_takeaway'),
            'status'      => 'pending',
            'total_price' => 0,
        ]);

        $totalPrice = 0;
        foreach ($items as $item) {
            $product  = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'];
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'subtotal'   => $subtotal,
            ]);
            $totalPrice += $subtotal;
        }

        if ($order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'occupied']);
        }
        $order->update(['total_price' => $totalPrice]);

        Payment::create([
            'order_id'       => $order->id,
            'status'         => 'pending',
            'payment_method' => 'later',
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'table', 'payment']);
        if (!$order->payment) {
            Payment::create(['order_id' => $order->id, 'status' => 'pending', 'payment_method' => 'later']);
            $order->load('payment');
        }
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,ready,completed,cancelled',
        ]);

        // Cegah selesaikan pesanan yang belum dibayar
        if ($request->status === 'completed') {
            $payment = $order->payment;
            if ($payment && $payment->status !== 'success') {
                return back()->with('error', 'Pesanan belum dibayar! Selesaikan pembayaran terlebih dahulu.');
            }
        }

        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['completed', 'cancelled'])) {
            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }
        }

        if ($request->status === 'completed') {
            $payment = $order->fresh()->payment;
            if ($payment) {
                $payment->update(['status' => 'success']);
            }
            if (!Transaction::where('order_id', $order->id)->exists()) {
                Transaction::create([
                    'order_id'       => $order->id,
                    'table_id'       => $order->table_id,
                    'total_price'    => $order->total_price,
                    'payment_status' => 'paid',
                    'payment_method' => optional($payment)->payment_method,
                ]);
            }
        }

        return back()->with('success', 'Status pesanan berhasil diubah.');
    }

    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'cash_received'  => 'nullable|numeric|min:0',
        ]);

        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment || $payment->status === 'success') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Pesanan ini sudah dibayar.'], 422);
            }
            return redirect()->route('orders.show', $order->id)->with('error', 'Pesanan ini sudah dibayar.');
        }

        if ($request->payment_method === 'cash') {
            $cashReceived = (float) $request->input('cash_received', 0);
            if ($cashReceived < $order->total_price) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Nominal uang cash kurang dari total pesanan.'], 422);
                }
                return redirect()->route('orders.show', $order->id)->with('error', 'Nominal uang cash kurang dari total pesanan.');
            }

            $payment->update([
                'payment_method' => 'cash',
                'status'         => 'success',
                'cash_received'  => $cashReceived,
            ]);

            if (!Transaction::where('order_id', $order->id)->exists()) {
                Transaction::create([
                    'order_id'       => $order->id,
                    'table_id'       => $order->table_id,
                    'total_price'    => $order->total_price,
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                ]);
            }

            if (in_array($order->status, ['cooking', 'ready'])) {
                $order->update(['status' => 'completed']);
                if ($order->table) {
                    $order->table->update(['status' => 'available']);
                }
            }

            $change = $cashReceived - $order->total_price;

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'change' => $change]);
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pembayaran cash berhasil! Kembalian: Rp' . number_format($change, 0, ',', '.'));
        }

        // QRIS
        $payment->update(['payment_method' => 'qris']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'redirect' => route('payment.page', $order->id)]);
        }

        return redirect()->route('payment.page', $order->id);
    }

    public function receipt(Order $order)
    {
        $order->load(['orderItems.product', 'table']);
        $payment      = Payment::where('order_id', $order->id)->first();
        $cashReceived = $payment?->cash_received;
        return view('orders.receipt', compact('order', 'payment', 'cashReceived'));
    }
}
