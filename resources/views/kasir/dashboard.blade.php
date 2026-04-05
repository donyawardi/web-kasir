<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Kasir
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Stat Cards --}}
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

        {{-- Aksi Cepat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Aksi Cepat</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kasir.orders.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Pesanan Baru
                </a>
                <a href="{{ route('kasir.orders.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Daftar Pesanan
                </a>
                <a href="{{ route('kasir.reports.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Laporan
                </a>
            </div>
        </div>

        {{-- Antrian Pending --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-gray-700">Antrian Aktif</h3>
                    @if($pending_queue->count())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">{{ $pending_queue->count() }}</span>
                    @endif
                </div>
                <a href="{{ route('kasir.orders.index', ['status' => 'pending']) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">Lihat semua &rarr;</a>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($pending_queue as $order)
                    @php
                        $statusColors = [
                            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'badge' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-400', 'ping' => 'bg-amber-400'],
                            'cooking' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'badge' => 'bg-orange-100 text-orange-700', 'dot' => 'bg-orange-400', 'ping' => 'bg-orange-400'],
                            'ready'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'badge' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-400', 'ping' => 'bg-emerald-400'],
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
                                        <p class="text-sm font-semibold text-gray-900">Meja {{ $order->table->table_number ?? '-' }}</p>
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
                                <a href="{{ route('kasir.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-xs font-medium text-gray-600 rounded-lg transition">
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
                        <p class="text-xs text-gray-300 mt-0.5">Semua pesanan sudah selesai. Kerja bagus!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div id="notif-toast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
        <div class="bg-white rounded-xl shadow-2xl border border-indigo-100 p-4 flex items-start gap-3 max-w-xs">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Pesanan Baru!</p>
                <p id="notif-text" class="text-xs text-gray-500 mt-0.5">Ada pesanan baru masuk.</p>
            </div>
            <button onclick="hideNotif()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <script>
    (function() {
        var lastOrderId = {{ $pending_queue->first()?->id ?? 'null' }};
        var checkUrl = @json(route('kasir.api.check-orders'));
        var toastEl = document.getElementById('notif-toast');
        var notifText = document.getElementById('notif-text');
        var pendingBadge = document.getElementById('stat-pending');
        var audioCtx = null;

        function playNotifSound() {
            try {
                if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                [0, 0.15].forEach(function(delay) {
                    var osc = audioCtx.createOscillator();
                    var gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.frequency.value = 880;
                    osc.type = 'sine';
                    gain.gain.value = 0.3;
                    osc.start(audioCtx.currentTime + delay);
                    osc.stop(audioCtx.currentTime + delay + 0.1);
                });
            } catch(e) {}
        }

        function showNotif(msg) {
            notifText.textContent = msg || 'Ada pesanan baru masuk.';
            toastEl.classList.remove('translate-x-full');
            setTimeout(hideNotif, 5000);
        }

        window.hideNotif = function() {
            toastEl.classList.add('translate-x-full');
        };

        function checkOrders() {
            fetch(checkUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (pendingBadge) pendingBadge.textContent = data.pending_count;
                    if (data.latest_order_id && lastOrderId && data.latest_order_id > lastOrderId) {
                        playNotifSound();
                        showNotif('Pesanan #' + data.latest_order_id + ' baru saja masuk!');
                    }
                    if (data.latest_order_id) lastOrderId = data.latest_order_id;
                })
                .catch(function() {});
        }

        fetch(checkUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.latest_order_id) lastOrderId = data.latest_order_id;
            }).catch(function() {});

        setInterval(checkOrders, 10000);

        document.addEventListener('click', function() {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }, { once: true });
    })();
    </script>
</x-app-layout>
