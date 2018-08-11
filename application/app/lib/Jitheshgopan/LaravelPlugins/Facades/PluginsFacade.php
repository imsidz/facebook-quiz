<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:12 AM
 */

namespace Jitheshgopan\LaravelPlugins\Facades;


use Illuminate\Support\Facades\Facade;

class PluginsFacade extends Facade{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'plugins'; }
}