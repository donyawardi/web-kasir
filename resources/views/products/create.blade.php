<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Menu</h2>
    </x-slot>

<div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Tambah Menu Baru</h2>
                    <p class="text-sm text-gray-400">Tambahkan menu baru ke daftar produk.</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold text-red-700">Terdapat kesalahan:</span>
                    </div>
                    <ul class="list-disc pl-5 text-sm text-red-600 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Menu</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh. Nasi Goreng Spesial"
                        @class([
                            'block w-full rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500',
                            'border-red-300 bg-red-50' => $errors->has('name'),
                            'border-gray-200' => !$errors->has('name')
                        ])
                        required>
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="font-normal text-gray-400">(opsional)</span></label>
                    <select name="category" id="category"
                        @class([
                            'block w-full rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white',
                            'border-red-300 bg-red-50' => $errors->has('category'),
                            'border-gray-200' => !$errors->has('category')
                        ])>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['Minuman', 'Menu Utama', 'Ayam Saus', 'Bebek Saus', 'Sayur', 'Cumi', 'Nasi', 'Udang'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="0"
                            @class([
                                'block w-full pl-10 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500',
                                'border-red-300 bg-red-50' => $errors->has('price'),
                                'border-gray-200' => !$errors->has('price')
                            ])
                            required>
                    </div>
                    @error('price')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi <span class="font-normal text-gray-400">(opsional)</span></label>
                    <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat menu..."
                        @class([
                            'block w-full rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500',
                            'border-red-300 bg-red-50' => $errors->has('description'),
                            'border-gray-200' => !$errors->has('description')
                        ])>{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gambar <span class="font-normal text-gray-400">(opsional)</span></label>
                    <label for="image" class="group flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition">
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="mt-1 text-xs text-gray-400 group-hover:text-indigo-500">Klik untuk pilih gambar</span>
                        <span class="text-[10px] text-gray-300">JPG, PNG, WEBP</span>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                    </label>
                    <div id="image-preview" class="mt-2 hidden"></div>
                    @error('image')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="available" value="0">
                        <input type="checkbox" name="available" value="1" class="sr-only peer" {{ old('available', 1) ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Tersedia</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    var preview = document.getElementById('image-preview');
    if (e.target.files && e.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML = '<img src="' + ev.target.result + '" class="w-20 h-20 object-cover rounded-xl ring-1 ring-gray-200">';
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
</x-app-layout>
