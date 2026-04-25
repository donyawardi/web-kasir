<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Transaksi</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Page header + filter card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Top bar --}}
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Riwayat Transaksi</h3>
                        <p class="text-xs text-gray-400">Semua transaksi pembayaran</p>
                    </div>
                </div>
                {{-- Summary stat --}}
                <div class="flex items-center gap-2 bg-indigo-50 rounded-xl px-4 py-2.5">
                    <div class="text-right">
                        <p class="text-xs text-indigo-400 font-medium uppercase tracking-wider">Total Pendapatan</p>
                        <p class="text-xl font-extrabold text-indigo-700 leading-tight">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                        @if(request()->filled('date_from') || request()->filled('date_to'))
                            <p class="text-xs text-indigo-300">
                                @if(request('date_from')){{ \Carbon\Carbon::parse(request('date_from'))->format('d M') }}@endif
                                @if(request('date_from') && request('date_to')) – @endif
                                @if(request('date_to')){{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}@endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="px-6 py-4 bg-gray-50/60 border-b border-gray-100">
                <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="rounded-lg border-gray-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400 bg-white">
                            <option value="">Semua</option>
                            <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dari tanggal</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sampai tanggal</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-200 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400">
                    </div>
                    <div class="flex gap-2 items-end">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Filter
                        </button>
                        @if(request()->filled('status') || request()->filled('date_from') || request()->filled('date_to'))
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 text-sm font-medium rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-12">#</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Meja</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-20"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $trx)
                            @php
                                $methodInfo = match($trx->payment_method) {
                                    'cash'  => ['label' => 'Tunai',         'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-emerald-600 bg-emerald-50'],
                                    'qris'  => ['label' => 'QRIS',          'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color' => 'text-violet-600 bg-violet-50'],
                                    'later' => ['label' => 'Bayar Kasir',   'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-amber-600 bg-amber-50'],
                                    default => ['label' => ucfirst($trx->payment_method ?? '-'), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-gray-600 bg-gray-100'],
                                };
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-6 py-4 text-gray-300 font-mono text-xs">{{ str_pad($transactions->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 6H6a2 2 0 00-2 2v8a2 2 0 002 2h4M14 6h4a2 2 0 012 2v8a2 2 0 01-2 2h-4"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-800">Meja {{ $trx->table->table_number ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $methodInfo['color'] }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $methodInfo['icon'] }}"/></svg>
                                        {{ $methodInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 font-medium">{{ $trx->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $trx->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-gray-900">Rp{{ number_format($trx->total_price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($trx->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('transactions.show', $trx->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition opacity-0 group-hover:opacity-100">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <p class="text-gray-400 font-medium">Belum ada transaksi</p>
                                        <p class="text-xs text-gray-300">Coba ubah filter untuk hasil berbeda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer pagination --}}
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
