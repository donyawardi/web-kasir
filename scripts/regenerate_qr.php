<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Table;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

foreach (Table::all() as $table) {
    try {
        $qrCodeData = route('order.show', ['table' => $table->id]);
        $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrCodeData);
        $table->qr_code = 'data:image/svg+xml;base64,' . base64_encode($svg);
        $table->save();
        echo "Regenerated QR for: {$table->table_number}\n";
    } catch (Exception $e) {
        echo "Failed for {$table->table_number}: " . $e->getMessage() . "\n";
    }
}
