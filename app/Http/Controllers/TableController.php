<?php

namespace App\Http\Controllers;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::paginate(10);
        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        // determine next numeric table number (handles numeric strings)
        $max = Table::all()->pluck('table_number')->map(function ($v) {
            return (int) preg_replace('/\D/', '', $v);
        })->max();

        $nextTableNumber = $max ? $max + 1 : 1;

        return view('tables.create', compact('nextTableNumber'));
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
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan meja.');
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

    public function regenerateQr(Request $request, Table $table)
    {
        try {
            $qrCodeData = route('order.show', ['table' => $table->id]);
            $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd());
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeData);
            $table->qr_code = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
            $table->save();

            $message = 'QR berhasil di-regenerate untuk meja ' . $table->table_number;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'qr' => $table->qr_code,
                ]);
            }

            return redirect()->route('tables.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error regenerate QR: ' . $e->getMessage());
            $err = 'Gagal meregenerate QR: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $err], 500);
            }
            return redirect()->back()->with('error', $err);
        }
    }

    public function downloadQr(Table $table)
    {
        if (empty($table->qr_code)) {
            return redirect()->back()->with('error', 'QR tidak tersedia untuk meja ini.');
        }

        // qr_code stored as data:image/svg+xml;base64,<data>
        if (preg_match('/^data:image\/svg\+xml;base64,(.*)$/', $table->qr_code, $m)) {
            $svg = base64_decode($m[1]);
            $filename = 'qr_table_' . $table->table_number . '.svg';
            return response($svg, 200, [
                'Content-Type' => 'image/svg+xml',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }

        return redirect()->back()->with('error', 'Format QR tidak dikenali.');
    }

    public function regenerateAll()
    {
        $tables = Table::all();
        foreach ($tables as $table) {
            try {
                $qrCodeData = route('order.show', ['table' => $table->id]);
                $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd());
                $writer = new Writer($renderer);
                $qrCodeSvg = $writer->writeString($qrCodeData);
                $table->qr_code = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
                $table->save();
            } catch (\Exception $e) {
                Log::error('RegenerateAll failed for table ' . $table->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('tables.index')->with('success', 'Regenerate QR selesai untuk semua meja.');
    }
}