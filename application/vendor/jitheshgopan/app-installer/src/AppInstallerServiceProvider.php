<?php namespace Jitheshgopan\AppInstaller;

use Illuminate\Support\ServiceProvider;

class AppInstallerServiceProvider extends ServiceProvider {

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
        $configPath =   __DIR__ . '/config/config.php';
        $this->publishes([
            $configPath => config_path('app-installer.php')
        ], 'config');

		if(!defined('INSTALLER_VENDOR_PATH'))
		    define('INSTALLER_VENDOR_PATH', 'jitheshgopan/app-installer');
        if(!defined('INSTALLER_VENDOR_NAME'))
		    define('INSTALLER_VENDOR_NAME', 'jitheshgopan');
        if(!defined('INSTALLER_NAMESPACE'))
		    define('INSTALLER_NAMESPACE', 'app-installer');

        $this->publishes([
            __DIR__.'/../public' => public_path('packages/' . INSTALLER_VENDOR_PATH),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/views', INSTALLER_NAMESPACE);

        $this->loadTranslationsFrom(__DIR__.'/lang', INSTALLER_NAMESPACE);

        $this->publishes([
            __DIR__.'/lang' => base_path('resources/lang/vendor/' . INSTALLER_NAMESPACE),
        ]);

		$this->loadRoutesFrom(__DIR__ . '/routes.php');
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
