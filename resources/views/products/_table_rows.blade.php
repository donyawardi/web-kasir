@foreach ($products as $product)
    <tr class="hover:bg-gray-50/60 transition-colors">
        <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0 ring-1 ring-gray-200">
                @else
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center flex-shrink-0 ring-1 ring-gray-200">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</div>
                    @if(!empty($product->category))
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600">{{ $product->category }}</span>
                    @elseif(!empty($product->description))
                        <div class="text-xs text-gray-400 mt-0.5 truncate max-w-[220px]">{{ Str::limit($product->description, 60) }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-5 py-3.5">
            <span class="text-sm font-bold text-gray-800">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
        </td>
        <td class="px-5 py-3.5">
            <button type="button" onclick="toggleAvail({{ $product->id }}, this)"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all {{ $product->available ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                data-available="{{ $product->available ? '1' : '0' }}">
                @if($product->available)
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                    Tersedia
                @else
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    Habis
                @endif
            </button>
        </td>
        <td class="px-5 py-3.5 text-right">
            <div class="flex items-center justify-end gap-1">
                <a href="{{ route('products.show', $product) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Lihat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach

@if($products->isEmpty())
    <tr>
        <td colspan="4" class="px-5 py-12 text-center">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">Tidak ada produk ditemukan</p>
            </div>
        </td>
    </tr>
@endif
