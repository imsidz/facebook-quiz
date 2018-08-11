<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:15 AM
 */

namespace Jitheshgopan\LaravelPlugins;


class PluginFactory {

    public static function make($manifest) {
        return new Plugin();
    }
}