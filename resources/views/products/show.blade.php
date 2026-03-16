@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $product->name }}</h1>

    <p><strong>Harga:</strong> Rp {{ number_format($product->price, 0, ',', '.') }}</p>
    <p><strong>Stok:</strong> {{ $product->stock }}</p>

    @if(!empty($product->description))
        <p><strong>Deskripsi:</strong></p>
        <p>{{ $product->description }}</p>
    @endif

    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Kembali</a>
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">Edit</a>

    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
    </form>
</div>
@endsection
