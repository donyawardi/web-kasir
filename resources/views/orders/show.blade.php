@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Pesanan #{{ $order->id }}</h1>
    <p><strong>Meja:</strong> {{ $order->table->id }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>

    <h3>Daftar Item</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                <td>Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('orders.index') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection
