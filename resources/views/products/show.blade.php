<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Menu</h2>
    </x-slot>

<div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Image Hero --}}
        @if($product->image)
            <div class="relative h-48 sm:h-56 bg-gradient-to-br from-gray-100 to-gray-50">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                <div class="absolute bottom-4 left-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $product->available ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                        @if($product->available)
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span></span>
                            Tersedia
                        @else
                            <span class="w-2 h-2 rounded-full bg-white/70"></span>
                            Habis
                        @endif
                    </span>
                </div>
            </div>
        @endif

        <div class="p-6">
            {{-- Title & Status (when no image) --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-1">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
                @if(!$product->image)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0 {{ $product->available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        @if($product->available)
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                            Tersedia
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            Habis
                        @endif
                    </span>
                @endif
            </div>

            @if(!empty($product->description))
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Deskripsi</p>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $product->description }}</p>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route($routePrefix . '.products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form action="{{ route($routePrefix . '.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
                <a href="{{ route($routePrefix . '.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
