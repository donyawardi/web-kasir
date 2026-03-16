@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Transaksi</h1>

    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="table_id" class="form-label">Pilih Meja</label>
            <select class="form-control" name="table_id" id="table_id" required>
                @foreach($tables as $table)
                    <option value="{{ $table->id }}" {{ $transaction->table_id == $table->id ? 'selected' : '' }}>
                        {{ $table->table_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">Total Harga</label>
            <input type="number" class="form-control" name="total_price" id="total_price" value="{{ $transaction->total_price }}" required>
        </div>

        <div class="mb-3">
            <label for="payment_status" class="form-label">Status Pembayaran</label>
            <select class="form-control" name="payment_status" id="payment_status" required>
                <option value="pending" {{ $transaction->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
