@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Meja</h1>
    <a href="{{ route('tables.create') }}" class="btn btn-primary">Tambah Meja</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nomor Meja</th>
                <th>Status</th>
                <th>QR Code</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tables as $table)
            <tr>
                <td>{{ $table->id }}</td>
                <td>{{ $table->table_number }}</td>
                <td>
                    <span class="badge {{ $table->status == 'available' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($table->status) }}
                    </span>
                </td>
                <td>
                    @if ($table->qr_code)
                        {{-- <img src="data:image/svg+xml;base64,{{ $table->qr_code }}" width="100" alt="QR Code"> --}}
                        <img src="{{ $table->qr_code }}" alt="QR Code">
                    @else
                        <span class="text-danger">QR Code tidak tersedia</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('tables.edit', $table->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('tables.destroy', $table->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus meja ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
