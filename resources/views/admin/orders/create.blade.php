<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 leading-tight">Buat Pesanan Baru</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih produk lalu konfirmasi pesanan</p>
                </div>
            </div>
        </div>
    </x-slot>

<div class="py-6 px-4 sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('admin.orders.store') }}" id="order-create-form">
        @csrf
        {{-- Hidden inputs for cart (populated by JS) --}}
        <div id="cart-hidden-inputs"></div>

        <div class="flex gap-6 items-start" style="max-width:1280px; margin:0 auto;">

            {{-- LEFT: Product Catalog --}}
            <div class="flex-1 min-w-0">

                {{-- Search & Filter Bar --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
                    <div class="flex gap-3 items-center">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19A8 8 0 1 1 11 3a8 8 0 0 1 0 16z"/></svg>
                            <input type="text" id="product-search" placeholder="Cari nama produk..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent bg-gray-50">
                        </div>
                    </div>
                    {{-- Category tabs --}}
                    <div class="flex gap-2 mt-3 flex-wrap" id="category-tabs">
                        <button type="button" data-cat="all" class="cat-tab active px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-indigo-600 text-white">
                            Semua
                        </button>
                        @php
                            $cats = $products->pluck('category')->filter()->unique()->sort()->values();
                        @endphp
                        @foreach($cats as $cat)
                        <button type="button" data-cat="{{ $cat }}" class="cat-tab px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">
                            {{ ucfirst($cat) }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Product Grid --}}
                <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($products as $product)
                    <div class="product-card group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all cursor-pointer overflow-hidden"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->name }}"
                         data-price="{{ $product->price }}"
                         data-cat="{{ $product->category ?? '' }}"
                         onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                        {{-- Product Image / Placeholder --}}
                        <div class="w-full aspect-square bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center overflow-hidden relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-10 h-10 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            @endif
                            {{-- Cart badge --}}
                            <div id="badge-{{ $product->id }}" class="product-badge hidden absolute top-2 right-2 w-6 h-6 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg">0</div>
                        </div>
                        <div class="p-3">
                            <p class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2 group-hover:text-indigo-700 transition">{{ $product->name }}</p>
                            @if($product->category)
                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-600">{{ ucfirst($product->category) }}</span>
                            @endif
                            <p class="text-indigo-600 font-bold text-sm mt-1.5">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach

                    {{-- Empty state --}}
                    <div id="no-products" class="hidden col-span-full py-12 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">Produk tidak ditemukan</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="w-80 flex-shrink-0 sticky top-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                    {{-- Header --}}
                    <div class="px-5 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-sm">Ringkasan Pesanan</h3>
                            <p id="cart-count-label" class="text-indigo-200 text-xs mt-0.5">0 item dipilih</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>

                    {{-- Table & Takeaway selection --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="space-y-3">
                            <div id="table-select-wrapper">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Meja</label>
                                <select name="table_id" required id="table-select"
                                    class="w-full rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 py-2.5 px-3">
                                    <option value="">— Pilih Meja —</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}"
                                            class="{{ $table->status === 'occupied' ? 'text-red-500' : '' }}"
                                            {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            Meja {{ $table->table_number }}
                                            @if($table->status === 'occupied') (Terisi) @elseif($table->status === 'available') (Kosong) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <label id="takeaway-toggle" class="flex items-center justify-between p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition select-none">
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
                    </div>

                    {{-- Cart Items --}}
                    <div id="cart-items-list" class="px-5 py-3 space-y-2 max-h-64 overflow-y-auto">
                        {{-- Empty cart --}}
                        <div id="cart-empty" class="py-8 text-center">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p class="text-xs text-gray-400">Belum ada produk dipilih</p>
                            <p class="text-xs text-gray-300 mt-1">Klik produk untuk menambahkan</p>
                        </div>
                    </div>

                    {{-- Order Total --}}
                    <div id="cart-footer" class="hidden px-5 py-4 border-t border-gray-100 bg-gray-50">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">Subtotal</span>
                            <span id="cart-subtotal" class="text-xs font-semibold text-gray-700">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 mt-2">
                            <span class="text-sm font-bold text-gray-800">Total</span>
                            <span id="cart-total" class="text-lg font-extrabold text-indigo-600">Rp 0</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-5 py-4 border-t border-gray-100 space-y-2">
                        <button type="submit" id="submit-btn" disabled
                            class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-200 disabled:cursor-not-allowed text-white disabled:text-gray-400 font-bold text-sm transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Buat Pesanan
                        </button>
                        <a href="{{ route('admin.orders.index') }}"
                            class="block w-full py-2.5 rounded-xl border border-gray-200 text-center text-gray-500 text-sm font-medium hover:bg-gray-50 transition">
                            Batal
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<style>
    .product-card.in-cart { border-color: #6366f1; background-color: #f5f3ff; }
    .product-card.in-cart .product-badge { display: flex !important; }
    .cat-tab.active { background-color: #4f46e5; color: #fff; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
(function () {
    // Cart state: { [productId]: { name, price, qty } }
    const cart = {};

    const formatRp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);

    // Keep persistent reference so the element survives innerHTML = '' clearing
    const emptyEl = document.getElementById('cart-empty');

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { name, price, qty: 1 };
        }
        renderCart();
    }

    function setQty(id, qty) {
        qty = parseInt(qty) || 0;
        if (qty <= 0) {
            delete cart[id];
        } else {
            cart[id].qty = qty;
        }
        renderCart();
    }

    function removeFromCart(id) {
        delete cart[id];
        renderCart();
    }

    function renderCart() {
        const ids = Object.keys(cart);
        const totalItems = ids.reduce((s, id) => s + cart[id].qty, 0);
        const totalPrice = ids.reduce((s, id) => s + cart[id].price * cart[id].qty, 0);

        // Update count label
        document.getElementById('cart-count-label').textContent = totalItems + ' item dipilih';

        // Update each product badge
        document.querySelectorAll('.product-card').forEach(card => {
            const pid = card.dataset.id;
            const badge = document.getElementById('badge-' + pid);
            if (cart[pid]) {
                badge.textContent = cart[pid].qty;
                badge.classList.remove('hidden');
                badge.style.display = 'flex';
                card.classList.add('in-cart');
            } else {
                badge.classList.add('hidden');
                badge.style.display = '';
                card.classList.remove('in-cart');
            }
        });

        // Update cart list
        const listEl = document.getElementById('cart-items-list');
        const footerEl = document.getElementById('cart-footer');

        if (ids.length === 0) {
            listEl.innerHTML = '';
            listEl.appendChild(emptyEl);
            emptyEl.style.display = '';
            footerEl.classList.add('hidden');
            document.getElementById('submit-btn').disabled = true;
        } else {
            emptyEl.style.display = 'none';
            listEl.innerHTML = '';
            ids.forEach(id => {
                const item = cart[id];
                const itemEl = document.createElement('div');
                itemEl.className = 'flex items-center gap-2 py-2 border-b border-gray-50 last:border-0';
                itemEl.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">${item.name}</p>
                        <p class="text-xs text-indigo-500 font-medium">${formatRp(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick="window._setQty(${id}, ${item.qty - 1})"
                            class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-red-100 hover:text-red-600 flex items-center justify-center text-gray-600 font-bold text-sm transition">−</button>
                        <span class="w-6 text-center text-xs font-bold text-gray-800">${item.qty}</span>
                        <button type="button" onclick="window._setQty(${id}, ${item.qty + 1})"
                            class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-indigo-100 hover:text-indigo-600 flex items-center justify-center text-gray-600 font-bold text-sm transition">+</button>
                    </div>
                    <p class="text-xs font-bold text-gray-700 w-16 text-right flex-shrink-0">${formatRp(item.price * item.qty)}</p>
                    <button type="button" onclick="window._removeFromCart(${id})"
                        class="w-5 h-5 rounded-full hover:bg-red-100 flex items-center justify-center text-gray-300 hover:text-red-500 transition flex-shrink-0 text-base leading-none">&times;</button>
                `;
                listEl.appendChild(itemEl);
            });
            footerEl.classList.remove('hidden');
            document.getElementById('cart-subtotal').textContent = formatRp(totalPrice);
            document.getElementById('cart-total').textContent = formatRp(totalPrice);
            document.getElementById('submit-btn').disabled = false;
        }

        // Rebuild hidden inputs
        const hiddenContainer = document.getElementById('cart-hidden-inputs');
        hiddenContainer.innerHTML = '';
        let i = 0;
        ids.forEach(id => {
            hiddenContainer.innerHTML += `
                <input type="hidden" name="items[${i}][product_id]" value="${id}">
                <input type="hidden" name="items[${i}][quantity]" value="${cart[id].qty}">
            `;
            i++;
        });
    }

    // Expose helpers to inline onclick
    window.addToCart = addToCart;
    window._setQty = setQty;
    window._removeFromCart = removeFromCart;

    // Take Away toggle: hide/show table select
    const takeawayCheckbox = document.getElementById('is_takeaway');
    const tableWrapper = document.getElementById('table-select-wrapper');
    const tableSelect = document.getElementById('table-select');

    function applyTakeaway() {
        if (takeawayCheckbox.checked) {
            tableWrapper.style.display = 'none';
            tableSelect.removeAttribute('required');
            tableSelect.value = '';
        } else {
            tableWrapper.style.display = '';
            tableSelect.setAttribute('required', 'required');
        }
    }

    takeawayCheckbox.addEventListener('change', applyTakeaway);
    // Apply on page load in case old() had is_takeaway checked
    applyTakeaway();

    // Category filter
    document.getElementById('category-tabs').addEventListener('click', function(e) {
        const btn = e.target.closest('.cat-tab');
        if (!btn) return;
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.classList.remove('active', 'bg-indigo-600', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        btn.classList.add('active', 'bg-indigo-600', 'text-white');
        btn.classList.remove('bg-gray-100', 'text-gray-600');
        filterProducts();
    });

    // Search
    document.getElementById('product-search').addEventListener('input', filterProducts);

    function filterProducts() {
        const query = document.getElementById('product-search').value.toLowerCase().trim();
        const activeCat = document.querySelector('.cat-tab.active')?.dataset.cat ?? 'all';
        let visible = 0;
        document.querySelectorAll('.product-card').forEach(card => {
            const matchCat = activeCat === 'all' || card.dataset.cat === activeCat;
            const matchQuery = !query || card.dataset.name.toLowerCase().includes(query);
            const show = matchCat && matchQuery;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('no-products').classList.toggle('hidden', visible > 0);
    }

    // Prevent form submit when cart is empty
    document.getElementById('order-create-form').addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 produk terlebih dahulu.');
        }
    });
})();
</script>
</x-app-layout>
