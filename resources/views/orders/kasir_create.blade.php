@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Pesanan Baru</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('kasir.orders.store') }}">
        @csrf

        <div class="mb-3">
            <label for="table_id" class="form-label">Pilih Meja</label>
            <select name="table_id" id="table_id" class="form-control" required>
                <option value="">-- Pilih Meja --</option>
                @foreach(App\Models\Table::all() as $table)
                    <option value="{{ $table->id }}">Meja {{ $table->number }}</option>
                @endforeach
            </select>
        </div>

        <hr>

        <h4>Pesanan</h4>

        <div id="order-items">
            <div class="order-item row mb-2">
                <div class="col-md-6">
                    <select name="items[0][product_id]" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach(App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - Rp{{ number_format($product->price) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="items[0][quantity]" class="form-control" placeholder="Jumlah" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-item">Hapus</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-item" class="btn btn-secondary mt-2">+ Tambah Item</button>

        <hr>

        <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
    </form>
</div>

<script>
    let itemIndex = 1;

    document.getElementById('add-item').addEventListener('click', function () {
        const orderItems = document.getElementById('order-items');
        const newItem = document.createElement('div');
        newItem.classList.add('order-item', 'row', 'mb-2');

        newItem.innerHTML = `
            <div class="col-md-6">
                <select name="items[${itemIndex}][product_id]" class="form-control" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach(App\Models\Product::all() as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - Rp{{ number_format($product->price) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Jumlah" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-item">Hapus</button>
            </div>
        `;

        orderItems.appendChild(newItem);
        itemIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-item')) {
            e.target.closest('.order-item').remove();
        }
    });
</script>
@endsection
