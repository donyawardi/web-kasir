<?php

namespace App\Http\Controllers;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:tables',
            'status' => 'required|in:available,occupied'
        ]);
    
        try {
            // 1. Simpan data meja sementara tanpa QR
            $table = new Table();
            $table->table_number = $request->table_number;
            $table->status = $request->status;
            $table->save(); // simpan dulu supaya dapat ID
    
            // 2. Generate QR Code berdasarkan ID yang sudah didapat
            $qrCodeData = route('order.show', ['table' => $table->id]); 
    
            $renderer = new ImageRenderer(
                new RendererStyle(256),
                new SvgImageBackEnd()
            );
    
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeData);
            $qrCodeBase64 = base64_encode($qrCodeSvg);
    
            // 3. Simpan kembali dengan QR code
            $table->qr_code = 'data:image/svg+xml;base64,' . $qrCodeBase64;
            $table->save();
    
            return redirect()->route('tables.index')->with('success', 'Meja berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan meja: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|unique:tables,table_number,' . $table->id,
            'status' => 'required|in:available,occupied'
        ]);

        $table->update([
            'table_number' => $request->table_number,
            'status' => $request->status
        ]);

        return redirect()->route('tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Meja berhasil dihapus.');
    }

    public function showQrCode(Table $table)
    {
        return view('tables.qr', compact('table'));
    }

}