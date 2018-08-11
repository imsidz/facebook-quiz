<?php
namespace Plugins;

use Illuminate\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $adminSidebarMenu = \Menu::get('AdminSidebarMenu');
        $order = $adminSidebarMenu->item('settings')->data('order');
        $adminSidebarMenu->add('Plugins', ['icon' => 'fa fa-fw fa-plug', 'url'    =>  action('AdminPluginsController@getIndex')])->data('order', $order + 0.1);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        \Event::listen('caffeinated:modules:loaded', function() {
            \Event::fire('plugins:loaded');
        });
    }

}
