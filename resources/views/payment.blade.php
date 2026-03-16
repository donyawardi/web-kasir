{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Pembayaran</h1>
    <p>Total Pembayaran: Rp{{ number_format($order->items->sum('total_price'), 0, ',', '.') }}</p>
    <button id="pay-btn">Bayar Sekarang</button>
    <div id="qris-container"></div>

    <script>
        document.getElementById('pay-btn').addEventListener('click', function() {
            axios.post('/api/payment', {
                order_id: {{ $order->id }}
            }).then(response => {
                document.getElementById('qris-container').innerHTML = `
                    <p>Scan QR Code untuk membayar:</p>
                    <img src="${response.data.qr_code_url}" alt="QR Code">
                `;
                setTimeout(checkPaymentStatus, 5000);
            }).catch(error => {
                console.error(error);
                alert('Gagal memulai pembayaran.');
            });
        });

        function checkPaymentStatus() {
            axios.post('/api/payment/confirm', {
                order_id: {{ $order->id }}
            }).then(response => {
                alert('Pembayaran berhasil!');
                window.location.href = '/';
            }).catch(error => {
                setTimeout(checkPaymentStatus, 5000);
            });
        }
    </script>
</body>
</html> --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pembayaran</h1>
    <p><strong>Meja {{ $order->table->table_number}}</strong></p>
    <p><strong>Nomor Pesanan:</strong> {{ $order->id }}</p>
    <p><strong>Total Bayar:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>

    <p>Silakan scan QR di bawah untuk membayar:</p>
    {{-- Tempel QRIS atau link ke penyedia pembayaran --}}
    <img src="{{ asset('img/qris-placeholder.png') }}" alt="QRIS" style="width:200px">

    <p>Terima Kasih </p>
</div>
@endsection
