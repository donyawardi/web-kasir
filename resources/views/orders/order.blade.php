{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pesan dari Meja {{ $table->table_number }}</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <input type="hidden" name="table_id" value="{{ $table->id }}">

        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <input type="number" name="quantities[{{ $product->id }}]" min="0" value="0" class="form-control">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary mt-3">Pesan Sekarang</button>
    </form>
</div>
@endsection --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pemesanan Online</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1> Meja {{ $table->table_number }}</h1>

    <h2>Menu</h2>
    <form id="order-form">
        <input type="hidden" name="table_id" value="{{ $table->id }}">

        <ul>
            @foreach($products as $product)
                <li>
                    {{ $product->name }} - Rp{{ number_format($product->price, 0, ',', '.') }}
                    <input type="number" name="items[{{ $product->id }}]" min="0" value="0">
                </li>
            @endforeach
        </ul>

        <button type="submit">Pesan Sekarang</button>
    </form>

    <script>
        document.getElementById('order-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let items = [];

            for (let [key, value] of formData.entries()) {
                if (key.startsWith("items") && value > 0) {
                    let productId = key.match(/\d+/)[0];
                    items.push({ product_id: productId, quantity: value });
                }
            }

            axios.post('/order', {
                table_id: formData.get('table_id'),
                items: items
            }).then(response => {
                alert('Pesanan berhasil!');
                window.location.href = '/payment/' + response.data.order_id;
            }).catch(error => {
                console.error(error);
                alert('Terjadi kesalahan saat memesan.');
            });
        });
    </script>
</body>
</html>
