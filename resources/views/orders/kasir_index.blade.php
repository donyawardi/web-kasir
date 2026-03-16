<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pesanan Kasir
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('kasir.orders.create') }}" class="btn btn-primary mb-4">
            + Pesanan Baru
        </a>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="table-auto w-full text-left border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Meja</th>
                        <th class="px-4 py-2 border">Total</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">{{ $order->table_number }}</td>
                            <td class="px-4 py-2 border">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 border">{{ $order->status }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('kasir.orders.show', $order->id) }}" class="text-blue-500">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
