@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Tambah Meja</h2>

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Validasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tables.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="table_number" class="form-label">Nomor Meja</label>
            <input type="text" name="table_number" id="table_number" class="form-control @error('table_number') is-invalid @enderror" value="{{ old('table_number') }}" required>
            @error('table_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Terisi</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('tables.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
