<?php
namespace Themes;

use Illuminate\Support\ServiceProvider;

class ThemesServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        /*$adminSidebarMenu = \Menu::get('AdminSidebarMenu');
        $order = $adminSidebarMenu->item('settings')->data('order');
        $adminSidebarMenu->add('Themes', ['icon' => 'fa fa-fw fa-eyedropper', 'url'    =>  action('AdminThemesController@getIndex')])->data('order', $order + 0.1);*/
        $themes = \App::make('themes');
        $themes->loadThemeTranslations();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('themes', function($app) {
            return new Themes($app['files'], $app['config'], $app['view']);
        });
    }

}
