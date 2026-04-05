<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Meja</h2>
    </x-slot>

<div class="container mx-auto py-6">
    <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Daftar Meja</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Meja</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($tables as $table)
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $table->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $table->table_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="badge {{ $table->status == 'available' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} px-2 py-1 rounded">
                            {{ ucfirst($table->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($table->qr_code)
                            <img src="{{ $table->qr_code }}" alt="QR Code" class="w-16 h-16">
                        @else
                            <span class="text-red-600">QR Code tidak tersedia</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.tables.regenerate_qr', $table->id) }}" method="POST" class="inline-block mr-3">
                            @csrf
                            <button type="button" class="text-blue-600 hover:underline regenerate-qr-btn" data-table-id="{{ $table->id }}" data-table-number="{{ $table->table_number }}">Regenerate QR</button>
                        </form>
                        <a href="{{ route('admin.tables.download_qr', $table->id) }}" class="text-blue-600 hover:underline mr-3" target="_blank" rel="noopener">Download QR</a>
                        <a href="{{ route('admin.tables.edit', $table->id) }}" class="text-yellow-600 hover:underline mr-3">Edit</a>
                        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus meja ini?')">
                             @csrf
                             @method('DELETE')
                             <button class="text-red-600">Hapus</button>
                         </form>
                     </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $tables->links() }}
    </div>

    {{-- Floating action button bottom-right --}}
    <a href="{{ route('admin.tables.create') }}" aria-label="Tambah Meja" class="fixed z-50 right-6 bottom-6 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span class="hidden sm:inline text-sm font-medium">Tambah Meja</span>
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.regenerate-qr-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tableId = btn.getAttribute('data-table-id');
                var tableNumber = btn.getAttribute('data-table-number');
                if (!confirm('Regenerate QR untuk meja ini?')) return;

                fetch('{{ url('') }}/admin/tables/' + tableId + '/regenerate-qr', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).then(function (r) { return r.json(); }).then(function (data) {
                    if (data.success) {
                        // show friendly message with table number
                        alert('Meja ' + tableNumber + ' berhasil digenerate');
                        // update only the QR img in the same row as the clicked button
                        var row = btn.closest('tr');
                        if (row) {
                            var img = row.querySelector('td img');
                            if (img && data.qr) img.src = data.qr;
                        }
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                }).catch(function (err) {
                    alert('Terjadi error saat regenerate');
                });
            });
        });
    });
</script>
</x-app-layout>
