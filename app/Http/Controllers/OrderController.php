<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Table;
use App\Models\Transaction;

class OrderController extends Controller
{
    public function storeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        try {
            // Buat order baru
            $order = new Order();
            $order->table_id = $request->table_id;
            $order->is_takeaway = $request->boolean('is_takeaway');
            $order->status = 'pending';
            $order->total_price = 0; // Default dulu
            $order->save();
    
            $total = 0;
    
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $price = $product->price;
                $subtotal = $price * $quantity;
    
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
    
                $total += $subtotal;
            }
    
            // Simpan total harga ke order
            $order->total_price = $total;
            $order->save();

            // Mark table as occupied
            Table::where('id', $order->table_id)->update(['status' => 'occupied']);
    
            return response()->json([
                'message' => 'Berhasil dipesan!',
                'order_id' => $order->id,
                'total_price' => $total,
                'tracking_url' => route('order.track', $order->id),
            ]);
    
        } catch (\Exception $e) {
    
            \Illuminate\Support\Facades\Log::error('Order creation failed', ['exception' => $e]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memesan.',
            ], 500);
        }
    }
    

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,ready,completed,cancelled',
        ]);

        // Cegah menyelesaikan pesanan yang belum dibayar
        if ($request->status === 'completed') {
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment && $payment->status !== 'success') {
                return redirect()->back()->with('error', 'Pesanan belum dibayar! Selesaikan pembayaran terlebih dahulu.');
            }
        }
    
        $order->update(['status' => $request->status]);

        // Free table when order completed or cancelled
        if (in_array($request->status, ['completed', 'cancelled'])) {
            Table::where('id', $order->table_id)->update(['status' => 'available']);
        }

        // Finalize payment & create transaction when completed
        if ($request->status === 'completed') {
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update(['status' => 'success']);
            }

            if (!Transaction::where('order_id', $order->id)->exists()) {
                $paymentMethod = optional(Payment::where('order_id', $order->id)->first())->payment_method;
                Transaction::create([
                    'order_id' => $order->id,
                    'table_id' => $order->table_id,
                    'total_price' => $order->total_price,
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function receipt(Order $order)
    {
        $order->load(['orderItems.product', 'table']);
        $payment = Payment::where('order_id', $order->id)->first();
        $cashReceived = $payment->cash_received ?? null;
        return view('orders.receipt', compact('order', 'payment', 'cashReceived'));
    }

    /**
     * Public: customer order tracking page
     */
    public function trackOrder($orderId)
    {
        $order = Order::with(['orderItems.product', 'table'])->findOrFail($orderId);
        return view('orders.tracking', compact('order'));
    }

    /**
     * API: return order status for polling
     */
    public function orderStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        return response()->json([
            'status' => $order->status,
            'updated_at' => $order->updated_at->toIso8601String(),
        ]);
    }

    public function orderPage($tableId)
    {
        $table = Table::findOrFail($tableId);
        $products = Product::orderByDesc('available')->orderBy('name')->get();
    
        return view('orders.order', compact('table', 'products'));
    }

}

