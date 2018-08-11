<?php

define('LARAVEL_START', microtime(true));
if(!defined('CMS_PACKED_MODE')) {
    //index.php not executed. Guess Is access from console
    define('CMS_PACKED_MODE', false);
}

if(!function_exists('content_path')) {
    function content_path($path = '') {
        if(CMS_PACKED_MODE) {
            $parentDir = base_path('../');
        } else {
            $parentDir = base_path('public/');
        }
        return $parentDir . 'content' .($path ? '/'.$path : $path);
    }
}

if(!function_exists('content_url')) {
    function content_url($path) {
        if (app('url')->isValidUrl($path)) {
            return $path;
        }
        $path =  'content/' . ltrim($path, '/');

        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = app('url')->to('/');

        $i = 'index.php';

        $root = \Illuminate\Support\Str::contains($root, $i) ? str_replace('/'.$i, '', $root) : $root;

        return $root.'/'.trim($path, '/');
    }
}

if(!function_exists('install_path')) {
    function install_path($path = '') {
        if(CMS_PACKED_MODE) {
            $installPath = base_path('../');
        } else {
            $installPath = base_path('/');
        }
        $installPath = rtrim($installPath, '/');
        return $installPath .($path ? '/'.$path : $path);
    }
}

//Implementing Cache buster
if(!function_exists('asset')) {
    function asset($path, $secure = null, $addCacheBuster = true)
    {
        if(CMS_PACKED_MODE) {
            $path = 'application/public/' . $path;
        }
        //Uses current protocol by default. Loads secure assets if the page is requested via https
        if (is_null($secure))
            $secure = Request::secure();

        $assetUrl = app('url')->asset($path, $secure);
        if($addCacheBuster) {
            $assetUrl = addCacheBusterToUrl($assetUrl);
        }
        $assetUrl = apply_filters('asset_url', $assetUrl, $path, $secure, $addCacheBuster);
        return $assetUrl;
    }
}

if(!function_Exists('addCacheBusterToUrl')) {
    function addCacheBusterToUrl($url) {
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        //If path is empty or is not a file, don't add cahce buster
        if(empty($url) || !$ext)
            return $url;
        $url_parsed = parse_url($url);
        $queryStringParsed = array();

        // Grab our first query string
        parse_str(@$url_parsed['query'], $queryStringParsed);

        // Here's the other query string
        $cacheBusterQueryString = getCacheBusterParam();
        $cacheBusterQueryStringParsed = array();
        parse_str($cacheBusterQueryString, $cacheBusterQueryStringParsed);

        // Stitch the two query strings together
        $final_query_string_array = array_merge($queryStringParsed, $cacheBusterQueryStringParsed);
        $final_query_string = http_build_query($final_query_string_array);
        $final_query_string = urldecode($final_query_string);

        // Now, our final URL:
        $port = @$url_parsed['port'];
        $host = @$url_parsed['host'];
        $host = !$port ? $host : $host . ":" . $port;
        $subPath = @$url_parsed['path'];
        $new_url = $url_parsed['scheme']
            . '://'
            . $host
            . $subPath
            . '?'
            . $final_query_string;
        return $new_url;
    }
}
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Include The Compiled Class File
|--------------------------------------------------------------------------
|
| To dramatically increase your application's performance, you may use a
| compiled class file which contains all of the classes commonly used
| by a request. The Artisan "optimize" is used to create this file.
|
*/

$compiledPath = __DIR__.'/cache/compiled.php';

if (file_exists($compiledPath)) {
    require $compiledPath;
}
