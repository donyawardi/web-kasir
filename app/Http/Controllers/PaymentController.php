<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order already processed'], 400);
        }

        // Simulasi QRIS (nanti diganti dengan API penyedia pembayaran)
        $qrCodeUrl = "https://dummy-qris.com/qris/".$order->id;

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'qr_code_url' => $qrCodeUrl,
        ]);

        return response()->json([
            'message' => 'Payment initiated',
            'qr_code_url' => $qrCodeUrl
        ]);
}

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $payment = Payment::where('order_id', $request->order_id)->firstOrFail();

        // Simulasi pembayaran sukses
        $payment->status = 'success';
        $payment->save();

        // Update status order
        $order = Order::findOrFail($request->order_id);
        $order->status = 'paid';
        $order->save();

        return response()->json([
            'message' => 'Payment successful',
            'order_id' => $order->id,
        ]);
    }

    public function paymentPage($order_id)
    // {
    // $order = Order::findOrFail($order_id);
    // return view('payment', compact('order'));
    // }
    {
        $order = Order::with('table')->findOrFail($order_id);
        return view('payment', compact('order'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'table']);
        return view('payment.show', compact('order'));
    }
    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);
    
        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil diselesaikan!');
    }
}