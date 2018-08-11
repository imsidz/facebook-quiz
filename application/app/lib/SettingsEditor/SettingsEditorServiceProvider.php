<?php
namespace SettingsEditor;

use Illuminate\Support\ServiceProvider;
use App;
use MyConfigManager\ConfigManager;

class SettingsEditorServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
        $this->app->bind('JsonSettingsEditor', function() {
            $configManager = new ConfigManager();
            return new JsonSettingsEditor($configManager);
        });
	}

}
