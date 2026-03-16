@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Meja</h1>
    <form action="{{ route('tables.update', $table->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="table_number" class="form-label">Nomor Meja</label>
            <input type="text" class="form-control" id="table_number" name="table_number" value="{{ $table->table_number }}" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Terisi</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
