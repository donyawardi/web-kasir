<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #{{ $order->id }} - Ayam Bakar Seafood 79</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-lg mx-auto py-6 px-4">
        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Ayam Bakar Seafood 79</h1>
            <p class="text-sm text-gray-500">Pembayaran Pesanan</p>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 space-y-5">
                {{-- Order Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-900">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Meja</p>
                        <p class="font-semibold text-gray-900">{{ $order->table->table_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <span id="status-badge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $order->status === 'completed' ? 'Lunas' : 'Menunggu Pembayaran' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Bayar</p>
                        <p class="text-xl font-bold text-indigo-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Item Details --}}
                <div class="border-t pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Rincian Pesanan</p>
                    <div class="space-y-1">
                        @foreach ($order->orderItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $item->product->name ?? 'Item' }} x{{ $item->quantity }}</span>
                                <span class="text-gray-800">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($order->status === 'completed')
                    {{-- Already paid --}}
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <svg class="mx-auto h-10 w-10 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-semibold">Pembayaran Berhasil!</p>
                        <p class="text-green-600 text-sm mt-1">Terima kasih atas pesanan Anda.</p>
                        <a href="{{ route('order.track', $order->id) }}" class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Status Pesanan
                        </a>
                    </div>
                @else
                    {{-- Payment Options --}}
                    <div id="payment-section" class="space-y-3">
                        <p class="text-sm font-medium text-gray-700">Pilih Metode Pembayaran</p>
                        <button id="pay-cash-button"
                            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Bayar Tunai di Kasir
                        </button>
                        @if(!empty($snapToken))
                        <button id="pay-button"
                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Bayar Online (QRIS / E-Wallet)
                        </button>
                        @endif
                        <p class="text-xs text-gray-400 text-center">{{ !empty($snapToken) ? 'Pilih bayar tunai atau online' : 'Silakan bayar tunai di kasir' }}</p>
                    </div>

                    {{-- Cash pending state (hidden by default) --}}
                    <div id="payment-cash-pending" class="hidden bg-emerald-50 border border-emerald-200 rounded-lg p-5 text-center">
                        <svg class="mx-auto h-12 w-12 text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-emerald-800 font-semibold text-lg">Silakan Bayar di Kasir</p>
                        <p class="text-emerald-600 text-sm mt-1">Tunjukkan halaman ini ke kasir sebagai bukti pesanan</p>
                        <div class="mt-3 bg-white rounded-lg p-3 border border-emerald-100">
                            <p class="text-xs text-gray-500">Total yang harus dibayar</p>
                            <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 mt-1">Pesanan #{{ $order->id }}</p>
                        </div>
                        <a href="{{ route('order.track', $order->id) }}" class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Status Pesanan
                        </a>
                    </div>

                    {{-- Success state (hidden by default) --}}
                    <div id="payment-success" class="hidden bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <svg class="mx-auto h-10 w-10 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-semibold">Pembayaran Berhasil!</p>
                        <p class="text-green-600 text-sm mt-1">Pesanan Anda sedang diproses.</p>
                        <a href="{{ route('order.track', $order->id) }}" class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Lihat Status Pesanan
                        </a>
                    </div>

                    {{-- Pending state (hidden by default) --}}
                    <div id="payment-pending" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p class="text-yellow-800 font-semibold">Menunggu Pembayaran...</p>
                        <p class="text-yellow-600 text-sm mt-1">Silakan selesaikan pembayaran Anda.</p>
                    </div>
                @endif

                <div class="pt-2">
                    <a href="{{ route('order.show', $order->table_id) }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-800">
                        &larr; Kembali ke Menu
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($order->status !== 'completed')
        <script>
            const payCashButton = document.getElementById('pay-cash-button');
            const paymentSection = document.getElementById('payment-section');
            const paymentSuccess = document.getElementById('payment-success');
            const paymentPending = document.getElementById('payment-pending');
            const paymentCashPending = document.getElementById('payment-cash-pending');
            const statusBadge = document.getElementById('status-badge');

            payCashButton.addEventListener('click', function () {
                payCashButton.disabled = true;
                payCashButton.textContent = 'Memproses...';

                fetch('{{ route('payment.choose-cash', $order->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(r => r.json())
                .then(data => {
                    paymentSection.classList.add('hidden');
                    paymentCashPending.classList.remove('hidden');
                    statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800';
                    statusBadge.textContent = 'Bayar di Kasir';
                    pollStatus();
                })
                .catch(() => {
                    payCashButton.disabled = false;
                    payCashButton.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Bayar Tunai di Kasir';
                    alert('Gagal. Silakan coba lagi.');
                });
            });

            function pollStatus() {
                const interval = setInterval(function() {
                    fetch('/api/payment/{{ $order->id }}/status')
                        .then(r => r.json())
                        .then(data => {
                            if (data.payment_status === 'success') {
                                clearInterval(interval);
                                if (paymentPending) paymentPending.classList.add('hidden');
                                paymentCashPending.classList.add('hidden');
                                paymentSuccess.classList.remove('hidden');
                                statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                                statusBadge.textContent = 'Lunas';
                                setTimeout(function() { window.location.href = '{{ route('order.track', $order->id) }}'; }, 2000);
                            }
                        });
                }, 5000);
            }
        </script>

        @if(!empty($snapToken))
        <script src="https://app.{{ config('midtrans.is_production') ? 'midtrans' : 'sandbox.midtrans' }}.com/snap/snap.js"
                data-client-key="{{ $clientKey }}"></script>
        <script>
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function () {
                payButton.disabled = true;
                payButton.textContent = 'Memproses...';

                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        paymentSection.classList.add('hidden');
                        paymentSuccess.classList.remove('hidden');
                        statusBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                        statusBadge.textContent = 'Lunas';
                        setTimeout(function() { window.location.href = '{{ route('order.track', $order->id) }}'; }, 2000);
                    },
                    onPending: function(result) {
                        paymentSection.classList.add('hidden');
                        paymentPending.classList.remove('hidden');
                        pollStatus();
                    },
                    onError: function(result) {
                        payButton.disabled = false;
                        payButton.textContent = 'Bayar Online (QRIS / E-Wallet)';
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.textContent = 'Bayar Online (QRIS / E-Wallet)';
                    }
                });
            });
        </script>
        @endif
    @endif
</body>
</html>
