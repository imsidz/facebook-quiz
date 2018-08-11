<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 07/01/16
 * Time: 12:44 AM
 */

namespace LaravelTranslate;
use Illuminate\Support\ServiceProvider;

class LaravelTranslateServiceProvider extends ServiceProvider{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Languages', function() {
            return new Languages();
        });
    }

}