<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

$apiKey = config('services.biteship.key');
$response = Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => $apiKey,
])->get("https://api.biteship.com/v1/maps/areas", [
    'countries' => 'ID',
    'input' => 'Jakarta Selatan',
    'type' => 'single'
]);

file_put_contents('biteship_area.json', $response->body());
