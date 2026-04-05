<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Menu</h2>
    </x-slot>

<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

    {{-- Search & Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form id="product-filter-form" method="GET" action="{{ route($routePrefix . '.products.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px] relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari menu..." autocomplete="off"
                    class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-200 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 cursor-pointer hover:bg-gray-100 transition">
                <input id="only-available-checkbox" type="checkbox" name="only_available" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ request('only_available') ? 'checked' : '' }}>
                <span class="text-sm font-medium text-gray-600">Tersedia saja</span>
            </label>
            <button type="submit" class="sr-only">Cari</button>
        </form>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Desktop Table --}}
    <div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="products-table-body" class="divide-y divide-gray-50">
                @include('products._table_rows')
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div id="mobile-products-list" class="sm:hidden space-y-3">
        @include('products._mobile_cards')
    </div>

    <div id="products-pagination">
        @include('products._pagination')
    </div>
</div>

<script>
    function toggleAvail(productId, btn) {
        var routePrefix = @json($routePrefix);
        var url = '/' + routePrefix + '/products/' + productId + '/toggle-availability';
        btn.disabled = true;
        btn.style.opacity = '0.5';
        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var isAvail = data.available;
            btn.dataset.available = isAvail ? '1' : '0';
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all ' +
                (isAvail ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200');
            btn.innerHTML = isAvail
                ? '<span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span> Tersedia'
                : '<span class="w-2 h-2 rounded-full bg-red-400"></span> Habis';
        })
        .catch(function() { alert('Gagal mengubah ketersediaan.'); })
        .finally(function() { btn.disabled = false; btn.style.opacity = '1'; });
    }

    (function () {
        var timeout = null;
        var form = document.getElementById('product-filter-form');
        var qInput = form ? form.querySelector('input[name="q"]') : null;
        var cb = document.getElementById('only-available-checkbox');

        function fetchResults() {
            var params = new URLSearchParams(new FormData(form));
            fetch(form.action + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var body = document.getElementById('products-table-body');
                var pagination = document.getElementById('products-pagination');
                if (body && data.rows) body.innerHTML = data.rows;
                if (pagination && data.pagination) pagination.innerHTML = data.pagination;
                var mobileContainer = document.getElementById('mobile-products-list');
                if (mobileContainer && data.mobile) mobileContainer.innerHTML = data.mobile;
            }).catch(function () {});
        }

        if (qInput) {
            qInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 300);
            });
        }
        if (cb) {
            cb.addEventListener('change', function () { fetchResults(); });
        }
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                fetchResults();
            });
        }
    })();
</script>

@push('modals')
<a href="{{ route($routePrefix . '.products.create') }}" aria-label="Tambah Menu"
   id="fab-btn"
   class="group fixed z-[9999] right-5 bottom-5 bg-indigo-600 hover:bg-indigo-700 text-white h-14 rounded-full shadow-2xl flex items-center justify-center overflow-hidden transition-all duration-300 w-14 hover:w-48 hover:px-5"
   style="position:fixed!important;">
    <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    <span class="ml-2 text-sm font-semibold whitespace-nowrap opacity-0 max-w-0 group-hover:opacity-100 group-hover:max-w-[120px] transition-all duration-300">Tambah Menu</span>
</a>
@endpush
</x-app-layout>
