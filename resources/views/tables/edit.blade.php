<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Meja</h2>
    </x-slot>

<div class="container mx-auto py-8">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Edit Meja</h2>
            <p class="text-sm text-gray-600">Perbarui nomor meja, status, atau lihat QR yang sudah tergenerate.</p>
        </div>

        <div class="px-6 py-6">
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 border border-green-100 text-green-700">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-700">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-700">
                    <ul class="list-disc pl-5 mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tables.update', $table->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja</label>
                    <input type="text" name="table_number" id="table_number" value="{{ old('table_number', $table->table_number) }}"
                        @class([
                            'block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
                            'border-red-500' => $errors->has('table_number'),
                            'border-gray-300' => !$errors->has('table_number')
                        ]) required>
                    @error('table_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>Terisi</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                @if($table->qr_code)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">QR Code Saat Ini</label>
                        <div class="p-3 bg-gray-50 rounded flex items-center gap-4">
                            <img src="{{ $table->qr_code }}" alt="QR Code" class="w-24 h-24 bg-white p-1 rounded shadow" />
                            <div class="text-sm text-gray-600">
                                <div>Anda dapat men-download atau regenerate di daftar meja.</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Simpan Perubahan</button>
                    <a href="{{ route('tables.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
