<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pesanan - Meja {{ $order->table->number ?? $order->table_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 space-y-4">
                <p><strong>Nomor Meja:</strong> {{ $order->table->number ?? $order->table_number }}</p>
                <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Dibuat pada:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>

                <h3 class="text-lg font-bold mt-6">Item Pesanan:</h3>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="p-2 border">Produk</th>
                            <th class="p-2 border">Jumlah</th>
                            <th class="p-2 border">Harga Satuan</th>
                            <th class="p-2 border">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="p-2 border">{{ $item->product->name ?? '-' }}</td>
                            <td class="p-2 border">{{ $item->quantity }}</td>
                            <td class="p-2 border">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="p-2 border">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($order->status !== 'selesai')
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Tandai Selesai
                        </button>
                    </form>
                @endif

                <a href="{{ route('kasir.orders.index') }}" class="inline-block mt-4 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Kembali ke Daftar Pesanan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
