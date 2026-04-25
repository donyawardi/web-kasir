<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $isAdmin ? 'Dashboard Admin' : 'Dashboard Kasir' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if ($isAdmin)

            {{-- Store Status Card --}}
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if($storeIsOpen)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Buka
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup
                            </span>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Status Toko</p>
                            <p class="text-xs text-gray-400">Jam buka: {{ $storeOpenTime }} – {{ $storeCloseTime }}</p>
                        </div>
                    </div>
                    <form action="{{ route('store.toggle') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 rounded text-sm font-medium shadow transition-colors
                                {{ $storeIsOpen
                                    ? 'bg-red-500 hover:bg-red-600 text-white'
                                    : 'bg-green-500 hover:bg-green-600 text-white' }}"
                            onclick="return confirm('{{ $storeIsOpen ? 'Tutup toko sekarang?' : 'Buka toko sekarang?' }}')">
                            {{ $storeIsOpen ? 'Tutup Toko' : 'Buka Toko' }}
                        </button>
                    </form>
                </div>

                {{-- Update Operating Hours & Closed Note --}}
                <form action="{{ route('store.hours') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-end gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jam Buka</label>
                        <input type="time" name="open_time" value="{{ $storeOpenTime }}"
                            class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Jam Tutup</label>
                        <input type="time" name="close_time" value="{{ $storeCloseTime }}"
                            class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400">
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-xs text-gray-500 mb-1">Pesan saat Tutup / Libur</label>
                        <select name="closed_note"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400 bg-white">
                            @php
                                $opt1 = 'Silahkan Datang Kembali Sesuai Jam Operasional Kami.';
                                $opt2 = '(Libur)';
                            @endphp
                            <option value="{{ $opt1 }}" {{ $closedNote === $opt1 ? 'selected' : '' }}>Silahkan Datang Kembali Sesuai Jam Operasional Kami.</option>
                            <option value="{{ $opt2 }}" {{ $closedNote === $opt2 ? 'selected' : '' }}>(Libur)</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">
                        Simpan
                    </button>
                </form>
            </div>

            {{-- Admin Stats Cards --}}
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

            {{-- Recent Orders --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Pesanan Terbaru</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Lihat semua &rarr;</a>
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
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $order->is_takeaway ? 'Bawa Pulang' : 'Meja ' . ($order->table->table_number ?? '-') }}</td>
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
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 hover:underline text-xs">Detail</a>
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
                                <p class="text-sm font-semibold text-gray-900">{{ $order->is_takeaway ? 'Bawa Pulang' : 'Meja ' . ($order->table->table_number ?? '-') }}</p>
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
                                <a href="{{ route('orders.show', $order->id) }}" class="block text-indigo-600 text-xs mt-1 hover:underline">Detail</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 text-sm py-4">Belum ada pesanan</p>
                    @endforelse
                </div>
            </div>

        @else

            {{-- Kasir: Store Status Banner --}}
            @if(!$storeIsOpen)
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-red-700">Toko Sedang Tutup</p>
                    <p class="text-xs text-red-500 mt-0.5">{{ $closedNote }}</p>
                    <p class="text-xs text-red-400 mt-1">Jam buka: {{ $storeOpenTime }} – {{ $storeCloseTime }}</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <p class="text-sm font-semibold text-green-700">Toko Sedang Buka</p>
                <span class="text-xs text-green-500 ml-auto">{{ $storeOpenTime }} – {{ $storeCloseTime }}</span>
            </div>
            @endif

            {{-- Kasir Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                        <p id="stat-pending" class="text-2xl font-extrabold text-amber-600 mt-0.5">{{ $stats['pending'] }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selesai Hari Ini</p>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $stats['completed_today'] }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Order Hari Ini</p>
                        <p class="text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $stats['orders_today'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Kasir Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Aksi Cepat</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Pesanan Baru
                    </a>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Daftar Pesanan
                    </a>
                </div>
            </div>

            {{-- Kasir Pending Queue --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-700">Antrian Aktif</h3>
                        @if($pending_queue->count())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">{{ $pending_queue->count() }}</span>
                        @endif
                    </div>
                    <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">Lihat semua &rarr;</a>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($pending_queue as $order)
                        @php
                            $statusColors = [
                                'pending' => ['bg' => 'bg-amber-50',   'badge' => 'bg-amber-100 text-amber-700',   'dot' => 'bg-amber-400',   'ping' => 'bg-amber-400'],
                                'cooking' => ['bg' => 'bg-orange-50',  'badge' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-400',  'ping' => 'bg-orange-400'],
                                'ready'   => ['bg' => 'bg-emerald-50', 'badge' => 'bg-emerald-100 text-emerald-700','dot'=> 'bg-emerald-400', 'ping' => 'bg-emerald-400'],
                            ];
                            $sc = $statusColors[$order->status] ?? $statusColors['pending'];
                            $waitMinutes = $order->created_at->diffInMinutes(now());
                        @endphp
                        <div class="px-5 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $sc['badge'] }} flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                        #{{ $order->id }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900">{{ $order->is_takeaway ? 'Bawa Pulang' : 'Meja ' . ($order->table->table_number ?? '-') }}</p>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $sc['badge'] }}">
                                                <span class="relative flex h-1.5 w-1.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $sc['ping'] }} opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $sc['dot'] }}"></span>
                                                </span>
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-400">
                                                <svg class="w-3 h-3 inline -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $waitMinutes }} menit lalu
                                            </span>
                                            <span class="text-xs font-bold text-gray-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 sm:flex-shrink-0">
                                    <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-xs font-medium text-gray-600 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="cooking">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition" onclick="return confirm('Mulai masak?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                                Masak
                                            </button>
                                        </form>
                                    @elseif($order->status === 'cooking')
                                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="ready">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition" onclick="return confirm('Tandai siap?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Siap
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$order->payment || $order->payment->status === 'success')
                                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="completed">
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded-lg transition" onclick="return confirm('Tandai selesai?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Selesai
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-medium rounded-lg cursor-not-allowed" title="Bayar dulu">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Selesai
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400">Tidak ada antrian aktif</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @endif

    </div>
</x-app-layout>
