<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Transaksi #{{ $transaction->id }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:underline">
                &larr; Kembali ke Daftar Transaksi
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ID Transaksi</p>
                        <p class="font-semibold text-gray-900">#{{ $transaction->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Meja</p>
                        <p class="font-semibold text-gray-900">Meja {{ $transaction->table->table_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Pembayaran</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Pending' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-900">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold text-indigo-600">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    @if(method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin'))
                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">
                            Edit Transaksi
                        </a>
                    @endif
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
