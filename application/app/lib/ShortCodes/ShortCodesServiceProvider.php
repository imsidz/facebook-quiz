<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 03/11/15
 * Time: 7:09 AM
 */
namespace ShortCodes;
use Illuminate\Support\ServiceProvider;

class ShortCodesServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        \App::singleton('shortCodeEngine', function() {
            $shortCodeEngine = new ShortCodeEngine();
            return $shortCodeEngine;
        });
        $shortCodeEngine = \App::make('shortCodeEngine');
        $shortCodes = require(__DIR__ .'/shortcodes.php');
        $shortCodeEngine->loadShortCodes($shortCodes);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
