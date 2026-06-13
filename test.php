<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pesanan = \App\Models\Pesanan::with('detail')->whereIn('id_pesanan', [4, 7, 8])->get();
foreach($pesanan as $p) {
    echo $p->id_pesanan . ' = ' . $p->detail->sum(function($d) { return $d->harga * $d->qty; }) . PHP_EOL;
}
