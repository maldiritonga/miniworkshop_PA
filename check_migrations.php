<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = \Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
$migrations = DB::table('migrations')->get();
echo "Total Migrations: " . count($migrations) . "\n";
foreach ($migrations as $m) {
    echo "- " . $m->migration . " (batch: " . $m->batch . ")\n";
}
