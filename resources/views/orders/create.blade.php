@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Buat Pesanan Baru</h2>

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf

        <div class="mb-3">
            <label for="table_id">Pilih Meja</label>
            <select name="table_id" class="form-control" required>
                <option value="">-- Pilih Meja --</option>
                @foreach($tables as $table)
                    <option value="{{ $table->id }}">Meja {{ $table->table_number }}</option>
                @endforeach
            </select>
        </div>

        <h4>Produk</h4>
        @foreach($products as $product)
            <div class="mb-2">
                <label>{{ $product->name }} (Rp{{ number_format($product->price, 0, ',', '.') }})</label>
                <input type="number" name="items[{{ $product->id }}]" min="0" value="0" class="form-control">
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary mt-3">Simpan Pesanan</button>
    </form>
</div>
@endsection
