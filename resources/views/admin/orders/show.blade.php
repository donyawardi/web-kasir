<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 leading-tight">Pesanan #{{ $order->id }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $statusColors = [
                        'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'cooking'   => 'bg-blue-100 text-blue-800 border-blue-200',
                        'ready'     => 'bg-purple-100 text-purple-800 border-purple-200',
                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                    $statusLabels = [
                        'pending'   => 'Pending',
                        'cooking'   => 'Dimasak',
                        'ready'     => 'Siap Disajikan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                    $statusIcons = [
                        'pending'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'cooking'   => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',
                        'ready'     => 'M5 13l4 4L19 7',
                        'completed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-semibold border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcons[$order->status] ?? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                </span>
                <button type="button" onclick="document.getElementById('btn-cetak-struk').click()"
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk
                </button>
            </div>
        </div>
    </x-slot>

<div class="py-6 px-4 sm:px-6 lg:px-8" style="max-width:1280px; margin:0 auto;">

    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Redirect Banner --}}
    <div id="redirect-banner" class="hidden mb-4 bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-indigo-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <p class="text-sm text-indigo-800">Mengalihkan ke daftar pesanan dalam <span id="countdown-sec" class="font-bold text-indigo-900">15</span> detik...</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 whitespace-nowrap">Langsung ke sana â†’</a>
    </div>

    <div class="flex gap-6 items-start">

        {{-- LEFT: Order Items + Status --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Info Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Meja</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 10V6m4 4V6M3 6h18M3 18h18"/></svg>
                        </div>
                        <div>
                            @if($order->is_takeaway)
                                <p class="text-sm font-bold text-orange-600">Take Away</p>
                            @else
                                <p class="text-sm font-bold text-gray-800">Meja {{ $order->table->table_number ?? '-' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Total Item</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-sm font-bold text-gray-800">{{ $order->orderItems->sum('quantity') }} pcs</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Pembayaran</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg {{ $order->payment?->status === 'success' ? 'bg-green-50' : 'bg-orange-50' }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 {{ $order->payment?->status === 'success' ? 'text-green-500' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            @if($order->payment?->status === 'success')
                                <p class="text-sm font-bold text-green-600">Lunas</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $order->payment->payment_method ?? '' }}</p>
                            @else
                                <p class="text-sm font-bold text-orange-500">Belum Bayar</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1.5">Waktu</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $order->created_at->format('H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800">Item Pesanan</h3>
                    <span class="text-xs text-gray-400">{{ $order->orderItems->count() }} jenis produk</span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($order->orderItems as $item)
                    <div class="px-5 py-3 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center flex-shrink-0">
                            @if($item->product?->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-9 h-9 rounded-xl object-cover">
                            @else
                                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">Rp {{ number_format($item->price, 0, ',', '.') }} / pcs</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">Ã— {{ $item->quantity }}</span>
                            <span class="text-sm font-bold text-gray-900 w-24 text-right">Rp {{ number_format($item->subtotal ?? $item->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-600">Total Pesanan</span>
                    <span class="text-xl font-extrabold text-indigo-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Update Status --}}
            @if(!in_array($order->status, ['completed', 'cancelled']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-bold text-gray-800 mb-3">Ubah Status Pesanan</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['pending' => ['label'=>'Pending','color'=>'yellow'], 'cooking' => ['label'=>'Dimasak','color'=>'blue'], 'ready' => ['label'=>'Siap Disajikan','color'=>'purple'], 'completed' => ['label'=>'Selesai','color'=>'green'], 'cancelled' => ['label'=>'Batalkan','color'=>'red']] as $val => $info)
                        @if($val !== $order->status)
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" name="status" value="{{ $val }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                                @if($val === 'cancelled') bg-red-50 text-red-700 border-red-200 hover:bg-red-100
                                @elseif($val === 'completed') bg-green-50 text-green-700 border-green-200 hover:bg-green-100
                                @elseif($val === 'cooking') bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100
                                @elseif($val === 'ready') bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100
                                @else bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100 @endif">
                                @if($val === 'cancelled')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @elseif($val === 'completed')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3-3 3M6 12h12"/></svg>
                                @endif
                                {{ $info['label'] }}
                            </button>
                        </form>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Print & navigate --}}
            <div class="flex items-center justify-between gap-3">
                <button type="button" id="btn-cetak-struk"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-50 transition">
                    â† Kembali ke Daftar
                </a>
            </div>
        </div>

        {{-- RIGHT: Payment Panel --}}
        <div class="w-80 flex-shrink-0 sticky top-6 space-y-4">

            {{-- Payment status card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800">Status Pembayaran</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-800 capitalize">{{ $order->payment?->payment_method ?? 'â€”' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status</span>
                        @if($order->payment?->status === 'success')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Lunas
                            </span>
                        @elseif($order->payment)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-100 text-orange-700 text-xs font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg> Menunggu
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Belum ada</span>
                        @endif
                    </div>
                    @if($order->payment?->cash_received)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Uang Diterima</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($order->payment->cash_received, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-semibold text-emerald-600">Rp {{ number_format(max(0, $order->payment->cash_received - $order->total_price), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-700">Total</span>
                        <span class="text-lg font-extrabold text-indigo-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Cash Payment Form --}}
            @if($order->payment && $order->payment->status === 'pending' && $order->status !== 'cancelled')
            <div class="bg-white rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-orange-500 to-amber-500 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm">Proses Pembayaran Cash</h3>
                        <p class="text-orange-100 text-xs">Total: <span class="font-bold text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
                    </div>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('payment.complete', $order) }}" id="admin-pay-form">
                        @csrf
                        @method('PUT')

                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Uang Diterima (Rp)</label>
                        <input type="number" step="1" min="0" id="admin-cash-input" name="cash_received" placeholder="0"
                            class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-3 font-bold text-lg text-center py-2.5">

                        <div id="admin-numpad" class="grid grid-cols-4 gap-1.5 mb-3">
                            @foreach(['1','2','3','4','5','6','7','8','9','0','000'] as $d)
                            <button type="button" data-digit="{{ $d }}"
                                class="aq-btn py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-sm font-bold transition">{{ $d }}</button>
                            @endforeach
                            <button type="button" data-action="backspace"
                                class="aq-btn py-2.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 font-bold transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l7-7 11 0v14H10L3 12z"/></svg>
                            </button>
                            <button type="button" data-val="uang_pas"
                                class="aq-btn col-span-3 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-bold transition">
                                Uang Pas
                            </button>
                            <button type="button" data-action="clear"
                                class="aq-btn py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-500 text-xs font-bold transition">C</button>
                        </div>

                        <div id="admin-change-row" class="hidden items-center justify-between bg-emerald-50 rounded-xl px-4 py-2.5 mb-3">
                            <span class="text-xs text-gray-600 font-medium">Kembalian</span>
                            <span id="admin-change" class="font-extrabold text-emerald-600 text-sm">Rp 0</span>
                        </div>

                        <button type="submit" id="admin-pay-btn" disabled
                            class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-200 disabled:cursor-not-allowed text-white disabled:text-gray-400 font-bold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Konfirmasi Bayar
                        </button>
                    </form>
                </div>
            </div>
            @elseif($order->payment?->status === 'success')
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-green-800">Pembayaran Lunas</p>
                <p class="text-xs text-green-600 mt-1 capitalize">{{ $order->payment->payment_method ?? 'cash' }}</p>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
(function() {
    const redirectUrl = @json(route('admin.orders.index'));
    const banner = document.getElementById('redirect-banner');
    const countdownEl = document.getElementById('countdown-sec');
    let timer = null;

    function startCountdown(seconds) {
        if (timer) return;
        let remaining = seconds;
        banner.classList.remove('hidden');
        banner.classList.add('flex');
        countdownEl.textContent = remaining;
        timer = setInterval(function() {
            remaining--;
            countdownEl.textContent = remaining;
            if (remaining <= 0) {
                clearInterval(timer);
                window.location.href = redirectUrl;
            }
        }, 1000);
    }

    @if(in_array($order->status, ['completed', 'cancelled']))
        startCountdown(15);
    @endif

    const cetakBtn = document.getElementById('btn-cetak-struk');
    if (cetakBtn) {
        cetakBtn.addEventListener('click', function() {
            const receiptUrl = @json(route('orders.receipt', $order->id)) + '?autoprint=1';
            window.open(receiptUrl, 'receipt', 'width=420,height=600');
        });
    }

    window.addEventListener('message', function(e) {
        if (e.data === 'receipt-printed') {
            startCountdown(15);
        }
    });

    // Cash numpad
    const cashInput = document.getElementById('admin-cash-input');
    if (cashInput) {
        const total = {{ $order->total_price }};
        const changeRow = document.getElementById('admin-change-row');
        const changeEl = document.getElementById('admin-change');
        const submitBtn = document.getElementById('admin-pay-btn');

        function fmt(v) { return 'Rp ' + Number(v||0).toLocaleString('id-ID'); }

        function update() {
            const received = Number(cashInput.value || 0);
            const change = received - total;
            if (received > 0) {
                changeRow.classList.remove('hidden');
                changeRow.classList.add('flex');
                changeEl.textContent = fmt(change >= 0 ? change : 0);
                changeEl.className = 'font-extrabold text-sm ' + (change >= 0 ? 'text-emerald-600' : 'text-red-500');
            } else {
                changeRow.classList.add('hidden');
                changeRow.classList.remove('flex');
            }
            submitBtn.disabled = received < total;
        }

        cashInput.addEventListener('input', update);

        document.getElementById('admin-numpad').addEventListener('click', function(e) {
            const btn = e.target.closest('.aq-btn');
            if (!btn) return;
            if (btn.dataset.val === 'uang_pas') {
                cashInput.value = total;
            } else if (btn.dataset.digit !== undefined) {
                cashInput.value = (cashInput.value === '0' ? '' : (cashInput.value || '')) + btn.dataset.digit;
            } else if (btn.dataset.action === 'backspace') {
                cashInput.value = (cashInput.value || '').slice(0, -1) || '';
            } else if (btn.dataset.action === 'clear') {
                cashInput.value = '';
            }
            update();
        });
    }
})();
</script>
</x-app-layout>
