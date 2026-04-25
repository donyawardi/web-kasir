<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $onlyAvailable = $request->boolean('only_available');
        $category = $request->get('category');

        $products = Product::when($q, function ($query, $q) {
            $query->where('name', 'like', '%' . $q . '%')
                  ->orWhere('description', 'like', '%' . $q . '%');
        })
        ->when($onlyAvailable, function ($query) {
            $query->where('available', true);
        })
        ->when($category, function ($query) use ($category) {
            $query->where('category', $category);
        })
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

        // If AJAX request (live search), return rendered rows and pagination as JSON
        if ($request->ajax()) {
            $rows = view('products._table_rows', compact('products'))->render();
            $pagination = view('products._pagination', compact('products'))->render();
            $mobile = view('products._mobile_cards', compact('products'))->render();
            return response()->json([
                'rows' => $rows,
                'pagination' => $pagination,
                'mobile' => $mobile,
            ]);
        }

            return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'available'   => 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // use boolean() so unchecked checkbox (missing param) is treated as false
        $data['available'] = $request->boolean('available');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'available'   => 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // use boolean() so unchecked checkbox sets available to false
        $data['available'] = $request->boolean('available');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('products.index')->with('success', "Produk '" . $product->name . "' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Toggle product availability (AJAX)
     */
    public function toggleAvailability(Product $product)
    {
        $product->update(['available' => !$product->available]);

        if (request()->wantsJson()) {
            return response()->json([
                'available' => $product->available,
                'message' => $product->available ? 'Produk ditandai tersedia.' : 'Produk ditandai habis.',
            ]);
        }

        return redirect()->back()->with('success', $product->available ? "'{$product->name}' ditandai tersedia." : "'{$product->name}' ditandai habis.");
    }
}
