<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Transaksi</h2>
    </x-slot>

<div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-xl font-semibold text-gray-800 mb-6">Edit Transaksi</h1>

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="table_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Meja</label>
                    <select class="block w-full rounded-md border-gray-300 shadow-sm" name="table_id" id="table_id" required>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('table_id', $transaction->table_id) == $table->id ? 'selected' : '' }}>
                                Meja {{ $table->table_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Harga</label>
                    <input type="number" class="block w-full rounded-md border-gray-300 shadow-sm" name="total_price" id="total_price" min="0" value="{{ old('total_price', $transaction->total_price) }}" required>
                </div>

                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Status Pembayaran</label>
                    <select class="block w-full rounded-md border-gray-300 shadow-sm" name="payment_status" id="payment_status" required>
                        <option value="pending" {{ old('payment_status', $transaction->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('payment_status', $transaction->payment_status) == 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">Update</button>
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
