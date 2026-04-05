<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Transaksi</h2>
    </x-slot>

<div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-xl font-semibold text-gray-800 mb-6">Tambah Transaksi</h1>

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="table_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Meja</label>
                    <select class="block w-full rounded-md border-gray-300 shadow-sm" name="table_id" id="table_id" required>
                        <option value="">-- Pilih Meja --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" @selected(old('table_id') == $table->id)>Meja {{ $table->table_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Harga</label>
                    <input type="number" class="block w-full rounded-md border-gray-300 shadow-sm" name="total_price" id="total_price" min="0" value="{{ old('total_price') }}" required>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">Simpan</button>
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded shadow">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
