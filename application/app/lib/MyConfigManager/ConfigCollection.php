<?php
namespace MyConfigManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 13/11/15
 * Time: 7:04 PM
 */

class ConfigCollection extends Collection{
    public function get($key = null, $default = null)
    {
        if(!$key)
            return $this->items;
        return Arr::get($this->items, $key, $default);
    }

    public function isTrue($key) {
        $val = $this->get($key);
        return ($val === true || $val == "true");
    }

    public function setConfig($config){
        $this->items = $config;
    }

    public function set($key, $value)
    {
        return $this->put($key, $value);
    }

}