<x-app-layout>
    <x-slot name="header">
        @php
            $indexRoute = 'orders.index';
            $storeRoute = 'orders.store';
        @endphp
        <div class="flex items-center gap-3">
            <a href="{{ route($indexRoute) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">Pesanan Baru</h2>
                <p class="text-xs text-gray-500 mt-0.5">Pilih menu lalu konfirmasi pesanan</p>
            </div>
        </div>
    </x-slot>

    @php
        $isAdmin    = auth()->user()?->hasRole('admin');
        $indexRoute = 'orders.index';
        $storeRoute = 'orders.store';
    @endphp

    {{-- Alerts --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        </div>
    @elseif(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">{{ session('error') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route($storeRoute) }}" id="pos-form">
        @csrf

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- LEFT: Product Catalog --}}
                <div class="flex-1 min-w-0">
                    {{-- Search & Category Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19A8 8 0 1 1 11 3a8 8 0 0 1 0 16z"/></svg>
                            <input type="text" id="search-product" placeholder="Cari nama menu..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent bg-gray-50">
                        </div>
                        <div id="category-tabs" class="flex gap-2 mt-3 flex-wrap">
                            <button type="button" data-cat="all" class="cat-tab flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-indigo-600 text-white">Semua</button>
                            @foreach($products->pluck('category')->unique()->filter() as $cat)
                                <button type="button" data-cat="{{ $cat }}" class="cat-tab flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">{{ $cat }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Product Grid --}}
                    <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach($products as $product)
                            <button type="button"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->price }}"
                                data-product-category="{{ $product->category }}"
                                data-available="{{ $product->available ? '1' : '0' }}"
                                class="product-card group relative rounded-2xl border border-gray-100 bg-white shadow-sm {{ $product->available ? 'hover:shadow-md hover:border-indigo-200' : 'opacity-60 cursor-not-allowed' }} transition-all duration-150 text-left overflow-hidden focus:outline-none {{ $product->available ? 'focus:ring-2 focus:ring-indigo-400' : '' }}"
                            >
                                <div class="relative">
                                    @if($product->image)
                                        <div class="aspect-square w-full overflow-hidden bg-gray-100">
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover {{ $product->available ? 'group-hover:scale-105' : 'grayscale' }} transition-transform duration-200">
                                        </div>
                                    @else
                                        <div class="aspect-square w-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    @unless($product->available)
                                        <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-red-600 bg-red-50 px-3 py-1 rounded-full shadow-sm">Habis</span>
                                        </div>
                                    @endunless
                                </div>
                                <div class="p-2.5">
                                    <p class="text-sm font-medium {{ $product->available ? 'text-gray-900' : 'text-gray-400' }} truncate">{{ $product->name }}</p>
                                    <p class="text-sm font-bold {{ $product->available ? 'text-indigo-600' : 'text-gray-400' }} mt-0.5">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="product-badge hidden absolute top-2 right-2 w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold items-center justify-center shadow-lg">0</span>
                            </button>
                        @endforeach
                    </div>

                    <div id="no-results" class="hidden text-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">Menu tidak ditemukan</p>
                    </div>
                </div>

                {{-- RIGHT: Cart Sidebar (desktop only) --}}
                <div class="hidden lg:block w-80 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-5 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-bold text-sm">Ringkasan Pesanan</h3>
                                        <p id="cart-count" class="text-indigo-200 text-xs mt-0.5">0 item dipilih</p>
                                    </div>
                                </div>
                                <button type="button" id="clear-cart" class="text-indigo-200 hover:text-white text-xs font-medium transition hidden">Hapus Semua</button>
                            </div>
                        </div>

                        <div class="px-5 py-4 border-b border-gray-100 space-y-3">
                            <div id="table-select-wrapper">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Meja</label>
                                <select name="table_id" id="table_id" required
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2.5 px-3">
                                    <option value="">— Pilih Meja —</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            Meja {{ $table->table_number }}
                                            @if($table->status === 'occupied') (Terisi) @elseif($table->status === 'available') (Kosong) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition select-none">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">Take Away</p>
                                        <p class="text-xs text-gray-400">Dibawa pulang</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" name="is_takeaway" value="1" id="is_takeaway" {{ old('is_takeaway') ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-checked:bg-indigo-500 rounded-full transition-colors duration-200"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                                </div>
                            </label>
                        </div>

                        <div id="cart-items" class="px-5 py-3 max-h-[320px] overflow-y-auto">
                            <div id="cart-empty" class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <p class="text-sm">Ketuk menu untuk menambahkan</p>
                            </div>
                        </div>

                        <div id="cart-hidden-inputs"></div>

                        <div class="border-t border-gray-100 px-5 py-4 space-y-4 bg-gray-50/30">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-700">Total</span>
                                <span id="cart-total" class="text-xl font-extrabold text-indigo-700">Rp0</span>
                            </div>
                            <button type="submit" id="submit-btn" disabled class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-200 disabled:cursor-not-allowed text-white disabled:text-gray-400 font-bold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- MOBILE: Sticky Bottom Cart Bar --}}
        <div id="mobile-cart-bar" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 hidden">
            <div class="bg-white border-t border-gray-200">
                <button type="button" id="mobile-cart-toggle"
                    class="w-full px-4 py-3 flex items-center justify-between bg-indigo-600 text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        <span class="font-semibold text-sm" id="mobile-cart-count-label">0 item</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-base" id="mobile-cart-total-label">Rp0</span>
                        <svg id="mobile-chevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </div>
                </button>
                <div id="mobile-cart-detail" class="hidden bg-white">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <div id="table-select-wrapper-mobile">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Meja</label>
                            <select id="table_id_mobile"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white py-2 px-3 mb-2">
                                <option value="">— Pilih Meja —</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                        Meja {{ $table->table_number }}
                                        @if($table->status === 'occupied') (Terisi) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 cursor-pointer hover:bg-white transition select-none">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span class="text-xs font-semibold text-gray-700">Take Away</span>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="is_takeaway_mobile" class="sr-only peer">
                                <div class="w-9 h-4 bg-gray-200 peer-checked:bg-indigo-500 rounded-full transition-colors duration-200"></div>
                                <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                        <div id="mobile-cart-items">
                            <div id="mobile-cart-empty" class="text-center py-6 text-gray-400 text-sm">Ketuk menu untuk menambahkan</div>
                        </div>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-100">
                        <button type="submit" id="mobile-submit-btn" disabled
                            class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold text-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:hidden h-20"></div>
    </form>

<script>
(function() {
    const products = {
        @foreach($products as $product)
        {{ $product->id }}: { id: {{ $product->id }}, name: @json($product->name), price: {{ $product->price }}, available: {{ $product->available ? 'true' : 'false' }} },
        @endforeach
    };

    const cart = {};

    const cartItemsEl    = document.getElementById('cart-items');
    const cartEmptyEl    = document.getElementById('cart-empty');
    const cartHiddenEl   = document.getElementById('cart-hidden-inputs');
    const cartCountEl    = document.getElementById('cart-count');
    const cartTotalEl    = document.getElementById('cart-total');
    const clearCartBtn   = document.getElementById('clear-cart');
    const submitBtn      = document.getElementById('submit-btn');
    const searchInput    = document.getElementById('search-product');
    const productGrid    = document.getElementById('product-grid');
    const noResults      = document.getElementById('no-results');

    const mobileBar       = document.getElementById('mobile-cart-bar');
    const mobileCountLbl  = document.getElementById('mobile-cart-count-label');
    const mobileTotalLbl  = document.getElementById('mobile-cart-total-label');
    const mobileDetail    = document.getElementById('mobile-cart-detail');
    const mobileChevron   = document.getElementById('mobile-chevron');
    const mobileItemsEl   = document.getElementById('mobile-cart-items');
    const mobileEmptyEl   = document.getElementById('mobile-cart-empty');
    const mobileSubmitBtn = document.getElementById('mobile-submit-btn');
    const mobileTableSel  = document.getElementById('table_id_mobile');
    const desktopTableSel = document.getElementById('table_id');
    const desktopTakeaway = document.getElementById('is_takeaway');
    const mobileTakeaway  = document.getElementById('is_takeaway_mobile');
    const tableWrapper        = document.getElementById('table-select-wrapper');
    const tableWrapperMobile  = document.getElementById('table-select-wrapper-mobile');

    function applyTakeaway(checked) {
        if (checked) {
            tableWrapper.style.display = 'none';
            tableWrapperMobile.style.display = 'none';
            desktopTableSel.removeAttribute('required');
            desktopTableSel.value = '';
            mobileTableSel.value  = '';
        } else {
            tableWrapper.style.display = '';
            tableWrapperMobile.style.display = '';
            desktopTableSel.setAttribute('required', 'required');
        }
    }

    mobileTableSel.addEventListener('change', function() { desktopTableSel.value = this.value; });
    desktopTableSel.addEventListener('change', function() { mobileTableSel.value = this.value; });
    mobileTakeaway.addEventListener('change', function() { desktopTakeaway.checked = this.checked; applyTakeaway(this.checked); });
    desktopTakeaway.addEventListener('change', function() { mobileTakeaway.checked = this.checked; applyTakeaway(this.checked); });
    applyTakeaway(desktopTakeaway.checked);

    document.getElementById('mobile-cart-toggle').addEventListener('click', function() {
        const isHidden = mobileDetail.classList.contains('hidden');
        mobileDetail.classList.toggle('hidden', !isHidden);
        mobileChevron.style.transform = isHidden ? 'rotate(180deg)' : '';
    });

    function formatRupiah(val) { return 'Rp' + Number(val || 0).toLocaleString('id-ID'); }
    function getTotal()     { let t = 0; for (const p in cart) { if (cart[p] > 0 && products[p]) t += products[p].price * cart[p]; } return t; }
    function getItemCount() { let c = 0; for (const p in cart) c += cart[p]; return c; }

    function renderCart() {
        const total     = getTotal();
        const itemCount = getItemCount();
        const hasItems  = itemCount > 0;

        cartCountEl.textContent  = itemCount + ' item';
        cartTotalEl.textContent  = formatRupiah(total);
        submitBtn.disabled       = !hasItems;
        clearCartBtn.classList.toggle('hidden', !hasItems);

        if (!hasItems) {
            cartEmptyEl.style.display = '';
            cartItemsEl.querySelectorAll('.cart-row').forEach(el => el.remove());
        } else {
            cartEmptyEl.style.display = 'none';
            const inCart = new Set(Object.keys(cart).filter(p => cart[p] > 0));
            cartItemsEl.querySelectorAll('.cart-row').forEach(row => { if (!inCart.has(row.dataset.pid)) row.remove(); });
            for (const pid in cart) {
                if (cart[pid] <= 0) continue;
                const p = products[pid];
                let row = cartItemsEl.querySelector('.cart-row[data-pid="' + pid + '"]');
                if (!row) {
                    row = document.createElement('div');
                    row.className = 'cart-row flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0';
                    row.dataset.pid = pid;
                    row.innerHTML =
                        '<div class="flex-1 min-w-0 mr-3"><p class="text-sm font-medium text-gray-900 truncate">' + p.name + '</p>' +
                        '<p class="cart-row-subtotal text-xs text-gray-500"></p></div>' +
                        '<div class="flex items-center gap-1.5">' +
                        '<button type="button" data-action="minus" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20 12H4"/></svg></button>' +
                        '<span class="cart-row-qty w-8 text-center text-sm font-semibold text-gray-900"></span>' +
                        '<button type="button" data-action="plus" class="w-7 h-7 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-600 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></button>' +
                        '<button type="button" data-action="remove" class="w-7 h-7 rounded-lg hover:bg-red-100 flex items-center justify-center text-red-400 hover:text-red-600 transition ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                        '</div>';
                    cartItemsEl.appendChild(row);
                }
                row.querySelector('.cart-row-qty').textContent      = cart[pid];
                row.querySelector('.cart-row-subtotal').textContent = formatRupiah(p.price * cart[pid]) + ' (' + formatRupiah(p.price) + ' x ' + cart[pid] + ')';
            }
        }

        let idx = 0, html = '';
        for (const pid in cart) {
            if (cart[pid] > 0) {
                html += '<input type="hidden" name="items[' + idx + '][product_id]" value="' + pid + '">';
                html += '<input type="hidden" name="items[' + idx + '][quantity]" value="' + cart[pid] + '">';
                idx++;
            }
        }
        cartHiddenEl.innerHTML = html;

        document.querySelectorAll('.product-card').forEach(function(card) {
            const pid   = card.dataset.productId;
            const badge = card.querySelector('.product-badge');
            if (cart[pid] && cart[pid] > 0) {
                badge.textContent = cart[pid];
                badge.classList.remove('hidden'); badge.classList.add('flex');
                card.classList.add('ring-2', 'ring-indigo-400', 'border-indigo-200', 'bg-indigo-50/30');
            } else {
                badge.classList.add('hidden'); badge.classList.remove('flex');
                card.classList.remove('ring-2', 'ring-indigo-400', 'border-indigo-200', 'bg-indigo-50/30');
            }
        });

        renderMobileCart();
    }

    function renderMobileCart() {
        const total     = getTotal();
        const itemCount = getItemCount();
        const hasItems  = itemCount > 0;

        mobileBar.classList.toggle('hidden', !hasItems);
        mobileCountLbl.textContent = itemCount + ' item';
        mobileTotalLbl.textContent = formatRupiah(total);
        mobileSubmitBtn.disabled   = !hasItems;

        if (!hasItems) {
            mobileDetail.classList.add('hidden');
            mobileChevron.style.transform = '';
            mobileEmptyEl.style.display   = '';
            mobileItemsEl.querySelectorAll('.m-cart-row').forEach(el => el.remove());
            return;
        }

        mobileEmptyEl.style.display = 'none';
        const inCart = new Set(Object.keys(cart).filter(p => cart[p] > 0));
        mobileItemsEl.querySelectorAll('.m-cart-row').forEach(row => { if (!inCart.has(row.dataset.pid)) row.remove(); });

        for (const pid in cart) {
            if (cart[pid] <= 0) continue;
            const p = products[pid];
            let row = mobileItemsEl.querySelector('.m-cart-row[data-pid="' + pid + '"]');
            if (!row) {
                row = document.createElement('div');
                row.className = 'm-cart-row flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0';
                row.dataset.pid = pid;
                row.innerHTML =
                    '<div class="flex-1 min-w-0 mr-3"><p class="text-sm font-medium text-gray-900 truncate">' + p.name + '</p>' +
                    '<p class="m-row-subtotal text-xs text-gray-500"></p></div>' +
                    '<div class="flex items-center gap-1.5">' +
                    '<button type="button" data-action="minus" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20 12H4"/></svg></button>' +
                    '<span class="m-row-qty w-8 text-center text-sm font-semibold text-gray-900"></span>' +
                    '<button type="button" data-action="plus" class="w-7 h-7 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></button>' +
                    '<button type="button" data-action="remove" class="w-7 h-7 rounded-lg hover:bg-red-100 flex items-center justify-center text-red-400 hover:text-red-600 ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                    '</div>';
                mobileItemsEl.appendChild(row);
            }
            row.querySelector('.m-row-qty').textContent      = cart[pid];
            row.querySelector('.m-row-subtotal').textContent = formatRupiah(p.price * cart[pid]) + ' (' + formatRupiah(p.price) + ' x ' + cart[pid] + ')';
        }
    }

    function addToCart(pid) {
        if (!products[pid] || !products[pid].available) return;
        cart[pid] = (cart[pid] || 0) + 1;
        renderCart();
    }

    productGrid.addEventListener('click', function(e) {
        const card = e.target.closest('.product-card');
        if (card && card.dataset.available !== '0') addToCart(card.dataset.productId);
    });

    function handleCartAction(pid, action) {
        if (action === 'plus')        cart[pid] = (cart[pid] || 0) + 1;
        else if (action === 'minus') { cart[pid] = Math.max(0, (cart[pid]||0)-1); if (!cart[pid]) delete cart[pid]; }
        else if (action === 'remove')  delete cart[pid];
        renderCart();
    }

    cartItemsEl.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        handleCartAction(btn.closest('.cart-row').dataset.pid, btn.dataset.action);
    });

    mobileItemsEl.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        handleCartAction(btn.closest('.m-cart-row').dataset.pid, btn.dataset.action);
    });

    clearCartBtn.addEventListener('click', function() {
        Object.keys(cart).forEach(pid => delete cart[pid]);
        renderCart();
    });

    // Category filter
    document.getElementById('category-tabs').addEventListener('click', function(e) {
        const btn = e.target.closest('.cat-tab');
        if (!btn) return;
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        btn.classList.add('bg-indigo-600', 'text-white');
        btn.classList.remove('bg-gray-100', 'text-gray-600');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.product-card').forEach(card => {
            const match = cat === 'all' || card.dataset.productCategory === cat;
            card.style.display = match ? '' : 'none';
        });
    });

    // Search filter
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            const show = card.dataset.productName.toLowerCase().includes(q);
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResults.classList.toggle('hidden', visible > 0);
    });
})();
</script>
</x-app-layout>
