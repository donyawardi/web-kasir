<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('kasir.orders.index') }}" class="p-1.5 -ml-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pesanan
            </h2>
        </div>
    </x-slot>

    @php
        $payment = $order->payment;
        $isPaid = $payment && $payment->status === 'success';
        $isUnpaid = $payment && in_array($payment->payment_method, ['later', 'cash']) && $payment->status === 'pending';
        $isActive = in_array($order->status, ['pending', 'cooking', 'ready']);
        $statusConfig = [
            'pending'   => ['label' => 'Menunggu',    'bg' => 'bg-amber-50',    'border' => 'border-amber-200',  'badge' => 'bg-amber-100 text-amber-800',     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',          'dot' => 'bg-amber-400',   'ring' => 'ring-amber-200'],
            'cooking'   => ['label' => 'Dimasak',     'bg' => 'bg-orange-50',   'border' => 'border-orange-200', 'badge' => 'bg-orange-100 text-orange-800',    'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z', 'dot' => 'bg-orange-400', 'ring' => 'ring-orange-200'],
            'ready'     => ['label' => 'Siap Diantar', 'bg' => 'bg-emerald-50',  'border' => 'border-emerald-200','badge' => 'bg-emerald-100 text-emerald-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',       'dot' => 'bg-emerald-400', 'ring' => 'ring-emerald-200'],
            'completed' => ['label' => 'Selesai',     'bg' => 'bg-indigo-50',   'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-800',    'icon' => 'M5 13l4 4L19 7',                                        'dot' => 'bg-indigo-400',  'ring' => 'ring-indigo-200'],
            'cancelled' => ['label' => 'Dibatalkan',  'bg' => 'bg-red-50',      'border' => 'border-red-200',    'badge' => 'bg-red-100 text-red-800',          'icon' => 'M6 18L18 6M6 6l12 12',                                  'dot' => 'bg-red-400',     'ring' => 'ring-red-200'],
        ];
        $cfg = $statusConfig[$order->status] ?? ['label' => ucfirst($order->status), 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'badge' => 'bg-gray-100 text-gray-800', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'dot' => 'bg-gray-400', 'ring' => 'ring-gray-200'];
    @endphp

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Order Header Card --}}
        <div class="{{ $cfg['bg'] }} border {{ $cfg['border'] }} rounded-2xl p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $cfg['badge'] }} flex items-center justify-center ring-4 {{ $cfg['ring'] }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg font-bold text-gray-900">Pesanan #{{ $order->id }}</h3>
                            @if($isActive)
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $cfg['dot'] }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $cfg['dot'] }}"></span>
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cfg['badge'] }}">{{ $cfg['label'] }}</span>
                            <span id="payment-badge">
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
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Meta info --}}
            <div class="mt-4 pt-4 border-t {{ $cfg['border'] }} grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Meja</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">
                        {{ $order->table->table_number ?? '-' }}
                        @if($order->is_takeaway)
                            <span class="inline-flex ml-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Take away</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Waktu</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                @if($isActive)
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Durasi</p>
                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->created_at->diffForHumans(null, true) }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Order Items Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Item Pesanan</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($order->orderItems as $item)
                <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->quantity }}x @ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900 flex-shrink-0">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-sm font-bold text-gray-700 uppercase tracking-wider">Total</span>
                <span class="text-lg font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Status Action Buttons --}}
        @if(!in_array($order->status, ['completed', 'cancelled']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Ubah Status</h3>
                <div class="flex flex-wrap gap-2">
                    @if($order->status === 'pending')
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cooking">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition" onclick="return confirm('Mulai masak pesanan ini?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                                Mulai Masak
                            </button>
                        </form>
                    @endif
                    @if($order->status === 'cooking')
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-sm transition" onclick="return confirm('Tandai pesanan siap diantar?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Siap Diantar
                            </button>
                        </form>
                    @endif
                    @if(in_array($order->status, ['pending', 'cooking', 'ready']))
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition" onclick="return confirm('Tandai pesanan ini selesai?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Selesai
                            </button>
                        </form>
                    @endif
                    @if(!$payment || $payment->status !== 'success')
                        <span id="cancel-wrapper">
                        <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-xl shadow-sm transition" onclick="return confirm('Batalkan pesanan ini?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Batalkan
                            </button>
                        </form>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Payment Processing for "Bayar Nanti" orders --}}
        @if($isUnpaid && !in_array($order->status, ['cancelled']))
            <div id="payment-section" class="bg-white rounded-2xl border border-yellow-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-yellow-50 border-b border-yellow-200 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-yellow-900">Proses Pembayaran</h3>
                        <p class="text-xs text-yellow-700">Pesanan ini belum dibayar. Pilih metode:</p>
                    </div>
                </div>
                <div class="p-5">
                    <form action="{{ route('kasir.orders.process-payment', $order->id) }}" method="POST" id="pay-later-form">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <label class="relative">
                                <input type="radio" name="payment_method" value="cash" class="peer sr-only" checked>
                                <div class="flex items-center justify-center gap-2 px-3 py-3 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span class="text-sm font-semibold">Cash</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="payment_method" value="qris" class="peer sr-only">
                                <div class="flex items-center justify-center gap-2 px-3 py-3 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 hover:border-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span class="text-sm font-semibold">QRIS</span>
                                </div>
                            </label>
                        </div>

                        <div id="pay-cash-group">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Uang Diterima</label>
                            <input type="number" step="1" min="0" name="cash_received" id="pay-cash-input" placeholder="0" class="block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-2">
                            <div id="pay-quick-cash" class="grid grid-cols-4 gap-1.5 mb-3">
                                <button type="button" data-digit="1" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">1</button>
                                <button type="button" data-digit="2" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">2</button>
                                <button type="button" data-digit="3" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">3</button>
                                <button type="button" data-digit="4" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">4</button>
                                <button type="button" data-digit="5" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">5</button>
                                <button type="button" data-digit="6" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">6</button>
                                <button type="button" data-digit="7" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">7</button>
                                <button type="button" data-digit="8" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">8</button>
                                <button type="button" data-digit="9" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">9</button>
                                <button type="button" data-digit="0" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">0</button>
                                <button type="button" data-digit="000" class="pq-btn py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 active:bg-gray-200 border border-gray-200 text-gray-800 text-base font-bold transition">000</button>
                                <button type="button" data-action="backspace" class="pq-btn py-2.5 rounded-lg bg-red-50 hover:bg-red-100 active:bg-red-200 border border-red-200 text-red-600 text-base font-bold transition flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l7-7 11 0v14H10L3 12z"/></svg>
                                </button>
                                <button type="button" data-val="uang_pas" class="pq-btn col-span-3 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 border border-emerald-200 text-emerald-700 text-sm font-semibold transition">Uang Pas</button>
                                <button type="button" data-action="clear" class="pq-btn py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 active:bg-gray-300 border border-gray-300 text-gray-600 text-xs font-semibold transition">C</button>
                            </div>
                            <div id="pay-change-row" class="hidden items-center justify-between text-sm mb-3 px-1">
                                <span class="text-gray-500">Kembalian</span>
                                <span id="pay-change" class="font-semibold text-emerald-600">Rp0</span>
                            </div>
                        </div>

                        <button type="submit" id="pay-submit-btn" disabled class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Bayar Rp{{ number_format($order->total_price, 0, ',', '.') }}
                        </button>
                    </form>
                </div>
            </div>

            <script>
            (function() {
                const total = {{ $order->total_price }};
                const cashInput = document.getElementById('pay-cash-input');
                const cashGroup = document.getElementById('pay-cash-group');
                const changeRow = document.getElementById('pay-change-row');
                const changeEl = document.getElementById('pay-change');
                const submitBtn = document.getElementById('pay-submit-btn');
                const radios = document.querySelectorAll('#pay-later-form input[name="payment_method"]');
                const paymentSection = document.getElementById('payment-section');
                const paymentBadge = document.getElementById('payment-badge');

                function fmt(v) { return 'Rp' + Number(v||0).toLocaleString('id-ID'); }

                function update() {
                    const method = document.querySelector('#pay-later-form input[name="payment_method"]:checked').value;
                    const isCash = method === 'cash';
                    cashGroup.style.display = isCash ? '' : 'none';

                    if (isCash) {
                        const received = Number(cashInput.value||0);
                        const change = received - total;
                        if (received > 0) {
                            changeRow.classList.remove('hidden');
                            changeRow.classList.add('flex');
                            changeEl.textContent = fmt(change >= 0 ? change : 0);
                            changeEl.className = 'font-semibold ' + (change >= 0 ? 'text-emerald-600' : 'text-red-500');
                        } else {
                            changeRow.classList.add('hidden');
                            changeRow.classList.remove('flex');
                        }
                        submitBtn.disabled = received < total;
                    } else {
                        submitBtn.disabled = false;
                    }
                }

                radios.forEach(r => r.addEventListener('change', update));
                cashInput.addEventListener('input', update);

                document.getElementById('pay-quick-cash').addEventListener('click', function(e) {
                    const btn = e.target.closest('.pq-btn');
                    if (!btn) return;
                    if (btn.dataset.val === 'uang_pas') {
                        cashInput.value = total;
                    } else if (btn.dataset.digit !== undefined) {
                        const cur = cashInput.value === '0' ? '' : (cashInput.value || '');
                        cashInput.value = cur + btn.dataset.digit;
                    } else if (btn.dataset.action === 'backspace') {
                        const cur = cashInput.value || '';
                        cashInput.value = cur.slice(0, -1) || '';
                    } else if (btn.dataset.action === 'clear') {
                        cashInput.value = '';
                    }
                    update();
                });

                // AJAX form submit
                document.getElementById('pay-later-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = e.target;
                    const method = form.querySelector('input[name="payment_method"]:checked').value;

                    if (method === 'qris') {
                        form.submit();
                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...';

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Bayar ' + fmt(total);
                            return;
                        }

                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }

                        // Success — update UI
                        paymentBadge.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Lunas</span>';

                        // Hide cancel button after payment success
                        var cancelWrapper = document.getElementById('cancel-wrapper');
                        if (cancelWrapper) cancelWrapper.remove();

                        const receiptUrl = @json(route('orders.receipt', $order->id));
                        const change = data.change || 0;
                        paymentSection.className = 'bg-emerald-50 rounded-2xl border border-emerald-200 shadow-sm overflow-hidden';
                        paymentSection.innerHTML =
                            '<div class="p-6 text-center">' +
                            '<div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">' +
                            '<svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                            '</div>' +
                            '<h3 class="text-lg font-bold text-emerald-800">Pembayaran Berhasil!</h3>' +
                            '<p class="text-sm text-emerald-700 mt-1">Metode: Cash</p>' +
                            (change > 0 ? '<p class="text-lg font-bold text-emerald-800 mt-2">Kembalian: ' + fmt(change) + '</p>' : '') +
                            '<p class="text-xs text-emerald-600 mt-3">Kembali ke daftar pesanan...</p>' +
                            '<a href="' + receiptUrl + '?autoprint=1" target="_blank" class="mt-3 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>' +
                            'Cetak Struk</a>' +
                            '</div>';

                        // Redirect ke daftar pesanan setelah 5 detik
                        setTimeout(function() { window.location.href = @json(route('kasir.orders.index')); }, 5000);
                    })
                    .catch(() => {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Bayar ' + fmt(total);
                    });
                });

                update();
            })();
            </script>
        @endif

        {{-- Bottom Action Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center gap-3">
            <a href="{{ route('kasir.orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <button type="button" id="btn-cetak-struk" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Struk
            </button>
        </div>

        {{-- Redirect Countdown Banner --}}
        <div id="redirect-banner" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-sm text-blue-800">
                Mengalihkan ke <span class="font-semibold">Manajemen Pesanan</span> dalam <span id="countdown-sec" class="font-bold text-blue-900">5</span> detik...
            </p>
            <a href="{{ route('kasir.orders.index') }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Klik untuk langsung ke halaman pesanan</a>
        </div>

    </div>

<script>
(function() {
    const redirectUrl = @json(route('kasir.orders.index'));
    const banner = document.getElementById('redirect-banner');
    const countdownEl = document.getElementById('countdown-sec');
    let timer = null;

    function startCountdown(seconds) {
        if (timer) return;
        let remaining = seconds;
        banner.classList.remove('hidden');
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

    // Auto-redirect jika status selesai/batal (5 detik)
    @if(in_array($order->status, ['completed', 'cancelled']))
        startCountdown(15);
    @endif

    // Cetak struk: buka popup, tunggu print selesai baru redirect
    const cetakBtn = document.getElementById('btn-cetak-struk');
    if (cetakBtn) {
        cetakBtn.addEventListener('click', function() {
            const receiptUrl = @json(route('orders.receipt', $order->id)) + '?autoprint=1';
            window.open(receiptUrl, 'receipt', 'width=420,height=600');
        });
    }

    // Dengarkan pesan dari halaman struk setelah print selesai
    window.addEventListener('message', function(e) {
        if (e.data === 'receipt-printed') {
            startCountdown(15);
        }
    });
})();
</script>
</x-app-layout>
