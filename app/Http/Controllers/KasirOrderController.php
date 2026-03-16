<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class KasirOrderController extends Controller
{
    // Menampilkan daftar pesanan kasir
    public function index()
    {
        $orders = Order::latest()->get(); // Ambil semua order
        // $orders = Order::where('created_by', auth()->id())->latest()->get(); // hanya ingin menampilkan order yang dibuat oleh kasir tertentu 
        return view('orders.index', compact('orders'));
    }

    // Menampilkan detail pesanan kasir
    public function show(Order $order)
    {
        return view('orders.kasir_show', compact('order'));
    }

    // Membuat pesanan baru
    public function create()
    {
        return view('orders.kasir_create');
    }

    // Menyimpan pesanan baru
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        try {
            $order = Order::create([
                'table_id' => $request->table_id,
                'status' => 'pending',
                'total_price' => 0, // akan dihitung setelah loop
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
    
            $order->update(['total_price' => $totalPrice]);
    
            return redirect()->route('kasir.orders.index')->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat pesanan.');
        }
    }
}