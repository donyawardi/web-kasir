<x-guest-layout>
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    @keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
    @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
</style>

<div class="min-h-screen" style="background: #f5f5f7;">

    {{-- ===== TOKO TUTUP OVERLAY ===== --}}
    @if(!$storeOpen)
    <div class="fixed inset-0 z-[9999] bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-6">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Toko Sedang Tutup</h2>
            <p class="text-gray-500 text-sm mb-4">{{ $closedNote }}</p>
            <div class="inline-flex items-center gap-2 bg-gray-100 rounded-full px-4 py-2 text-sm text-gray-600 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Jam Buka: {{ $openTime }} – {{ $closeTime }}
            </div>
        </div>
    </div>
    @endif

    {{-- ===== HEADER ===== --}}
    <div class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-200/70 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-gray-900 truncate leading-tight">Ayam Bakar Seafood 79</h1>
                    <div class="flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <span class="text-xs text-indigo-600 font-semibold">Meja {{ $table->table_number }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @php
                    $activeOrder = \App\Models\Order::where('table_id', $table->id)
                        ->whereNotIn('status', ['completed', 'cancelled'])
                        ->latest()->first();
                @endphp
                @if($activeOrder)
                    <a href="{{ route('order.track', $activeOrder->id) }}"
                        class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Lacak Pesanan
                    </a>
                @endif
                <button type="button" id="cart-toggle"
                    class="relative w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition active:scale-95">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    <span id="cart-badge" class="hidden absolute -top-1 -right-1 w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] font-bold items-center justify-center shadow">0</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH + CATEGORIES ===== --}}
    <div class="sticky top-14 z-20 bg-white/95 backdrop-blur-md border-b border-gray-200/70">
        <div class="max-w-3xl mx-auto px-4 pt-3 pb-2 space-y-2.5">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Cari menu favorit kamu..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-100 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
            </div>
            <div id="category-tabs" class="flex gap-1.5 overflow-x-auto scrollbar-hide pb-1">
                <button type="button" data-cat="all"
                    class="cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm shadow-indigo-200">Semua</button>
                @foreach($products->pluck('category')->unique()->filter() as $cat)
                    <button type="button" data-cat="{{ $cat }}"
                        class="cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">{{ $cat }}</button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== MENU GRID ===== --}}
    <div class="max-w-3xl mx-auto px-4 pt-4 pb-36">
        <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($products as $product)
                <div class="product-item group relative rounded-2xl bg-white overflow-hidden border border-gray-100 shadow-sm transition-all duration-200 {{ $product->available ? 'hover:shadow-md hover:border-indigo-200 cursor-pointer' : 'opacity-60' }}"
                    data-name="{{ strtolower($product->name) }}"
                    data-id="{{ $product->id }}"
                    data-category="{{ $product->category }}">
                    <div class="relative aspect-[4/3] overflow-hidden bg-gray-50">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover {{ $product->available ? 'group-hover:scale-105' : 'grayscale' }} transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-50 via-gray-50 to-gray-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        @if($product->category)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold">{{ $product->category }}</span>
                        @endif
                        @unless($product->available)
                            <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1 rounded-full">Habis</span>
                            </div>
                        @endunless
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">{{ $product->name }}</h3>
                        @if($product->description)
                            <p class="text-[11px] text-gray-400 mt-0.5 line-clamp-1">{{ $product->description }}</p>
                        @endif
                        <p class="text-sm font-extrabold text-indigo-600 mt-1.5">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                        @if($product->available)
                            <div class="mt-2.5">
                                <div class="qty-wrapper hidden" data-id="{{ $product->id }}">
                                    <div class="flex items-center justify-between bg-indigo-50 rounded-xl px-1 py-1">
                                        <button type="button" data-action="minus" data-id="{{ $product->id }}"
                                            class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-gray-500 hover:text-red-500 hover:shadow-md transition active:scale-90">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="qty-display text-sm font-extrabold text-indigo-700 min-w-[2rem] text-center" data-id="{{ $product->id }}">0</span>
                                        <button type="button" data-action="plus" data-id="{{ $product->id }}"
                                            class="w-8 h-8 rounded-lg bg-indigo-600 shadow-sm flex items-center justify-center text-white hover:bg-indigo-700 hover:shadow-md transition active:scale-90">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" data-action="add" data-id="{{ $product->id }}"
                                    class="add-btn w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition active:scale-[0.97] flex items-center justify-center gap-1 shadow-sm shadow-indigo-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                        @else
                            <div class="mt-2.5">
                                <div class="w-full py-2 rounded-xl bg-gray-100 text-gray-400 text-xs font-medium text-center">Tidak tersedia</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div id="no-results" class="hidden text-center py-20 text-gray-400">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">Menu tidak ditemukan</p>
            <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain</p>
        </div>
    </div>

    {{-- ===== FLOATING CART BAR ===== --}}
    <div id="cart-bar" class="fixed bottom-0 inset-x-0 z-40 transition-transform duration-300 translate-y-full">
        <div class="max-w-3xl mx-auto px-4 pb-5">
            <div class="bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-4 py-3.5">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        </div>
                        <div>
                            <p id="bar-count" class="text-xs text-gray-400 leading-none">0 item</p>
                            <p id="bar-total" class="text-base font-extrabold text-white mt-0.5 leading-none">Rp0</p>
                        </div>
                    </div>
                    <button type="button" id="submit-btn"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-all active:scale-[0.97] flex items-center gap-2 shadow-lg shadow-indigo-900/30">
                        <span>Pesan Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== CART SHEET ===== --}}
    <div id="cart-overlay" class="fixed inset-0 z-50 hidden">
        <div id="cart-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div id="cart-sheet" class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl max-h-[85vh] flex flex-col transform translate-y-full transition-transform duration-300 ease-out">
            <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Ringkasan Pesanan</h3>
                        <p class="text-xs text-gray-400">Meja {{ $table->table_number }}</p>
                    </div>
                </div>
                <button type="button" id="cart-close"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition active:scale-90">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="cart-list" class="flex-1 overflow-y-auto px-5 py-4 space-y-2.5">
                <div id="cart-list-empty" class="text-center py-12 text-gray-400">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Keranjang masih kosong</p>
                    <p class="text-xs text-gray-400 mt-1">Pilih menu yang kamu suka</p>
                </div>
            </div>
            <div class="border-t border-gray-100 px-5 py-4 space-y-3 flex-shrink-0 bg-white">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-500">Total Pembayaran</span>
                    <span id="sheet-total" class="text-xl font-extrabold text-indigo-600">Rp0</span>
                </div>
                <button type="button" id="sheet-submit-btn"
                    class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-all active:scale-[0.98] shadow-sm shadow-indigo-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Pesan Sekarang
                </button>
            </div>
        </div>
    </div>

    {{-- ===== KONFIRMASI SHEET (REDESIGNED) ===== --}}
    <div id="confirm-overlay" class="fixed inset-0 hidden" style="z-index: 60;">
        <div id="confirm-backdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div id="confirm-sheet" class="absolute bottom-0 inset-x-0 bg-white rounded-t-[28px] max-h-[94vh] flex flex-col transform translate-y-full transition-transform duration-300 ease-out overflow-hidden">

            {{-- Colored top accent --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-5 pt-5 pb-6 flex-shrink-0 relative">
                {{-- Handle --}}
                <div class="flex justify-center mb-4">
                    <div class="w-10 h-1 rounded-full bg-white/30"></div>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-indigo-200 text-xs font-semibold uppercase tracking-widest mb-1">Konfirmasi Pesanan</p>
                        <h3 class="text-white text-xl font-extrabold leading-tight" id="confirm-headline">0 Item</h3>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <div class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-indigo-200 text-xs font-medium">Meja {{ $table->table_number }}</span>
                        </div>
                    </div>
                    <button type="button" id="confirm-close"
                        class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center transition active:scale-90 flex-shrink-0 mt-1">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Total pill overlapping --}}
                <div class="absolute -bottom-5 left-5 right-5">
                    <div class="bg-white rounded-2xl shadow-lg shadow-indigo-200/60 px-5 py-3.5 flex items-center justify-between border border-indigo-50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-medium leading-none">Total Tagihan</p>
                                <p id="confirm-total-pill" class="text-base font-extrabold text-gray-900 leading-tight mt-0.5">Rp0</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-medium leading-none">Subtotal</p>
                            <p id="confirm-subtotal-pill" class="text-sm font-bold text-indigo-600 leading-tight mt-0.5">Rp0</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scrollable body --}}
            <div class="flex-1 overflow-y-auto pt-8 pb-2">

                {{-- Detail item pesanan --}}
                <div class="px-5 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rincian Pesanan</p>
                    </div>
                    <div id="confirm-items" class="space-y-2"></div>
                </div>

                {{-- Pilih metode bayar --}}
                <div class="px-5 mb-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1 h-4 rounded-full bg-indigo-500"></div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Metode Pembayaran</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">

                        {{-- Bayar di Kasir --}}
                        <button type="button"
                            class="pay-method-btn group relative flex flex-col items-start gap-3 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-amber-300 hover:bg-amber-50/60 transition-all active:scale-[0.97] text-left overflow-hidden"
                            data-method="cashier">
                            <div class="absolute top-0 right-0 w-16 h-16 rounded-bl-full bg-amber-50 group-hover:bg-amber-100 transition-colors -mt-4 -mr-4 opacity-0 group-[.selected]:opacity-100"></div>
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center border border-amber-100 group-[.selected]:border-amber-300 transition-colors">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Kasir</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-snug">Tunai atau QRIS<br>di meja kasir</p>
                            </div>
                            <div class="confirm-check hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-amber-500 items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>

                        {{-- Bayar QR --}}
                        <button type="button"
                            class="pay-method-btn group relative flex flex-col items-start gap-3 p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 hover:border-indigo-300 hover:bg-indigo-50/60 transition-all active:scale-[0.97] text-left overflow-hidden"
                            data-method="qr">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center border border-indigo-100 group-[.selected]:border-indigo-300 transition-colors">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">Bayar QR</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-snug">Scan QRIS<br>langsung sekarang</p>
                            </div>
                            <div class="confirm-check hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-indigo-600 items-center justify-center shadow-sm">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>

                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 flex-shrink-0 bg-white border-t border-gray-100">
                {{-- Hint text --}}
                <p id="confirm-hint" class="text-center text-xs text-gray-400 mb-3">Pilih metode pembayaran untuk melanjutkan</p>
                <button type="button" id="confirm-submit-btn" disabled
                    class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-100 disabled:text-gray-400 text-white font-bold text-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2.5 disabled:shadow-none shadow-lg shadow-indigo-300/40">
                    <svg id="confirm-btn-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <svg id="confirm-btn-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span id="confirm-btn-text">Pilih metode pembayaran</span>
                </button>
            </div>

        </div>
    </div>

</div>

<script>
(function() {
    var products = {
        @foreach($products as $product)
        {{ $product->id }}: { id: {{ $product->id }}, name: @json($product->name), price: {{ $product->price }} },
        @endforeach
    };

    var cart = {};
    var selectedMethod = null;

    var cartBar        = document.getElementById('cart-bar');
    var barCount       = document.getElementById('bar-count');
    var barTotal       = document.getElementById('bar-total');
    var cartBadge      = document.getElementById('cart-badge');
    var submitBtn      = document.getElementById('submit-btn');
    var sheetTotal     = document.getElementById('sheet-total');
    var sheetSubmitBtn = document.getElementById('sheet-submit-btn');
    var cartList       = document.getElementById('cart-list');
    var cartListEmpty  = document.getElementById('cart-list-empty');
    var cartOverlay    = document.getElementById('cart-overlay');
    var cartSheet      = document.getElementById('cart-sheet');
    var cartBackdrop   = document.getElementById('cart-backdrop');
    var searchInput    = document.getElementById('search-input');
    var productGrid    = document.getElementById('product-grid');
    var noResults      = document.getElementById('no-results');

    var confirmOverlay    = document.getElementById('confirm-overlay');
    var confirmSheet      = document.getElementById('confirm-sheet');
    var confirmBackdrop   = document.getElementById('confirm-backdrop');
    var confirmItems      = document.getElementById('confirm-items');
    var confirmHeadline   = document.getElementById('confirm-headline');
    var confirmTotalPill  = document.getElementById('confirm-total-pill');
    var confirmSubPill    = document.getElementById('confirm-subtotal-pill');
    var confirmHint       = document.getElementById('confirm-hint');
    var confirmSubmitBtn  = document.getElementById('confirm-submit-btn');
    var confirmBtnText    = document.getElementById('confirm-btn-text');
    var confirmBtnIcon    = document.getElementById('confirm-btn-icon');
    var confirmBtnSpinner = document.getElementById('confirm-btn-spinner');

    function formatRupiah(v) { return 'Rp' + Number(v || 0).toLocaleString('id-ID'); }

    function getTotal() {
        var t = 0;
        for (var p in cart) { if (cart[p] > 0 && products[p]) t += products[p].price * cart[p]; }
        return t;
    }

    function getItemCount() {
        var c = 0;
        for (var p in cart) c += cart[p];
        return c;
    }

    function render() {
        var total    = getTotal();
        var count    = getItemCount();
        var hasItems = count > 0;

        barCount.textContent   = count + ' item';
        barTotal.textContent   = formatRupiah(total);
        sheetTotal.textContent = formatRupiah(total);

        hasItems ? cartBar.classList.remove('translate-y-full') : cartBar.classList.add('translate-y-full');

        if (count > 0) {
            cartBadge.textContent = count;
            cartBadge.classList.remove('hidden');
            cartBadge.classList.add('flex');
        } else {
            cartBadge.classList.add('hidden');
            cartBadge.classList.remove('flex');
        }

        document.querySelectorAll('.product-item[data-id]').forEach(function(card) {
            var pid        = card.dataset.id;
            var qty        = cart[pid] || 0;
            var addBtn     = card.querySelector('.add-btn');
            var qtyWrapper = card.querySelector('.qty-wrapper');
            var qtyDisplay = card.querySelector('.qty-display');
            if (!addBtn || !qtyWrapper) return;
            if (qty > 0) {
                addBtn.classList.add('hidden');
                qtyWrapper.classList.remove('hidden');
                if (qtyDisplay) qtyDisplay.textContent = qty;
                card.classList.add('ring-2', 'ring-indigo-400', 'border-indigo-200');
                card.classList.remove('border-gray-100');
            } else {
                addBtn.classList.remove('hidden');
                qtyWrapper.classList.add('hidden');
                card.classList.remove('ring-2', 'ring-indigo-400', 'border-indigo-200');
                card.classList.add('border-gray-100');
            }
        });

        var inCartIds = new Set();
        for (var p in cart) { if (cart[p] > 0) inCartIds.add(String(p)); }
        cartList.querySelectorAll('.cart-sheet-row').forEach(function(r) { if (!inCartIds.has(r.dataset.pid)) r.remove(); });

        for (var pid in cart) {
            if (cart[pid] <= 0) continue;
            var prod = products[pid];
            var row  = cartList.querySelector('.cart-sheet-row[data-pid="' + pid + '"]');
            if (!row) {
                row = document.createElement('div');
                row.className   = 'cart-sheet-row flex items-center gap-3 bg-gray-50 rounded-2xl p-3.5';
                row.dataset.pid = pid;
                row.innerHTML =
                    '<div class="flex-1 min-w-0">' +
                        '<p class="text-sm font-semibold text-gray-900 truncate">' + prod.name + '</p>' +
                        '<p class="sheet-row-sub text-xs text-gray-400 mt-0.5"></p>' +
                    '</div>' +
                    '<div class="flex items-center gap-1.5">' +
                        '<button type="button" data-sheet-minus="' + pid + '" class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-500 hover:text-red-500 transition active:scale-90"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M20 12H4"/></svg></button>' +
                        '<span class="sheet-row-qty w-7 text-center text-sm font-extrabold text-indigo-700"></span>' +
                        '<button type="button" data-sheet-plus="' + pid + '" class="w-8 h-8 rounded-xl bg-indigo-600 shadow-sm flex items-center justify-center text-white hover:bg-indigo-700 transition active:scale-90"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg></button>' +
                    '</div>';
                cartList.appendChild(row);
            }
            row.querySelector('.sheet-row-qty').textContent = cart[pid];
            row.querySelector('.sheet-row-sub').textContent = formatRupiah(prod.price) + ' × ' + cart[pid] + ' = ' + formatRupiah(prod.price * cart[pid]);
        }

        cartListEmpty.style.display = hasItems ? 'none' : '';
    }

    function setQty(pid, qty) {
        if (qty <= 0) { delete cart[pid]; } else { cart[pid] = qty; }
        render();
    }

    productGrid.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        var pid = btn.dataset.id, action = btn.dataset.action;
        if (action === 'add' || action === 'plus') setQty(pid, (cart[pid] || 0) + 1);
        else if (action === 'minus') setQty(pid, (cart[pid] || 0) - 1);
    });

    cartList.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-sheet-minus]');
        if (btn) { setQty(btn.dataset.sheetMinus, (cart[btn.dataset.sheetMinus] || 0) - 1); return; }
        btn = e.target.closest('[data-sheet-plus]');
        if (btn) { setQty(btn.dataset.sheetPlus, (cart[btn.dataset.sheetPlus] || 0) + 1); }
    });

    function openSheet() {
        cartOverlay.classList.remove('hidden');
        requestAnimationFrame(function() { cartSheet.classList.remove('translate-y-full'); });
    }
    function closeSheet() {
        cartSheet.classList.add('translate-y-full');
        setTimeout(function() { cartOverlay.classList.add('hidden'); }, 300);
    }
    document.getElementById('cart-toggle').addEventListener('click', openSheet);
    document.getElementById('cart-close').addEventListener('click', closeSheet);
    cartBackdrop.addEventListener('click', closeSheet);

    function openConfirm() {
        confirmItems.innerHTML = '';
        var total = getTotal();
        var count = getItemCount();

        confirmHeadline.textContent     = count + ' Item Dipesan';
        confirmTotalPill.textContent    = formatRupiah(total);
        confirmSubPill.textContent      = formatRupiah(total);

        for (var pid in cart) {
            if (cart[pid] <= 0) continue;
            var p   = products[pid];
            var row = document.createElement('div');
            row.className = 'flex items-center gap-3 bg-gray-50/80 rounded-xl px-4 py-3 border border-gray-100';
            row.innerHTML =
                '<div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center flex-shrink-0">' +
                    '<span class="text-[11px] font-extrabold text-white">' + cart[pid] + 'x</span>' +
                '</div>' +
                '<p class="text-sm font-semibold text-gray-800 flex-1 truncate">' + p.name + '</p>' +
                '<p class="text-sm font-bold text-gray-700 flex-shrink-0">' + formatRupiah(p.price * cart[pid]) + '</p>';
            confirmItems.appendChild(row);
        }

        // Tambah baris total di bawah items
        var totalRow = document.createElement('div');
        totalRow.className = 'flex items-center justify-between px-4 py-2.5 mt-1';
        totalRow.innerHTML =
            '<span class="text-xs font-semibold text-gray-400">' + count + ' item · ' + formatRupiah(total) + '</span>' +
            '<span class="text-xs font-bold text-indigo-500">Lihat rincian ↑</span>';
        confirmItems.appendChild(totalRow);

        selectedMethod = null;
        document.querySelectorAll('.pay-method-btn').forEach(function(b) {
            b.classList.remove('border-indigo-400', 'border-amber-400', 'bg-indigo-50', 'bg-amber-50', 'selected');
            b.querySelector('.confirm-check').classList.remove('flex');
            b.querySelector('.confirm-check').classList.add('hidden');
        });
        confirmSubmitBtn.disabled  = true;
        confirmBtnText.textContent = 'Pilih metode pembayaran';
        confirmBtnIcon.classList.add('hidden');
        confirmHint.textContent    = 'Pilih metode pembayaran untuk melanjutkan';
        confirmHint.classList.remove('hidden');

        confirmOverlay.classList.remove('hidden');
        requestAnimationFrame(function() { confirmSheet.classList.remove('translate-y-full'); });
    }

    function closeConfirm() {
        confirmSheet.classList.add('translate-y-full');
        setTimeout(function() { confirmOverlay.classList.add('hidden'); }, 300);
    }

    document.getElementById('confirm-close').addEventListener('click', closeConfirm);
    confirmBackdrop.addEventListener('click', closeConfirm);

    submitBtn.addEventListener('click', function() {
        if (getItemCount() > 0) openConfirm();
    });

    sheetSubmitBtn.addEventListener('click', function() {
        if (getItemCount() > 0) { closeSheet(); setTimeout(openConfirm, 310); }
    });

    document.querySelectorAll('.pay-method-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            selectedMethod = this.dataset.method;
            document.querySelectorAll('.pay-method-btn').forEach(function(b) {
                b.classList.remove('border-indigo-400', 'border-amber-400', 'bg-indigo-50/80', 'bg-amber-50/80', 'selected');
                b.querySelector('.confirm-check').classList.remove('flex');
                b.querySelector('.confirm-check').classList.add('hidden');
            });

            if (selectedMethod === 'cashier') {
                this.classList.add('border-amber-400', 'bg-amber-50/80', 'selected');
                confirmBtnText.textContent = 'Pesan & Bayar di Kasir';
            } else {
                this.classList.add('border-indigo-400', 'bg-indigo-50/80', 'selected');
                confirmBtnText.textContent = 'Pesan & Bayar via QR';
            }

            this.querySelector('.confirm-check').classList.remove('hidden');
            this.querySelector('.confirm-check').classList.add('flex');
            confirmSubmitBtn.disabled = false;
            confirmBtnIcon.classList.remove('hidden');
            confirmHint.classList.add('hidden');
        });
    });

    confirmSubmitBtn.addEventListener('click', function() {
        if (!selectedMethod || getItemCount() === 0) return;
        var items = [];
        for (var pid in cart) {
            if (cart[pid] > 0) items.push({ product_id: Number(pid), quantity: cart[pid] });
        }

        confirmSubmitBtn.disabled  = true;
        confirmBtnText.textContent = 'Memproses...';
        confirmBtnIcon.classList.add('hidden');
        confirmBtnSpinner.classList.remove('hidden');

        fetch('{{ route("order.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ table_id: {{ $table->id }}, items: items, payment_method: selectedMethod })
        })
        .then(function(r) {
            return r.json().then(function(data) {
                if (!r.ok) {
                    if (data.store_closed) {
                        // Reload so the overlay becomes visible
                        window.location.reload();
                        return;
                    }
                    throw new Error(data.message || 'Gagal membuat pesanan');
                }
                return data;
            });
        })
        .then(function(data) {
            if (!data) return;
            if (data.order_id) {
                window.location.href = selectedMethod === 'qr'
                    ? '{{ url("/payment") }}/' + data.order_id
                    : data.tracking_url;
                return;
            }
            throw new Error(data.message || 'Gagal membuat pesanan');
        })
        .catch(function(err) { alert(err.message || 'Terjadi kesalahan saat memesan.'); })
        .finally(function() {
            confirmSubmitBtn.disabled = false;
            confirmBtnSpinner.classList.add('hidden');
            confirmBtnIcon.classList.remove('hidden');
            confirmBtnText.textContent = selectedMethod === 'cashier' ? 'Pesan & Bayar di Kasir' : 'Pesan & Bayar via QR';
        });
    });

    var activeCategory = 'all';
    document.getElementById('category-tabs').addEventListener('click', function(e) {
        var btn = e.target.closest('.cat-tab');
        if (!btn) return;
        activeCategory = btn.dataset.cat;
        document.querySelectorAll('.cat-tab').forEach(function(t) {
            t.className = t.dataset.cat === activeCategory
                ? 'cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                : 'cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
        });
        filterProducts();
    });

    searchInput.addEventListener('input', filterProducts);

    function filterProducts() {
        var q = searchInput.value.toLowerCase().trim();
        var visible = 0;
        document.querySelectorAll('.product-item').forEach(function(card) {
            var show = (!q || card.dataset.name.includes(q)) && (activeCategory === 'all' || card.dataset.category === activeCategory);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResults.classList.toggle('hidden', visible > 0);
    }

    render();
})();
</script>
</x-guest-layout>