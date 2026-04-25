<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .wrap { max-width: 340px; width: 100%; }

        .receipt {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
        }

        .receipt-logo {
            display: flex;
            justify-content: center;
            padding: 10px 0 6px;
        }
        .receipt-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            /* hapus latar hitam PNG saat di atas kertas putih */
            mix-blend-mode: multiply;
            display: block;
        }

        .receipt pre {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.6;
            white-space: pre;
            color: #000;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            font-size: 13px;
            color: #6b7280;
        }
        input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; }

        .badge {
            display: inline-block;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 100px;
            margin-left: 4px;
        }
        .badge-on  { background: #dcfce7; color: #166534; }
        .badge-off { background: #f3f4f6; color: #6b7280; }

        .info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .controls { display: flex; gap: 8px; margin-top: 12px; }
        .controls button {
            flex: 1;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            transition: background 0.15s;
        }
        .controls button:hover { background: #f3f4f6; }
        .btn-print {
            background: #4f46e5 !important;
            color: #fff !important;
            border-color: #4f46e5 !important;
        }
        .btn-print:hover { background: #4338ca !important; }

        @media print {
            body { background: none; padding: 0; min-height: 0; display: block; }
            .no-print { display: none !important; }
            .receipt-logo { display: none !important; }
            .receipt { border: none; border-radius: 0; padding: 0; background: #fff; }
            pre { padding-bottom: 20mm; }
            @page { margin: 0; size: 58mm auto; }
        }
    </style>
</head>
<body>

<div class="wrap">
    @php
        $W = 32;

        $rl = function(string $left, string $right) use ($W): string {
            $pad = max(1, $W - mb_strlen($left) - mb_strlen($right));
            return $left . str_repeat(' ', $pad) . $right;
        };

        $rc = function(string $text) use ($W): string {
            $pad = max(0, (int) floor(($W - mb_strlen($text)) / 2));
            return str_repeat(' ', $pad) . $text;
        };

        $sep  = str_repeat('=', $W);
        $sep2 = str_repeat('-', $W);

        $paymentLabel = match($payment?->payment_method) {
            'cash'             => 'TUNAI',
            'qris'             => 'QRIS',
            'later','cashier'  => 'BAYAR DI KASIR',
            default            => strtoupper($payment?->payment_method ?? '-'),
        };

        $statusLabel = match(true) {
            in_array($payment?->status, ['paid', 'success', 'settlement']) => 'LUNAS',
            $payment?->status === 'pending' => 'PENDING',
            default => strtoupper($payment?->status ?? 'PENDING'),
        };

        $tipeLabel = $order->is_takeaway ? 'BAWA PULANG' : 'DINE IN';
        $totalQty  = $order->orderItems->sum('quantity');

        $lines   = [];
        $lines[] = $rc('AYAM BAKAR');
        $lines[] = $rc('SEAFOOD 79');
        $lines[] = $sep;
        $lines[] = $rl('No. Pesanan', '#' . str_pad($order->id, 4, '0', STR_PAD_LEFT));
        if (!$order->is_takeaway) {
            $lines[] = $rl('Meja', $order->table->table_number ?? '-');
        }
        $lines[] = $rl('Tipe', $tipeLabel);
        $lines[] = $rl('Tanggal', $order->created_at->format('d/m/Y H:i'));
        $lines[] = $rl('Bayar', $paymentLabel);
        $lines[] = $rl('Status', $statusLabel);
        $lines[] = $sep;
        foreach ($order->orderItems as $item) {
            $lines[] = $item->product->name ?? 'Produk';
            $lines[] = $rl(
                '  ' . $item->quantity . 'x Rp' . number_format($item->price, 0, ',', '.'),
                number_format($item->subtotal, 0, ',', '.'),
            );
        }
        $lines[] = $sep;
        $lines[] = $rl('Jumlah Item', $totalQty . ' pcs');
        $lines[] = $rl('TOTAL', 'Rp' . number_format($order->total_price, 0, ',', '.'));
        if ($payment?->payment_method === 'cash' && $cashReceived) {
            $change  = (float) $cashReceived - (float) $order->total_price;
            $lines[] = $sep2;
            $lines[] = $rl('Tunai', 'Rp' . number_format($cashReceived, 0, ',', '.'));
            $lines[] = $rl('Kembalian', 'Rp' . number_format(max(0, $change), 0, ',', '.'));
        }
        $lines[] = $sep;
        $lines[] = $rc('Terima kasih!');
        $lines[] = $sep;
    @endphp

    <div class="receipt">
        <div class="receipt-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Ayam Bakar Seafood 79">
        </div>
        <pre id="receipt-content">{{ implode("\n", $lines) }}</pre>
    </div>

    <div class="toggle-row no-print">
        <input type="checkbox" id="autoprint-toggle">
        <label for="autoprint-toggle">
            Auto print aktif
            <span class="badge badge-off" id="auto-badge">OFF</span>
        </label>
    </div>

    <div class="info no-print" id="info-text">
        Auto print <b>nonaktif</b> — klik tombol cetak manual di bawah.
    </div>

    <div class="controls no-print">
        <button class="btn-print" id="btn-cetak">🖨️ Cetak Struk</button>
        <button id="btn-reload">↺ Reload Halaman</button>
    </div>
</div>

<script>
    const STORAGE_KEY = 'receipt_autoprint';
    const toggle = document.getElementById('autoprint-toggle');
    const badge  = document.getElementById('auto-badge');
    const info   = document.getElementById('info-text');

    function applyState(enabled) {
        toggle.checked = enabled;
        localStorage.setItem(STORAGE_KEY, enabled ? '1' : '0');
        if (enabled) {
            badge.textContent = 'ON';
            badge.className   = 'badge badge-on';
            info.innerHTML    = 'Auto print <b>aktif</b> — struk akan otomatis tercetak saat halaman dibuka.';
        } else {
            badge.textContent = 'OFF';
            badge.className   = 'badge badge-off';
            info.innerHTML    = 'Auto print <b>nonaktif</b> — klik tombol cetak manual di bawah.';
        }
    }

    // Baca preferensi tersimpan dari localStorage
    applyState(localStorage.getItem(STORAGE_KEY) === '1');

    toggle.addEventListener('change', function () {
        applyState(this.checked);
    });

    document.getElementById('btn-cetak').addEventListener('click', function () {
        window.print();
    });

    document.getElementById('btn-reload').addEventListener('click', function () {
        window.location.reload();
    });

    // Auto print setelah seluruh halaman + gambar selesai dimuat
    window.addEventListener('load', function () {
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            setTimeout(() => window.print(), 400);
        }
    });
</script>

</body>
</html>
