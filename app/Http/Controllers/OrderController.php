<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;

class OrderController extends Controller
{
    public function index()
    {
        // $orders = Order::with('table')->orderBy('created_at', 'desc')->get();
        $orders = Order::with('orderItems.product')->get();
        $orders = Order::with(['table', 'orderItems.product'])->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)   //untuk admin
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = array_filter($request->items, function ($qty) {
            return $qty > 0;
        });
    
        if (count($items) === 0) {
            return back()->with('error', 'Minimal pilih 1 produk.');
        }
    
        // Buat pesanan baru
        $order = new Order();
        $order->table_id = $request->table_id;
        $order->status = 'pending'; // default
        $order->total_price = 0;
        $order->save();
    
        $total = 0;
    
        foreach ($request->items as $item) {
            $product = \App\Models\Product::find($item['menu_id']);
            $subtotal = $product->price * $item['quantity'];
    
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);

               $total += $subtotal;
        }
    
        $order->total_price = $total;
        $order->save();
    
        return response()->json([
            'order_id' => $order->id,
            'total_price' => $total,
        ]);
    
    
        $order->total_price = $totalPrice;
        $order->save();
    
        return redirect()->route('payment.page', $order->id)->with('success', 'Pesanan berhasil dibuat!');
    }
    
    
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
    
            return response()->json([
                'message' => 'Berhasil dipesan!',
                'order_id' => $order->id,
                'total_price' => $total,
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'message' => 'Terjadi kesalahan saat memesan.',
                'error' => $e->getMessage(), // bisa dihilangkan di production
            ], 500);
        }
    }
    

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);
    
        $order->update(['status' => $request->status]);
    
        return redirect()->route('orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function orderPage($tableId)
    {
        $table = \App\Models\Table::findOrFail($tableId);
        $products = Product::all(); // tampilkan semua produk
    
        return view('orders.order', compact('table', 'products'));
    }

    public function create()
    {
        $tables = \App\Models\Table::all();
        $products = \App\Models\Product::all();
    
        return view('orders.create', compact('tables', 'products'));
    }

    public function createByKasir()
    {
        $tables = Table::all();
        $products = Product::all();
        return view('orders.kasir_create', compact('tables', 'products'));
    }
    
    public function storeByKasir(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array',
        ]);
    
        $order = new Order();
        $order->table_id = $request->table_id;
        $order->status = 'pending';
        $order->total_price = 0;
        $order->save();
    
        $total = 0;
        foreach ($request->items as $productId => $quantity) {
            if ($quantity > 0) {
                $product = Product::findOrFail($productId);
                $subtotal = $product->price * $quantity;
    
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
    
                $total += $subtotal;
            }
        }
    
        $order->total_price = $total;
        $order->save();
    
        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat.');
    }
    
}
