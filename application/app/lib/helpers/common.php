<?php

if(!function_exists('isAdmin')) {
    function isAdmin() {
        $admin = App::make('loggedInAdmin');
        return !!$admin;
    }
}

if(!function_exists('isRtl')) {
    function isRtl() {
        return (Config::get('languageDirection') == 'rtl');
    }

}

if(!function_exists('getCacheBusterParam')) {
    function getCacheBusterParam(){
        return 'v=' . Config::get('appMeta')['version'];
    }
}

if(!function_exists('assetWithCacheBuster')) {
    function assetWithCacheBuster($path, $secure = null)
    {
        return asset($path, $secure, true);
    }
}

if(!function_exists('uploadedFilePath')) {
    function uploadedFilePath($path) {
        return Storage::getDriver()->getAdapter()->getPathPrefix() . $path;
    }
}

if(!function_exists('timeago')) {
    function timeago($time) {
        return (new Date($time))->diffForHumans();
    }
}

if(!function_exists('isTrue')) {
    function isTrue($value)
    {
        return ($value === true || $value == "true");
    }
}

if(!function_exists('getConfig')) {
    function getConfig($key) {
        return \MyConfig::get($key);
    }
}

if(!function_exists('isConfigEnabled')) {
    function isConfigEnabled($key) {
        return \MyConfig::isTrue($key);
    }
}