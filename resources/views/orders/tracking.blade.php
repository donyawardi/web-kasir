<x-guest-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-white border-b border-gray-100 sticky top-0 z-30">
            <div class="max-w-lg mx-auto px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Ayam Bakar Seafood 79</h1>
                        <p class="text-xs text-gray-400 mt-0.5">Status Pesanan</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        Meja {{ $order->table->table_number ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto px-4 py-6 space-y-6">
            {{-- Order Number --}}
            <div class="text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wider">Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">#{{ $order->id }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            {{-- Payment Info Banner --}}
            @php
                $payment = $order->payment;
                $isPaid  = $payment && $payment->status === 'success';
            @endphp
            @if($payment)
                <div class="rounded-2xl border px-5 py-4 flex items-center gap-4
                    {{ $isPaid ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $isPaid ? 'bg-emerald-100' : 'bg-amber-100' }}">
                        @if($isPaid)
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold {{ $isPaid ? 'text-emerald-800' : 'text-amber-800' }}">
                            {{ $isPaid ? 'Pembayaran Lunas' : 'Belum Dibayar' }}
                        </p>
                        <p class="text-xs {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5">
                            @if($payment->payment_method === 'later' || $payment->payment_method === 'cashier')
                                Bayar di kasir
                            @elseif($payment->payment_method === 'qris')
                                Bayar via QRIS
                            @elseif($payment->payment_method === 'cash')
                                Tunai
                            @else
                                {{ ucfirst($payment->payment_method) }}
                            @endif
                        </p>
                    </div>
                    <p class="text-base font-extrabold {{ $isPaid ? 'text-emerald-700' : 'text-amber-700' }} flex-shrink-0">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                </div>
            @endif

            {{-- Status Tracker --}}
            @php
                $statuses = ['pending', 'cooking', 'ready', 'completed'];
                $labels = ['Menunggu', 'Dimasak', 'Siap', 'Selesai'];
                $icons = [
                    'pending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'cooking' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>',
                    'ready' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'completed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                ];
                $currentIndex = array_search($order->status, $statuses);
                if ($currentIndex === false) $currentIndex = -1;
                $isCancelled = $order->status === 'cancelled';
            @endphp

            @if($isCancelled)
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-red-700">Pesanan Dibatalkan</h3>
                    <p class="text-sm text-red-500 mt-1">Pesanan ini telah dibatalkan.</p>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6">
                    <div class="relative">
                        @foreach($statuses as $i => $status)
                            @php
                                $isDone = $i <= $currentIndex;
                                $isCurrent = $i === $currentIndex;
                            @endphp
                            <div class="flex items-start gap-4 {{ $i < count($statuses) - 1 ? 'pb-8' : '' }} relative">
                                {{-- Vertical line --}}
                                @if($i < count($statuses) - 1)
                                    <div class="absolute left-5 top-10 w-0.5 h-full -ml-px {{ $i < $currentIndex ? 'bg-indigo-500' : 'bg-gray-200' }}"></div>
                                @endif

                                {{-- Circle --}}
                                <div class="relative z-10 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $isDone ? ($isCurrent ? 'bg-indigo-600 shadow-lg shadow-indigo-200 ring-4 ring-indigo-100' : 'bg-indigo-500') : 'bg-gray-100' }}">
                                    <svg class="w-5 h-5 {{ $isDone ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icons[$status] !!}</svg>
                                </div>

                                {{-- Label --}}
                                <div class="pt-1.5">
                                    <p class="text-sm font-semibold {{ $isDone ? 'text-gray-900' : 'text-gray-400' }}">{{ $labels[$i] }}</p>
                                    @if($isCurrent)
                                        <p class="text-xs text-indigo-600 mt-0.5 flex items-center gap-1" id="status-hint">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                            </span>
                                            Saat ini
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Animated status message --}}
                <div id="status-message" class="text-center">
                    @if($order->status === 'pending')
                        <p class="text-sm text-amber-600 font-medium">Pesanan kamu sedang menunggu konfirmasi...</p>
                    @elseif($order->status === 'cooking')
                        <p class="text-sm text-orange-600 font-medium">Pesanan kamu sedang dimasak oleh dapur 🍳</p>
                    @elseif($order->status === 'ready')
                        <p class="text-sm text-green-600 font-medium">Pesanan kamu sudah siap! Silakan ambil 🎉</p>
                    @elseif($order->status === 'completed')
                        <p class="text-sm text-indigo-600 font-medium">Pesanan selesai. Terima kasih! 🙏</p>
                    @endif
                </div>
            @endif

            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Detail Pesanan</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($order->orderItems as $item)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->product->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $item->quantity }}x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                    <span class="text-sm font-medium text-gray-500">Total</span>
                    <span class="text-lg font-bold text-indigo-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Back button --}}
            <div class="text-center">
                <a href="{{ route('order.show', $order->table_id) }}" class="text-sm text-indigo-600 hover:underline">Pesan lagi di meja ini</a>
            </div>
        </div>
    </div>

    @unless($isCancelled || $order->status === 'completed')
    <script>
    (function() {
        var currentStatus = @json($order->status);
        var statusUrl = @json(route('api.order.status', $order->id));

        setInterval(function() {
            fetch(statusUrl)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status !== currentStatus) {
                        window.location.reload();
                    }
                })
                .catch(function() {});
        }, 5000);
    })();
    </script>
    @endunless
</x-guest-layout>
