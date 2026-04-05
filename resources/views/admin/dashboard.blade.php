<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pesanan</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                <p class="mt-1 text-sm text-yellow-600">{{ $stats['pending_orders'] }} menunggu</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pesanan Selesai</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ $stats['completed_orders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan</p>
                <p class="mt-1 text-2xl font-bold text-indigo-600">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-400">dari pesanan selesai</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Produk Tersedia</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['available_products'] }}</p>
                <p class="mt-1 text-sm text-gray-400">dari {{ $stats['total_products'] }} total / {{ $stats['total_tables'] }} meja</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Aksi Cepat</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">
                    + Produk Baru
                </a>
                <a href="{{ route('admin.tables.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">
                    + Tambah Meja
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">
                    Lihat Produk
                </a>
                <a href="{{ route('admin.tables.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">
                    Lihat Meja
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">
                    Lihat Transaksi
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded shadow">
                    Laporan Penjualan
                </a>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Pesanan Terbaru</h3>
            </div>

            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meja</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($recent_orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-500">#{{ $order->id }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">Meja {{ $order->table->table_number ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $order->orderItems->count() }} item</td>
                                <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $sc = match($order->status) {
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default      => 'bg-yellow-100 text-yellow-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('payment.show', $order->id) }}" class="text-indigo-600 hover:underline text-xs">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-center text-gray-400">Belum ada pesanan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="sm:hidden p-4 space-y-3">
                @forelse ($recent_orders as $order)
                    <div class="bg-gray-50 rounded p-3 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Meja {{ $order->table->table_number ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $order->orderItems->count() }} item &middot; Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $sc = match($order->status) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default      => 'bg-yellow-100 text-yellow-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">{{ ucfirst($order->status) }}</span>
                            <a href="{{ route('payment.show', $order->id) }}" class="block text-indigo-600 text-xs mt-1 hover:underline">Detail</a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-4">Belum ada pesanan</p>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>

