@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manajemen Role</h1>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">Tambah Role</a>

    <table class="table mt-3">
        <tr>
            <th>Nama Role</th>
            <th>Aksi</th>
        </tr>
        @foreach($roles as $role)
        <tr>
            <td>{{ $role->name }}</td>
            <td>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
