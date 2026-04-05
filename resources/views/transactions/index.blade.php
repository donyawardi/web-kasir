<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Transaksi</h2>
    </x-slot>

@php
    $isAdmin = method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin');
@endphp
<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Daftar Transaksi</h1>
        @if($isAdmin)
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">
                Tambah Transaksi
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="status" class="rounded-md border-gray-300 text-sm shadow-sm">
                <option value="">Semua Status</option>
                <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300 text-sm shadow-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300 text-sm shadow-sm">
            <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded">Filter</button>
            @if(request()->filled('status') || request()->filled('date_from') || request()->filled('date_to'))
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meja</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-500">{{ $transactions->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">Meja {{ $transaction->table->table_number ?? '-' }}</td>
                            <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('transactions.show', $transaction->id) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    @if($isAdmin)
                                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="text-amber-600 hover:underline">Edit</a>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-gray-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
</x-app-layout>
