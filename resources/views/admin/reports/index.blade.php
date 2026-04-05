<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Filter --}}
        <div class="bg-white rounded-lg shadow p-5">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Periode</label>
                    <select name="range" class="rounded-md border-gray-300 text-sm shadow-sm" onchange="toggleCustomDate(this)">
                        <option value="today" @selected($range === 'today')>Hari Ini</option>
                        <option value="yesterday" @selected($range === 'yesterday')>Kemarin</option>
                        <option value="this_week" @selected($range === 'this_week')>Minggu Ini</option>
                        <option value="this_month" @selected($range === 'this_month')>Bulan Ini</option>
                        <option value="custom" @selected($range === 'custom')>Custom</option>
                    </select>
                </div>
                <div id="custom-dates" class="{{ $range === 'custom' ? '' : 'hidden' }} flex items-end gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-md border-gray-300 text-sm shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-md border-gray-300 text-sm shadow-sm">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow">Lihat Laporan</button>
            </form>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.reports.export', array_filter(['range' => $range, 'date_from' => $dateFrom, 'date_to' => $dateTo])) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
            </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan</p>
                <p class="mt-1 text-2xl font-bold text-indigo-600">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pesanan</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ $totalOrders }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata / Pesanan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">Rp{{ number_format($avgOrder, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Ramai</p>
                <p class="mt-1 text-2xl font-bold text-orange-600">{{ str_pad($peakHour, 2, '0', STR_PAD_LEFT) }}:00</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $peakOrders }} pesanan</p>
            </div>
        </div>

        {{-- Charts Row 1: Revenue + Hourly --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daily Revenue Chart --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Pendapatan Harian</h3>
                <div class="relative" style="height:280px">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Hourly Orders Chart --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Pesanan per Jam</h3>
                <div class="relative" style="height:280px">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Charts Row 2: Top Products + Category --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Products Bar Chart --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Produk Terlaris</h3>
                <div class="relative" style="height:300px">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            {{-- Category Pie Chart --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Penjualan per Kategori</h3>
                <div class="relative" style="height:300px">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tables: Top Products + Daily Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Products Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Produk Terlaris</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Terjual</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($topProducts as $i => $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $p['name'] }}</td>
                                    <td class="px-5 py-3 text-right">{{ $p['qty'] }}</td>
                                    <td class="px-5 py-3 text-right">Rp{{ number_format($p['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-4 text-center text-gray-400">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daily Breakdown Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Rincian Harian</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($dailyBreakdown as $day)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-900">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y') }}</td>
                                    <td class="px-5 py-3 text-right">{{ $day['orders'] }}</td>
                                    <td class="px-5 py-3 text-right font-medium">Rp{{ number_format($day['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-4 text-center text-gray-400">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Daftar Pesanan Selesai ({{ $totalOrders }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meja</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-500">#{{ $order->id }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">Meja {{ $order->table->table_number ?? '-' }}</td>
                                <td class="px-5 py-3 text-right">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400">Tidak ada pesanan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        function toggleCustomDate(el) {
            document.getElementById('custom-dates').classList.toggle('hidden', el.value !== 'custom');
        }

        var chartColors = ['#6366f1','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];

        // Daily Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($dailyBreakdown->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($dailyBreakdown->pluck('revenue')),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#6366f1',
                }, {
                    label: 'Pesanan',
                    data: @json($dailyBreakdown->pluck('orders')),
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.3,
                    pointRadius: 3,
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp' + (v/1000) + 'k' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
                }
            }
        });

        // Hourly Orders Chart
        @php
            $hourlyLabels = collect(range(0,23))->map(fn($h) => str_pad($h,2,'0',STR_PAD_LEFT).':00')->values();
            $hourlyData = collect($hourlyBreakdown)->pluck('orders')->values();
            $hourlyColors = collect(range(0,23))->map(fn($h) => $h == $peakHour ? '#f59e0b' : '#c7d2fe')->values();
        @endphp
        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: @json($hourlyLabels),
                datasets: [{
                    label: 'Pesanan',
                    data: @json($hourlyData),
                    backgroundColor: @json($hourlyColors),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // Top Products Bar Chart
        new Chart(document.getElementById('topProductsChart'), {
            type: 'bar',
            data: {
                labels: @json(collect($topProducts)->pluck('name')),
                datasets: [{
                    label: 'Terjual',
                    data: @json(collect($topProducts)->pluck('qty')),
                    backgroundColor: chartColors,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // Category Doughnut Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($categorySales)),
                datasets: [{
                    data: @json(array_values(array_map(fn($c) => $c['revenue'], $categorySales))),
                    backgroundColor: chartColors.slice(0, {{ count($categorySales) }}),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': Rp' + Number(ctx.raw).toLocaleString('id-ID') } }
                }
            }
        });
    </script>
</x-app-layout>
