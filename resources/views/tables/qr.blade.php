<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Meja {{ $table->table_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-white text-slate-900">
    <main class="px-4 py-10">
        <div class="max-w-md mx-auto text-center">
            <div class="bg-white shadow-sm rounded-2xl p-8 border border-slate-100">
                <h1 class="text-lg font-semibold text-slate-800 mb-6">Scan QR untuk Pesan</h1>
                <div class="flex justify-center">
                    @if($table->qr_code)
                        <img src="{{ $table->qr_code }}" alt="QR Code Meja {{ $table->table_number }}" class="w-52 h-52">
                    @else
                        <p class="text-slate-400">QR Code belum tersedia</p>
                    @endif
                </div>
                <p class="mt-4 text-sm text-slate-500">Meja {{ $table->table_number }}</p>
            </div>
        </div>
    </main>
</body>
</html>