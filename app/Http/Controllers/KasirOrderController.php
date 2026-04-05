<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Table;

class KasirOrderController extends Controller
{
    // Menampilkan daftar pesanan kasir
    public function index(Request $request)
    {
        $status = $request->query('status');
        $range = $request->query('range');
        $tableNumber = $request->query('table_number');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $allowedStatuses = ['pending', 'cooking', 'ready', 'completed', 'cancelled'];

        // Default to today if no range or date filters provided
        if (!$range && !$dateFrom && !$dateTo) {
            $range = 'today';
        }

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

        $orders = Order::with('table', 'payment')
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(filled($tableNumber), function ($query) use ($tableNumber) {
                $query->whereHas('table', function ($tableQuery) use ($tableNumber) {
                    $tableQuery->where('table_number', 'like', '%' . $tableNumber . '%');
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
            ->paginate(10)
            ->withQueryString();

        return view('orders.kasir_index', compact('orders'));
    }

    // Menampilkan detail pesanan kasir
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'table', 'payment']);
        return view('orders.kasir_show', compact('order'));
    }

    // Membuat pesanan baru
    public function create()
    {
        $products = Product::orderByDesc('available')->orderBy('name')->get();
        $tables = Table::orderBy('table_number')->get();

        return view('orders.kasir_create', compact('products', 'tables'));
    }

    // Menyimpan pesanan baru
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => $request->boolean('is_takeaway') ? 'nullable' : 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        try {
            $order = Order::create([
                'table_id' => $request->table_id,
                'is_takeaway' => $request->boolean('is_takeaway'),
                'status' => 'pending',
                'total_price' => 0,
            ]);
    
            $totalPrice = 0;
    
            foreach ($request->items as $item) {
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

            // Mark table as occupied (only for dine-in)
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            $order->update(['total_price' => $totalPrice]);

            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'status' => 'pending',
                    'payment_method' => 'later',
                    'qr_code_url' => null,
                ]
            );

            return redirect()->route('kasir.orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat. Silakan proses pembayaran.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
        }
    }

    // Proses pembayaran untuk pesanan 'bayar nanti'
    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment || $payment->status === 'success') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Pesanan ini sudah dibayar.'], 422);
            }
            return redirect()->route('kasir.orders.show', $order->id)
                ->with('error', 'Pesanan ini sudah dibayar.');
        }

        $paymentMethod = $request->payment_method;

        if ($paymentMethod === 'cash') {
            $cashReceived = (float) $request->input('cash_received', 0);
            if ($cashReceived < $order->total_price) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Nominal uang cash kurang dari total pesanan.'], 422);
                }
                return redirect()->route('kasir.orders.show', $order->id)
                    ->with('error', 'Nominal uang cash kurang dari total pesanan.');
            }

            $payment->update([
                'payment_method' => 'cash',
                'status' => 'success',
                'cash_received' => $cashReceived,
            ]);

            if (!\App\Models\Transaction::where('order_id', $order->id)->exists()) {
                Transaction::create([
                    'order_id' => $order->id,
                    'table_id' => $order->table_id,
                    'total_price' => $order->total_price,
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                ]);
            }

            // Auto-complete order if already being prepared
            if (in_array($order->status, ['cooking', 'ready'])) {
                $order->update(['status' => 'completed']);
                $table = $order->table()->first();
                if ($table) {
                    $table->update(['status' => 'available']);
                }
            }

            $change = $cashReceived - $order->total_price;

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran cash berhasil!',
                    'change' => $change,
                ]);
            }

            return redirect()->route('kasir.orders.show', $order->id)
                ->with('success', 'Pembayaran cash berhasil! Kembalian: Rp' . number_format($change, 0, ',', '.'));
        }

        // QRIS — redirect to payment page
        $payment->update(['payment_method' => 'qris']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('payment.page', $order->id),
            ]);
        }

        return redirect()->route('payment.page', $order->id);
    }
}