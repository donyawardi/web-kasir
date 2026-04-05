<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pesanan</h2>
    </x-slot>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua pesanan dari satu tempat</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow hover:bg-indigo-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Buat Pesanan
        </a>
    </div>

    {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Pesanan</div>
        </div>
        <div class="bg-yellow-50 rounded-xl shadow-sm border border-yellow-200 p-4 text-center">
            <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
            <div class="text-xs text-yellow-600 mt-1">Pending</div>
        </div>
        <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-700">{{ $stats['cooking'] }}</div>
            <div class="text-xs text-blue-600 mt-1">Dimasak</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $stats['completed'] }}</div>
            <div class="text-xs text-green-600 mt-1">Selesai</div>
        </div>
        <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-700">{{ $stats['cancelled'] }}</div>
            <div class="text-xs text-red-600 mt-1">Batal</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status')=='pending')>Pending</option>
                    <option value="cooking" @selected(request('status')=='cooking')>Dimasak</option>
                    <option value="ready" @selected(request('status')=='ready')>Siap</option>
                    <option value="completed" @selected(request('status')=='completed')>Selesai</option>
                    <option value="cancelled" @selected(request('status')=='cancelled')>Batal</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">No. Meja</label>
                <input type="text" name="table_number" value="{{ request('table_number') }}" placeholder="Cari meja..." class="rounded-lg border-gray-300 text-sm w-28 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.index', ['range' => 'today']) }}" class="px-3 py-2 rounded-lg text-xs font-medium {{ request('range') === 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Hari Ini</a>
                <a href="{{ route('admin.orders.index', ['range' => 'yesterday']) }}" class="px-3 py-2 rounded-lg text-xs font-medium {{ request('range') === 'yesterday' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Kemarin</a>
                <a href="{{ route('admin.orders.index', ['range' => 'this_week']) }}" class="px-3 py-2 rounded-lg text-xs font-medium {{ request('range') === 'this_week' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Minggu Ini</a>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
        </form>
    </div>

    {{-- Tabel Pesanan --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Meja</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $order->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-xs font-medium">
                                {{ $order->table->table_number ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'cooking' => 'bg-blue-100 text-blue-800',
                                    'ready' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'cooking' => 'Dimasak',
                                    'ready' => 'Siap',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Batal',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->payment->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $order->payment->status === 'success' ? 'Lunas' : 'Belum Bayar' }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium hover:bg-indigo-100 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-gray-400 mt-2">Tidak ada pesanan ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
</x-app-layout>
