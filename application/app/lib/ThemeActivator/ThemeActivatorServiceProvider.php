<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 16/01/16
 * Time: 5:25 AM
 */

namespace ThemeActivator;


use Illuminate\Support\ServiceProvider;
use MyConfigManager\ConfigManager;

class ThemeActivatorServiceProvider extends ServiceProvider{

    public function boot()
    {
        \Event::listen('config:loaded', function() {
            \App::make('themes')->setActive(\App::make('themeActivator')->getActiveTheme());
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('themeActivator', function() {
            $themeActivator = new ThemeActivator(new ConfigManager());
            return $themeActivator;
        });
    }

}