<?php

define('LARAVEL_START', microtime(true));

// 1. Muat Autoloader Composer
require __DIR__.'/../vendor/autoload.php';

// 2. Muat Bootstrap App & Tangani Permintaan
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// 3. Kirim Respon
$response->send();

// 4. Hentikan Kernel
$kernel->terminate($request, $response);
