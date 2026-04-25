<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Transaksi</h2>
            <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

        @php
            $methodInfo = match($transaction->payment_method) {
                'cash'  => ['label' => 'Tunai',       'color' => 'bg-emerald-100 text-emerald-700'],
                'qris'  => ['label' => 'QRIS',         'color' => 'bg-violet-100 text-violet-700'],
                'later' => ['label' => 'Bayar Kasir',  'color' => 'bg-amber-100 text-amber-700'],
                default => ['label' => ucfirst($transaction->payment_method ?? '-'), 'color' => 'bg-gray-100 text-gray-600'],
            };
        @endphp

        {{-- Receipt-style card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header stripe --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-5 text-white">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-indigo-200 text-xs font-medium uppercase tracking-wider">Transaksi</p>
                        <p class="text-2xl font-extrabold mt-0.5">#{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-indigo-200 text-xs mt-1">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($transaction->payment_status === 'paid')
                        <span class="flex items-center gap-1.5 bg-emerald-500/20 border border-emerald-400/40 text-emerald-100 text-xs font-bold px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            LUNAS
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 bg-amber-500/20 border border-amber-400/40 text-amber-100 text-xs font-bold px-3 py-1.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            PENDING
                        </span>
                    @endif
                </div>
            </div>

            {{-- Meta info --}}
            <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
                <div class="px-5 py-4 text-center">
                    <p class="text-xs text-gray-400">Meja</p>
                    <p class="text-base font-bold text-gray-800 mt-0.5">{{ $transaction->table->table_number ?? '-' }}</p>
                </div>
                <div class="px-5 py-4 text-center">
                    <p class="text-xs text-gray-400">Metode</p>
                    <p class="text-xs font-bold mt-1">
                        <span class="px-2 py-1 rounded-lg {{ $methodInfo['color'] }}">{{ $methodInfo['label'] }}</span>
                    </p>
                </div>
                <div class="px-5 py-4 text-center">
                    <p class="text-xs text-gray-400">Pesanan</p>
                    <p class="text-base font-bold text-gray-800 mt-0.5">
                        {{ $transaction->order?->orderItems->count() ?? 0 }} item
                    </p>
                </div>
            </div>

            {{-- Dashed separator --}}
            <div class="relative px-4 py-0">
                <div class="border-t border-dashed border-gray-200"></div>
                <div class="absolute -left-1.5 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-gray-100 border border-gray-200"></div>
                <div class="absolute -right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-gray-100 border border-gray-200"></div>
            </div>

            {{-- Order items --}}
            <div class="px-6 py-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider py-3">Rincian Pesanan</p>

                @if($transaction->order && $transaction->order->orderItems->isNotEmpty())
                    <div class="space-y-1 mb-4">
                        @foreach($transaction->order->orderItems as $item)
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-extrabold flex items-center justify-center flex-shrink-0">{{ $item->quantity }}x</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                                        <p class="text-xs text-gray-400">Rp{{ number_format($item->price, 0, ',', '.') }} / pcs</p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-gray-900 flex-shrink-0 ml-4">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 py-4 text-center">Rincian pesanan tidak tersedia.</p>
                @endif
            </div>

            {{-- Dashed separator --}}
            <div class="relative px-4 py-0">
                <div class="border-t border-dashed border-gray-200"></div>
                <div class="absolute -left-1.5 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-gray-100 border border-gray-200"></div>
                <div class="absolute -right-1.5 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-gray-100 border border-gray-200"></div>
            </div>

            {{-- Total --}}
            <div class="px-6 py-5 flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-500">Total Pembayaran</span>
                <span class="text-2xl font-extrabold text-indigo-600">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <a href="{{ route('transactions.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Transaksi
        </a>

    </div>
</x-app-layout>
