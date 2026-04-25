<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pesanan</h2>
    </x-slot>

    @php
        $isAdmin = auth()->user()?->hasRole('admin');
        $indexRoute  = 'orders.index';
        $createRoute = 'orders.create';
        $showRoute   = 'orders.show';

        $statusConfig = [
            'pending'   => ['label' => 'Menunggu',    'bg' => 'bg-amber-50',   'border' => 'border-amber-200',  'badge' => 'bg-amber-100 text-amber-800',    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',          'dot' => 'bg-amber-400'],
            'cooking'   => ['label' => 'Dimasak',     'bg' => 'bg-orange-50',  'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-800',   'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z', 'dot' => 'bg-orange-400'],
            'ready'     => ['label' => 'Siap',        'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200','badge' => 'bg-emerald-100 text-emerald-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',       'dot' => 'bg-emerald-400'],
            'completed' => ['label' => 'Selesai',     'bg' => 'bg-indigo-50',  'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-800',   'icon' => 'M5 13l4 4L19 7',                                        'dot' => 'bg-indigo-400'],
            'cancelled' => ['label' => 'Dibatalkan',  'bg' => 'bg-red-50',     'border' => 'border-red-200',    'badge' => 'bg-red-100 text-red-800',          'icon' => 'M6 18L18 6M6 6l12 12',                                  'dot' => 'bg-red-400'],
        ];
    @endphp

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        {{-- Header + Tombol Buat Pesanan --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Pesanan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola semua pesanan dari satu tempat</p>
            </div>
            <a href="{{ route($createRoute) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Pesanan
            </a>
        </div>

        {{-- Statistik (admin only) --}}
        @if($isAdmin && isset($stats))
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
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
        @endif

        {{-- Filter Bar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route($indexRoute) }}" class="space-y-3">
                {{-- Quick date pills --}}
                @php
                    $todayQuery     = array_merge(request()->except(['date_from','date_to','range']), ['range' => 'today']);
                    $yesterdayQuery = array_merge(request()->except(['date_from','date_to','range']), ['range' => 'yesterday']);
                    $weekQuery      = array_merge(request()->except(['date_from','date_to','range']), ['range' => 'this_week']);
                    $defaultRange   = $isAdmin ? null : 'today';
                @endphp
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wider mr-1">Periode:</span>
                    <a href="{{ route($indexRoute, $todayQuery) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('range', $defaultRange) === 'today' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Hari Ini</a>
                    <a href="{{ route($indexRoute, $yesterdayQuery) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('range') === 'yesterday' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Kemarin</a>
                    <a href="{{ route($indexRoute, $weekQuery) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ request('range') === 'this_week' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Minggu Ini</a>
                </div>
                <div class="flex flex-wrap items-end gap-2">
                    <div class="flex-1 min-w-[120px]">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach(['pending' => 'Menunggu', 'cooking' => 'Dimasak', 'ready' => 'Siap', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Meja</label>
                        <input type="text" name="table_number" value="{{ request('table_number') }}" placeholder="No"
                            class="w-full rounded-lg border-gray-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Dari</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Sampai</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Filter
                    </button>
                    @if(request()->filled('status') || request()->filled('table_number') || request()->filled('date_from') || request()->filled('date_to'))
                        <a href="{{ route($indexRoute) }}" class="inline-flex items-center gap-1 px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Order Cards --}}
        <div class="space-y-3">
            @forelse ($orders as $order)
                @php
                    $cfg = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'badge' => 'bg-gray-100 text-gray-800', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'dot' => 'bg-gray-400'];
                    $isPaid   = $order->payment && $order->payment->status === 'success';
                    $isUnpaid = $order->payment && $order->payment->payment_method === 'later' && $order->payment->status === 'pending';
                    $isActive = in_array($order->status, ['pending', 'cooking', 'ready']);
                @endphp
                <div class="group {{ $cfg['bg'] }} border {{ $cfg['border'] }} rounded-xl p-4 sm:p-5 transition hover:shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                        {{-- Left: Order info --}}
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $cfg['badge'] }} flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-gray-900">#{{ $order->id }}</span>
                                    <span class="text-sm font-semibold text-gray-700">{{ $order->is_takeaway ? 'Take Away' : 'Meja ' . ($order->table->table_number ?? '-') }}</span>
                                    @if($isActive)
                                        <span class="relative flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $cfg['dot'] }} opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $cfg['dot'] }}"></span>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cfg['badge'] }}">{{ $cfg['label'] }}</span>
                                    @if($isUnpaid)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Belum Bayar
                                        </span>
                                    @elseif($isPaid)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Lunas
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                                    <span>{{ $order->created_at->format('H:i') }}</span>
                                    @if($isActive)
                                        <span>{{ $order->created_at->diffForHumans(null, true) }} lalu</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Center: Price --}}
                        <div class="sm:text-right flex-shrink-0">
                            <p class="text-lg font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>

                        {{-- Right: Actions --}}
                        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                            {{-- Tombol status cepat (kasir dan admin) --}}
                            @if($isUnpaid && !in_array($order->status, ['cancelled']))
                                <a href="{{ route($showRoute, $order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Bayar
                                </a>
                            @endif
                            @if($order->status === 'pending')
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="cooking">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-orange-500 text-white hover:bg-orange-600 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                        Masak
                                    </button>
                                </form>
                            @elseif($order->status === 'cooking')
                                <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="ready">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Siap
                                    </button>
                                </form>
                            @elseif($order->status === 'ready')
                                @if($isPaid || !$order->payment)
                                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="completed">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition" onclick="return confirm('Tandai selesai?')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Selesai
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-200 text-gray-400 cursor-not-allowed" title="Bayar dulu">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </span>
                                @endif
                            @endif
                            <a href="{{ route($showRoute, $order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </a>
                            <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Struk
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-gray-500 font-medium">Belum ada pesanan</p>
                    <p class="text-gray-400 text-sm mt-1">Pesanan akan muncul di sini saat ada yang masuk</p>
                    <a href="{{ route($createRoute) }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Pesanan Baru
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="mt-2">{{ $orders->links() }}</div>
        @endif

    </div>
</x-app-layout>
