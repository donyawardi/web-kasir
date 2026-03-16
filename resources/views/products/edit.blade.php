@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Produk</h2>
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <label>Nama Produk</label>
                <input type="text" name="name" value="{{ $product->name }}" required>
            </div>
            <div>
                <label>Harga</label>
                <input type="number" name="price" value="{{ $product->price }}" required>
            </div>
            <div>
                <label>Stok</label>
                <input type="number" name="stock" value="{{ $product->stock }}" required>
            </div>
            <button type="submit">Update</button>
            <a href="{{ route('admin.products.index') }}">Batal</a>
        </form>
    </div>
@endsection
