<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * API: create Snap token for an order (called from customer page)
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with('orderItems.product', 'table')->findOrFail($request->order_id);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order sudah diproses'], 400);
        }

        $payment = Payment::firstOrCreate(
            ['order_id' => $order->id],
            ['status' => 'pending', 'payment_method' => 'qris']
        );

        // Reuse snap_token if it exists and is not too old (< 20 hours)
        if ($payment->snap_token && $payment->updated_at->diffInHours(now()) < 20) {
            return response()->json([
                'snap_token' => $payment->snap_token,
            ]);
        }

        $snapToken = $this->generateSnapToken($order);
        $payment->update(['snap_token' => $snapToken]);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    /**
     * Customer-facing payment page (public)
     */
    public function paymentPage($order_id)
    {
        $order = Order::with('orderItems.product', 'table')->findOrFail($order_id);

        if ($order->status === 'completed') {
            return view('payment', [
                'order' => $order,
                'payment' => Payment::where('order_id', $order->id)->first(),
                'snapToken' => null,
            ]);
        }

        $payment = Payment::firstOrCreate(
            ['order_id' => $order->id],
            ['status' => 'pending', 'payment_method' => 'qris']
        );

        $snapToken = $payment->snap_token;
        $tokenExpired = $payment->snap_token && $payment->updated_at->diffInHours(now()) >= 20;

        if ((!$snapToken || $tokenExpired) && $order->status === 'pending') {
            try {
                $snapToken = $this->generateSnapToken($order);
                $payment->update(['snap_token' => $snapToken]);
            } catch (\Throwable $e) {
                $snapToken = null;
            }
        }

        return view('payment', [
            'order' => $order,
            'payment' => $payment,
            'snapToken' => $snapToken,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /**
     * Staff-facing payment detail page (authenticated)
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'table']);
        $payment = Payment::where('order_id', $order->id)->first();

        return view('payment.show', compact('order', 'payment'));
    }

    /**
     * Customer chooses to pay cash at the cashier (public, no auth)
     */
    public function chooseCash(Order $order)
    {
        if ($order->status === 'completed') {
            return response()->json(['message' => 'Pesanan sudah dibayar'], 400);
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'status' => 'pending',
                'payment_method' => 'cash',
            ]
        );

        return response()->json(['message' => 'Silakan bayar tunai di kasir.']);
    }

    /**
     * Manual payment completion by staff (cash payment / manual confirm)
     */
    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        $cashReceived = (float) $request->input('cash_received', 0);

        if ($cashReceived > 0 && $cashReceived < $order->total_price) {
            return redirect()->back()->with('error', 'Uang yang diterima kurang dari total pesanan.');
        }

        $order->update(['status' => 'completed']);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'status' => 'success',
                'payment_method' => 'cash',
                'cash_received' => $cashReceived > 0 ? $cashReceived : null,
            ]
        );

        // Prevent duplicate transactions
        if (!Transaction::where('order_id', $order->id)->exists()) {
            Transaction::create([
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'total_price' => $order->total_price,
                'payment_status' => 'paid',
                'payment_method' => 'cash',
            ]);
        }

        // Release table
        if ($order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil! Kembalian: Rp' . number_format(max(0, $cashReceived - $order->total_price), 0, ',', '.'));
    }

    /**
     * Midtrans notification webhook (called by Midtrans server)
     */
    public function notification(Request $request)
    {
        $this->initMidtrans();
        $notification = new \Midtrans\Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id; // format: ORDER-{id}
        $paymentType = $notification->payment_type;
        $fraudStatus = $notification->fraud_status ?? null;
        $signatureKey = $notification->signature_key;

        // Verify signature
        $serverKey = config('midtrans.server_key');
        $grossAmount = $notification->gross_amount;
        $statusCode = $notification->status_code;
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Extract actual order ID
        $actualOrderId = str_replace('ORDER-', '', $orderId);
        $order = Order::find($actualOrderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Update payment info
        $payment->transaction_id = $notification->transaction_id ?? null;
        $payment->payment_method = $paymentType;

        if ($transactionStatus === 'capture') {
            $payment->status = ($fraudStatus === 'accept') ? 'success' : 'pending';
        } elseif ($transactionStatus === 'settlement') {
            $payment->status = 'success';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->status = 'failed';
        } elseif ($transactionStatus === 'pending') {
            $payment->status = 'pending';
        }

        $payment->save();

        // If payment successful, complete the order
        if ($payment->status === 'success') {
            $order->update(['status' => 'completed']);

            if (!Transaction::where('order_id', $order->id)->exists()) {
                Transaction::create([
                    'order_id' => $order->id,
                    'table_id' => $order->table_id,
                    'total_price' => $order->total_price,
                    'payment_status' => 'paid',
                    'payment_method' => $paymentType,
                ]);
            }

            // Release table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        } elseif ($payment->status === 'failed') {
            $order->update(['status' => 'cancelled']);

            // Release table
            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * AJAX: check payment status for polling from client
     */
    public function checkStatus(Order $order)
    {
        $payment = Payment::where('order_id', $order->id)->first();

        return response()->json([
            'order_status' => $order->fresh()->status,
            'payment_status' => $payment?->status ?? 'unknown',
        ]);
    }

    /**
     * Generate Midtrans Snap token
     */
    private function generateSnapToken(Order $order): string
    {
        $this->initMidtrans();
        $items = [];
        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => (string) $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($item->product->name ?? 'Item', 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id,
                'gross_amount' => (int) $order->total_price,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => 'Pelanggan',
                'last_name' => 'Meja ' . ($order->table->table_number ?? '-'),
            ],
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }
}