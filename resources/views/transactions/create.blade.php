@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Transaksi</h1>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="table_id" class="form-label">Pilih Meja</label>
            <select class="form-control" name="table_id" id="table_id" required>
                <option value="">-- Pilih Meja --</option>
                @foreach($tables as $table)
                    <option value="{{ $table->id }}">{{ $table->table_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">Total Harga</label>
            <input type="number" class="form-control" name="total_price" id="total_price" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
