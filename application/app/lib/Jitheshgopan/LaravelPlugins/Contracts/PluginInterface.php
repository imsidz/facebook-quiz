<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:27 AM
 */

namespace Jitheshgopan\LaravelPlugins\Contracts;


interface PluginInterface {

    /*
     * Install the plugin
     */
    public function install();

    /*
     * Uninstall the plugin
     */
    public function uninstall();
}