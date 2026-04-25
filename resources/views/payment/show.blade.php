<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Pembayaran - Pesanan #{{ $order->id }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:underline">
                &larr; Kembali ke Daftar Pesanan
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-4">

                {{-- Order Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-900">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Meja</p>
                        <p class="font-semibold text-gray-900">Meja {{ $order->table->table_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Pesanan</p>
                        @php
                            $statusClass = match($order->status) {
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default      => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <hr>

                {{-- Order Items --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Rincian Pesanan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900">{{ $item->product->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right text-gray-700">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center text-gray-700">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-800">Total</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 text-base">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <hr>

                {{-- Payment Info --}}
                @if ($payment)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-900">{{ strtoupper($payment->payment_method ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Pembayaran</p>
                            @php
                                $payStatusClass = match($payment->status) {
                                    'success' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    default   => 'bg-yellow-100 text-yellow-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold {{ $payStatusClass }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        @if ($payment->transaction_id)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Transaction ID (Midtrans)</p>
                                <p class="font-mono text-sm text-gray-900">{{ $payment->transaction_id }}</p>
                            </div>
                        @endif
                    </div>

                    <hr>
                @endif

                {{-- Payment Action --}}
                @if ($order->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <p class="text-yellow-800 font-medium mb-3">Pesanan ini menunggu pembayaran.</p>
                        <form action="{{ route('payment.complete', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                onclick="return confirm('Konfirmasi pembayaran tunai telah diterima?')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded shadow">
                                ✓ Konfirmasi Pembayaran Tunai
                            </button>
                        </form>
                    </div>
                @elseif ($order->status === 'completed')
                    <div class="bg-green-50 border border-green-200 rounded p-4">
                        <p class="text-green-800 font-medium">✓ Pembayaran telah selesai.</p>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 rounded p-4">
                        <p class="text-red-800 font-medium">Pesanan ini dibatalkan.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
