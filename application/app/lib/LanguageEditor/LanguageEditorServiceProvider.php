<?php
namespace LanguageEditor;

use Illuminate\Support\ServiceProvider;
use App;
use MyConfigManager\ConfigManager;

class LanguageEditorServiceProvider extends ServiceProvider {

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
        $this->app->bind('LanguageEditor', function() {
            $languages = App::make('Languages');
            $configManager = new ConfigManager();
            return new LanguageEditor($configManager, $languages);
        });
	}

}
