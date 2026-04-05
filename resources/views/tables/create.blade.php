<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Meja</h2>
    </x-slot>

<div class="container mx-auto py-8">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Tambah Meja</h2>
            <p class="text-sm text-gray-600">Buat meja baru dan generate QR untuk penggunaan kasir.</p>
        </div>

        <div class="px-6 py-6">
            {{-- Notifikasi Sukses / Error --}}
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 border border-green-100 text-green-700">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validasi Error --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-100 text-red-700">
                    <ul class="list-disc pl-5 mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tables.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja</label>
                    <input type="text" name="table_number" id="table_number" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('table_number', isset($nextTableNumber) ? (string) $nextTableNumber : '') }}" required>
                    @error('table_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    @if(isset($nextTableNumber))
                        <p class="mt-2 text-xs text-gray-500">Nomor meja berikutnya: <span class="inline-block bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-700">{{ $nextTableNumber }}</span></p>
                    @endif
                </div>

                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Terisi</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Simpan</button>
                    <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
