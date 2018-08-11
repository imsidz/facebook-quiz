<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 13/11/15
 * Time: 10:21 PM
 */

namespace MyConfigManager;


use Illuminate\Support\ServiceProvider;

class MyConfigServiceProvider extends ServiceProvider{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \App::singleton('siteConfig', function() {
            //Initialize config as empty array
            $config = [];
            return new ConfigCollection($config);
        });
    }

}