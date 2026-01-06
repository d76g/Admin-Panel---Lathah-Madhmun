<?php
// CRITICAL: Start output buffering FIRST to catch any deprecation warnings
ob_start();

// Suppress deprecation warnings for PHP 8.4 compatibility
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Custom error handler to completely suppress deprecation warnings
// This catches deprecation warnings before they can be output
$previousErrorHandler = set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$previousErrorHandler) {
    // Completely suppress deprecation and strict warnings
    if ($errno === E_DEPRECATED || $errno === E_STRICT) {
        return true; // Suppress the error - don't output it
    }
    // Pass other errors to previous handler or default behavior
    if ($previousErrorHandler) {
        return $previousErrorHandler($errno, $errstr, $errfile, $errline);
    }
    return false;
}, E_ALL);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

// Clean output buffer to remove any deprecation warnings that were captured
// This prevents "headers already sent" errors
ob_end_clean();

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
