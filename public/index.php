<?php

/**
 * Laravel - Front Controller
 *
 * This file is the entry point for all requests entering the application.
 * It bootstraps the framework, then dispatches the request to the appropriate
 * controller and returns the response back to the client.
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
