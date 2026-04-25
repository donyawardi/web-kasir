<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Role</h2>
    </x-slot>

<div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Daftar Role</h3>
            <p class="text-sm text-gray-400">Kelola role dan hak akses pengguna.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Role
        </a>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        @forelse($roles as $role)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ $role->name === 'admin' ? 'bg-purple-100' : ($role->name === 'kasir' ? 'bg-blue-100' : 'bg-gray-100') }}">
                                <svg class="w-5 h-5 {{ $role->name === 'admin' ? 'text-purple-600' : ($role->name === 'kasir' ? 'text-blue-600' : 'text-gray-500') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ ucfirst($role->name) }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $role->permissions_count }} permission{{ $role->permissions_count != 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus role {{ $role->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($role->permissions->count())
                        <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-gray-100">
                            @foreach($role->permissions->take(6) as $perm)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-[11px] font-medium text-gray-600">{{ $perm->name }}</span>
                            @endforeach
                            @if($role->permissions->count() > 6)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-[11px] font-medium text-indigo-600">+{{ $role->permissions->count() - 6 }} lainnya</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 flex flex-col items-center py-12 bg-white rounded-xl border border-gray-100">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">Belum ada role.</p>
            </div>
        @endforelse
    </div>
</div>
</x-app-layout>
