<?php
define('CMS_PACKED_MODE', true);
function setEnvVariable($name, $value) {
    if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($name)) {
        apache_setenv($name, $value);
    }
    if (function_exists('putenv')) {
        putenv("$name=$value");
    }
    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
}
function loadEnvConfig() {
    $baseConfig = require(__DIR__ . '/application/base-config.php');
    $config = require(__DIR__ . '/config.php');
    $config = array_merge($baseConfig, $config);
    foreach ($config as $key => $value) {
        setEnvVariable($key, $value);
    }
}
loadEnvConfig();
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/

require __DIR__.'/application/bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/application/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
