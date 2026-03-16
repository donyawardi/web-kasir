
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">Daftar Pesanan</h1>

    <div class="card shadow-sm p-4">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Meja</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>Meja {{ $order->table->id }}</td>
                        <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            
                        <td>
                            @if ($order->status == 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif ($order->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            {{-- <ul>
                                @foreach ($order->orderItems as $item)
                                    <li>{{ $item->product->name }} ({{ $item->quantity }})</li>
                                @endforeach
                            </ul> --}}
                            {{-- <td> --}}
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                </form>

                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success btn-sm">Selesaikan</button>
                                </form>
                            
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection