<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'this_month');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($range === 'today') {
            $dateFrom = Carbon::today()->toDateString();
            $dateTo = Carbon::today()->toDateString();
        } elseif ($range === 'yesterday') {
            $dateFrom = Carbon::yesterday()->toDateString();
            $dateTo = Carbon::yesterday()->toDateString();
        } elseif ($range === 'this_week') {
            $dateFrom = Carbon::now()->startOfWeek()->toDateString();
            $dateTo = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($range === 'this_month') {
            $dateFrom = Carbon::now()->startOfMonth()->toDateString();
            $dateTo = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($range === 'custom') {
            // use provided dates
        }

        $orders = Order::with(['table', 'orderItems.product'])
            ->where('status', 'completed')
            ->when(filled($dateFrom), fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when(filled($dateTo), fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();

        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();
        $avgOrder = $totalOrders > 0 ? round($totalRevenue / $totalOrders) : 0;

        // Top selling products
        $productSales = [];
        $categorySales = [];
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $pid = $item->product_id;
                if (!isset($productSales[$pid])) {
                    $productSales[$pid] = [
                        'name' => $item->product->name ?? '-',
                        'qty' => 0,
                        'revenue' => 0,
                    ];
                }
                $productSales[$pid]['qty'] += $item->quantity;
                $productSales[$pid]['revenue'] += $item->quantity * $item->price;

                // Category aggregation
                $cat = $item->product->category ?? 'Lainnya';
                if (!isset($categorySales[$cat])) {
                    $categorySales[$cat] = ['qty' => 0, 'revenue' => 0];
                }
                $categorySales[$cat]['qty'] += $item->quantity;
                $categorySales[$cat]['revenue'] += $item->quantity * $item->price;
            }
        }
        usort($productSales, fn($a, $b) => $b['qty'] - $a['qty']);
        $topProducts = array_slice($productSales, 0, 10);

        // Daily breakdown
        $dailyBreakdown = $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'))
            ->map(fn($dayOrders, $date) => [
                'date' => $date,
                'orders' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_price'),
            ])
            ->sortKeys()
            ->values();

        // Hourly breakdown (jam ramai)
        $hourlyBreakdown = array_fill(0, 24, ['orders' => 0, 'revenue' => 0]);
        foreach ($orders as $order) {
            $hour = (int) $order->created_at->format('G');
            $hourlyBreakdown[$hour]['orders']++;
            $hourlyBreakdown[$hour]['revenue'] += $order->total_price;
        }

        // Peak hour
        $peakHour = 0;
        $peakOrders = 0;
        foreach ($hourlyBreakdown as $h => $data) {
            if ($data['orders'] > $peakOrders) {
                $peakOrders = $data['orders'];
                $peakHour = $h;
            }
        }

        return view('admin.reports.index', compact(
            'orders', 'totalRevenue', 'totalOrders', 'avgOrder',
            'topProducts', 'dailyBreakdown', 'hourlyBreakdown',
            'categorySales', 'peakHour', 'peakOrders',
            'range', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $range = $request->query('range', 'this_month');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($range === 'today') {
            $dateFrom = Carbon::today()->toDateString();
            $dateTo = Carbon::today()->toDateString();
        } elseif ($range === 'yesterday') {
            $dateFrom = Carbon::yesterday()->toDateString();
            $dateTo = Carbon::yesterday()->toDateString();
        } elseif ($range === 'this_week') {
            $dateFrom = Carbon::now()->startOfWeek()->toDateString();
            $dateTo = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($range === 'this_month') {
            $dateFrom = Carbon::now()->startOfMonth()->toDateString();
            $dateTo = Carbon::now()->endOfMonth()->toDateString();
        }

        $orders = Order::with(['table', 'orderItems.product', 'payment'])
            ->where('status', 'completed')
            ->when(filled($dateFrom), fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when(filled($dateTo), fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->oldest()
            ->get();

        $filename = 'laporan-penjualan-' . ($dateFrom ?? 'all') . '-sd-' . ($dateTo ?? 'all') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Tanggal', 'Jam', 'No. Meja', 'Take Away', 'Item', 'Qty', 'Harga Satuan', 'Subtotal', 'Total Order', 'Metode Bayar']);

            $no = 1;
            foreach ($orders as $order) {
                $items = $order->orderItems;
                $paymentMethod = $order->payment?->payment_method ?? '-';
                $tableNo = $order->is_takeaway ? '-' : ($order->table?->number ?? '-');
                $isTakeaway = $order->is_takeaway ? 'Ya' : 'Tidak';
                $firstRow = true;

                if ($items->isEmpty()) {
                    fputcsv($handle, [
                        $no++,
                        $order->created_at->format('d/m/Y'),
                        $order->created_at->format('H:i'),
                        $tableNo, $isTakeaway, '-', 0, 0, 0,
                        $order->total_price,
                        $paymentMethod,
                    ]);
                    continue;
                }

                foreach ($items as $item) {
                    fputcsv($handle, [
                        $firstRow ? $no++ : '',
                        $firstRow ? $order->created_at->format('d/m/Y') : '',
                        $firstRow ? $order->created_at->format('H:i') : '',
                        $firstRow ? $tableNo : '',
                        $firstRow ? $isTakeaway : '',
                        $item->product?->name ?? '-',
                        $item->quantity,
                        $item->price,
                        $item->quantity * $item->price,
                        $firstRow ? $order->total_price : '',
                        $firstRow ? $paymentMethod : '',
                    ]);
                    $firstRow = false;
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
