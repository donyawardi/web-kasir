@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pembayaran</h2>
    <p><strong>Nomor Meja:</strong> {{ $order->table->table_number }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Total: Rp{{ number_format($order->total_price, 0, ',', '.') }}</h4>

    @auth
        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('kasir'))
            <form action="{{ route('payment.complete', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success">Tandai Lunas</button>
            </form>
        @endif
    @endauth
</div>
@endsection
