<?php
namespace MenuManager;

use Illuminate\Support\ServiceProvider;

class MenuManagerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
        $this->initializeAdminMenu();
        \Event::listen('language:activated', function() {
            //$this->initializeFrontendMenu();
        });
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
        $this->app->singleton('navMenuManager', function() {
            return new Frontend\NavMenuManager(new Builder([]));
        });
	}

    public function initializeAdminMenu()
    {
        $adminMenuManager = new Admin\AdminMenuManager();
        $adminMenuManager->initialize();
        add_action('admin_menu', function($adminSidebarMenu) use ($adminMenuManager){
            $adminMenuManager->markNewMenuItems();
        });
    }

    /*public function initializeFrontendMenu() {
        $frontendMenuManager = \App::make('navMenuManager');
        $frontendMenuManager->initialize();
    }*/
}
