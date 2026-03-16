@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Transaksi</h1>
    <a href="{{ route('transactions.create') }}" class="btn btn-primary">Tambah Transaksi</a>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Meja</th>
                <th>Total Harga</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transaction->table->table_number }}</td>
                <td>Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($transaction->payment_status) }}</td>
                <td>
                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
