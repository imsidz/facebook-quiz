<?php namespace Jitheshgopan\LaravelPlugins;

use Illuminate\Support\ServiceProvider;

class LaravelPluginsServiceProvider extends ServiceProvider {

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
        $plugins = \App::make('plugins');
        $plugins->loadPluginTranslations();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
        $this->app->singleton('plugins', function($app) {
            $repository = $app->make('Caffeinated\Modules\Contracts\Repository');
            $plugins = new Plugins($app, $repository);
            $plugins->setAppVersion(\Config::get('laravel-plugins.appVersion'));
            return $plugins;
        });
        require_once(__DIR__ . '/helpers.php');
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
