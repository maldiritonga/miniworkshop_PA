<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Produk::find(6);
echo "Before: ", $p->diskon_persen, "\n";
$p->update(['diskon_persen' => 15]);
$p = App\Models\Produk::find(6);
echo "After: ", $p->diskon_persen, "\n";
