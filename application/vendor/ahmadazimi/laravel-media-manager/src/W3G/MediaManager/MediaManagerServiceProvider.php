<?php
namespace W3G\MediaManager;

use Illuminate\Support\ServiceProvider;

class MediaManagerServiceProvider extends ServiceProvider {

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
	public function boot() {
        $configPath =   __DIR__ . '/../../config/config.php';
        $this->publishes([
            $configPath => config_path('laravel-media-manager.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../../../public' => public_path('packages/ahmadazimi/laravel-media-manager'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../../views' => base_path('resources/views/vendor/laravel-media-manager'),
        ], 'views');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array(
			'command.mediamanager.publish',
		);
	}
}
