<x-guest-layout>
<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .product-card-active { box-shadow: 0 0 0 2px #6366f1; }
</style>

<div class="min-h-screen" style="background: #f5f5f7;">

    {{-- ===== HEADER ===== --}}
    <div class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-200/70 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between gap-3">
            {{-- Brand --}}
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

            {{-- Actions --}}
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

    {{-- ===== SEARCH + CATEGORIES (sticky below header) ===== --}}
    <div class="sticky top-14 z-20 bg-white/95 backdrop-blur-md border-b border-gray-200/70">
        <div class="max-w-3xl mx-auto px-4 pt-3 pb-2 space-y-2.5">
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="search-input" placeholder="Cari menu favorit kamu..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-100 border-0 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
            </div>
            {{-- Category Tabs --}}
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
    <form id="order-form">
        <input type="hidden" name="table_id" value="{{ $table->id }}">

        <div class="max-w-3xl mx-auto px-4 pt-4 pb-36">
            <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($products as $product)
                    <div class="product-item group relative rounded-2xl bg-white overflow-hidden border border-gray-100 shadow-sm transition-all duration-200 {{ $product->available ? 'hover:shadow-md hover:border-indigo-200 cursor-pointer' : 'opacity-60' }}"
                        data-name="{{ strtolower($product->name) }}"
                        data-id="{{ $product->id }}"
                        data-category="{{ $product->category }}">

                        {{-- Image --}}
                        <div class="relative aspect-[4/3] overflow-hidden bg-gray-50">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover {{ $product->available ? 'group-hover:scale-105' : 'grayscale' }} transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-50 via-gray-50 to-gray-100 flex flex-col items-center justify-center gap-1">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif

                            {{-- Category badge --}}
                            @if($product->category)
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold">{{ $product->category }}</span>
                            @endif

                            {{-- Out of stock overlay --}}
                            @unless($product->available)
                                <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                    <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1 rounded-full">Habis</span>
                                </div>
                            @endunless
                        </div>

                        {{-- Info --}}
                        <div class="p-3">
                            <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="text-[11px] text-gray-400 mt-0.5 line-clamp-1">{{ $product->description }}</p>
                            @endif
                            <p class="text-sm font-extrabold text-indigo-600 mt-1.5">Rp{{ number_format($product->price, 0, ',', '.') }}</p>

                            @if($product->available)
                                <div class="mt-2.5">
                                    {{-- Qty control (shown when qty > 0) --}}
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
                                    {{-- Add button (shown when qty = 0) --}}
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
    </form>

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
                    <button type="button" id="submit-btn" disabled
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:bg-white/10 disabled:text-white/30 text-white font-bold text-sm transition-all active:scale-[0.97] flex items-center gap-2 shadow-lg shadow-indigo-900/30 disabled:shadow-none">
                        <span id="btn-text">Pesan Sekarang</span>
                        <svg id="btn-arrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <svg id="btn-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== CART SHEET ===== --}}
    <div id="cart-overlay" class="fixed inset-0 z-50 hidden">
        <div id="cart-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div id="cart-sheet" class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl max-h-[85vh] flex flex-col transform translate-y-full transition-transform duration-300 ease-out">

            {{-- Sheet handle --}}
            <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            {{-- Sheet Header --}}
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

            {{-- Sheet List --}}
            <div id="cart-list" class="flex-1 overflow-y-auto px-5 py-4 space-y-2.5">
                <div id="cart-list-empty" class="text-center py-12 text-gray-400">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Keranjang masih kosong</p>
                    <p class="text-xs text-gray-400 mt-1">Pilih menu yang kamu suka</p>
                </div>
            </div>

            {{-- Sheet Footer --}}
            <div class="border-t border-gray-100 px-5 py-4 space-y-3 flex-shrink-0 bg-white">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-500">Total Pembayaran</span>
                    <span id="sheet-total" class="text-xl font-extrabold text-indigo-600">Rp0</span>
                </div>
                <button type="button" id="sheet-submit-btn" disabled
                    class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-100 disabled:text-gray-400 text-white font-bold text-sm transition-all active:scale-[0.98] shadow-sm shadow-indigo-200 disabled:shadow-none flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Konfirmasi Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
    (function() {
        // Product data
        var products = {
            @foreach($products as $product)
            {{ $product->id }}: { id: {{ $product->id }}, name: @json($product->name), price: {{ $product->price }} },
            @endforeach
        };

        // Cart state
        var cart = {};

        // DOM refs
        var cartBar        = document.getElementById('cart-bar');
        var barCount       = document.getElementById('bar-count');
        var barTotal       = document.getElementById('bar-total');
        var cartBadge      = document.getElementById('cart-badge');
        var submitBtn      = document.getElementById('submit-btn');
        var btnText        = document.getElementById('btn-text');
        var btnArrow       = document.getElementById('btn-arrow');
        var btnSpinner     = document.getElementById('btn-spinner');
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

            // Bottom bar
            barCount.textContent       = count + ' item';
            barTotal.textContent       = formatRupiah(total);
            submitBtn.disabled         = !hasItems;
            sheetSubmitBtn.disabled    = !hasItems;
            sheetTotal.textContent     = formatRupiah(total);

            if (hasItems) {
                cartBar.classList.remove('translate-y-full');
            } else {
                cartBar.classList.add('translate-y-full');
            }

            // Header badge
            if (count > 0) {
                cartBadge.textContent = count;
                cartBadge.classList.remove('hidden');
                cartBadge.classList.add('flex');
            } else {
                cartBadge.classList.add('hidden');
                cartBadge.classList.remove('flex');
            }

            // Product cards
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

            // Cart sheet list
            var rows     = cartList.querySelectorAll('.cart-sheet-row');
            var inCartIds = new Set();
            for (var p in cart) { if (cart[p] > 0) inCartIds.add(String(p)); }

            rows.forEach(function(r) { if (!inCartIds.has(r.dataset.pid)) r.remove(); });

            for (var pid in cart) {
                if (cart[pid] <= 0) continue;
                var prod = products[pid];
                var row  = cartList.querySelector('.cart-sheet-row[data-pid="' + pid + '"]');
                if (!row) {
                    row = document.createElement('div');
                    row.className  = 'cart-sheet-row flex items-center gap-3 bg-gray-50 rounded-2xl p-3.5';
                    row.dataset.pid = pid;
                    row.innerHTML =
                        '<div class="flex-1 min-w-0">' +
                            '<p class="text-sm font-semibold text-gray-900 truncate">' + prod.name + '</p>' +
                            '<p class="sheet-row-sub text-xs text-gray-400 mt-0.5"></p>' +
                        '</div>' +
                        '<div class="flex items-center gap-1.5">' +
                            '<button type="button" data-sheet-minus="' + pid + '" class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-gray-500 hover:text-red-500 transition active:scale-90">' +
                                '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M20 12H4"/></svg>' +
                            '</button>' +
                            '<span class="sheet-row-qty w-7 text-center text-sm font-extrabold text-indigo-700"></span>' +
                            '<button type="button" data-sheet-plus="' + pid + '" class="w-8 h-8 rounded-xl bg-indigo-600 shadow-sm flex items-center justify-center text-white hover:bg-indigo-700 transition active:scale-90">' +
                                '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>' +
                            '</button>' +
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

        // Product grid actions
        productGrid.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-action]');
            if (!btn) return;
            var pid    = btn.dataset.id;
            var action = btn.dataset.action;
            if (action === 'add' || action === 'plus') {
                setQty(pid, (cart[pid] || 0) + 1);
            } else if (action === 'minus') {
                setQty(pid, (cart[pid] || 0) - 1);
            }
        });

        // Cart sheet actions
        cartList.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-sheet-minus]');
            if (btn) { setQty(btn.dataset.sheetMinus, (cart[btn.dataset.sheetMinus] || 0) - 1); return; }
            btn = e.target.closest('[data-sheet-plus]');
            if (btn) { setQty(btn.dataset.sheetPlus, (cart[btn.dataset.sheetPlus] || 0) + 1); }
        });

        // Cart sheet open/close
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

        // Active category
        var activeCategory = 'all';

        document.getElementById('category-tabs').addEventListener('click', function(e) {
            var btn = e.target.closest('.cat-tab');
            if (!btn) return;
            activeCategory = btn.dataset.cat;
            document.querySelectorAll('.cat-tab').forEach(function(t) {
                if (t.dataset.cat === activeCategory) {
                    t.className = 'cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm shadow-indigo-200';
                } else {
                    t.className = 'cat-tab flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all bg-gray-100 text-gray-600 hover:bg-gray-200';
                }
            });
            filterProducts();
        });

        searchInput.addEventListener('input', filterProducts);

        function filterProducts() {
            var q = searchInput.value.toLowerCase().trim();
            var visible = 0;
            document.querySelectorAll('.product-item').forEach(function(card) {
                var matchName = !q || card.dataset.name.includes(q);
                var matchCat  = activeCategory === 'all' || card.dataset.category === activeCategory;
                var show = matchName && matchCat;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            noResults.classList.toggle('hidden', visible > 0);
        }

        // Submit order
        function submitOrder() {
            var items = [];
            for (var pid in cart) {
                if (cart[pid] > 0) items.push({ product_id: Number(pid), quantity: cart[pid] });
            }
            if (!items.length) return;

            submitBtn.disabled      = true;
            sheetSubmitBtn.disabled = true;
            btnText.textContent     = 'Memproses...';
            btnArrow.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            sheetSubmitBtn.textContent = 'Memproses...';

            fetch('{{ route("order.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ table_id: {{ $table->id }}, items: items })
            }).then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.order_id) {
                    window.location.href = '{{ url("/payment") }}/' + data.order_id;
                    return;
                }
                throw new Error(data.message || 'Gagal membuat pesanan');
            })
            .catch(function(err) {
                alert(err.message || 'Terjadi kesalahan saat memesan.');
            })
            .finally(function() {
                submitBtn.disabled      = false;
                sheetSubmitBtn.disabled = false;
                btnText.textContent     = 'Pesan Sekarang';
                btnArrow.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                sheetSubmitBtn.textContent = 'Konfirmasi Pesanan';
            });
        }

        submitBtn.addEventListener('click', submitOrder);
        sheetSubmitBtn.addEventListener('click', submitOrder);

        render();
    })();
    </script>
</x-guest-layout>
