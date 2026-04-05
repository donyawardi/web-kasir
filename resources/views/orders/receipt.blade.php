<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    @php
        $w = 32; // karakter per baris untuk 58mm

        function rl($left, $right, $width = 32) {
            $pad = max(1, $width - mb_strlen($left) - mb_strlen($right));
            return $left . str_repeat(' ', $pad) . $right;
        }

        function rc($text, $width = 32) {
            $pad = max(0, intval(($width - mb_strlen($text)) / 2));
            return str_repeat(' ', $pad) . $text;
        }

        $sep = str_repeat('=', $w);
        $sep2 = str_repeat('-', $w);
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 48mm;
            margin: 0 auto;
            padding: 2mm 0;
            color: #000;
        }
        pre {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            white-space: pre;
            margin: 0;
        }
        .receipt-text { }
        .header-text {
            font-size: 14px;
            font-weight: bold;
        }
        @media print {
            html, body { width: 48mm; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 0; padding: 0; size: 48mm auto; }
            /* Feed paper past the tear bar (~30mm) so the last line is visible */
            pre { padding-bottom: 30mm !important; }
        }
        .print-actions {
            text-align: center;
            margin-top: 15px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }
        .print-actions .btn-row { display: flex; gap: 6px; margin-bottom: 6px; }
        .print-actions button, .print-actions a {
            display: inline-flex; align-items: center; justify-content: center; gap: 5px;
            padding: 10px 16px; font-size: 13px; font-weight: 600; border-radius: 8px;
            cursor: pointer; text-decoration: none; flex: 1; border: none; transition: background 0.15s;
        }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-print:hover { background: #4338ca; }
        .btn-back { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db !important; }
        .btn-back:hover { background: #e5e7eb; }
    </style>
</head>
<body>

<pre class="receipt-text"><span class="header-text">{{ rc('AYAM BAKAR', $w) }}
{{ rc('SEAFOOD 79', $w) }}</span>
{{ $sep }}
{{ rl('No. Pesanan', '#' . $order->id, $w) }}
{{ rl('Meja', $order->table->table_number ?? '-', $w) }}
@if($order->is_takeaway)
{{ rl('Tipe', 'TAKE AWAY', $w) }}
@endif
{{ rl('Tanggal', $order->created_at->format('d/m/Y H:i'), $w) }}
@if($payment)
{{ rl('Bayar', strtoupper($payment->payment_method), $w) }}
{{ rl('Status', $payment->status === 'success' ? 'LUNAS' : 'BELUM BAYAR', $w) }}
@endif
{{ $sep }}
@foreach($order->orderItems as $item)
{{ mb_substr($item->product->name ?? '-', 0, $w) }}
{{ rl('  ' . $item->quantity . 'x ' . number_format($item->price, 0, ',', '.'), number_format($item->quantity * $item->price, 0, ',', '.'), $w) }}
@endforeach
{{ $sep }}
{{ rl('Jumlah Item', $order->orderItems->sum('quantity') . ' pcs', $w) }}
<b>{{ rl('TOTAL', 'Rp' . number_format($order->total_price, 0, ',', '.'), $w) }}</b>
@if($payment && $payment->payment_method === 'cash' && $cashReceived)
{{ $sep2 }}
{{ rl('Tunai', 'Rp' . number_format($cashReceived, 0, ',', '.'), $w) }}
{{ rl('Kembalian', 'Rp' . number_format($cashReceived - $order->total_price, 0, ',', '.'), $w) }}
@endif
{{ $sep }}
{{ rc('Terima kasih!', $w) }}
{{ $sep }}
</pre>

    <div class="print-actions no-print">
        <div class="btn-row">
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
            <a class="btn-back" href="{{ url()->previous() }}">← Kembali</a>
        </div>
    </div>

    <script>
    // Setelah cetak selesai, beritahu halaman pembuka
    window.onafterprint = function() {
        if (window.opener) {
            window.opener.postMessage('receipt-printed', '*');
        }
    };
    @if(request('autoprint'))
    window.onload = function() { window.print(); };
    @endif
    </script>
</body>
</html>
