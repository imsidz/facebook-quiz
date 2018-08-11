<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:35 AM
 */

namespace Jitheshgopan\LaravelPlugins;


use Illuminate\Events\Dispatcher;
use Jitheshgopan\LaravelPlugins\Contracts\PluginInterface;

abstract class AbstractPlugin implements PluginInterface {

    public $events;

    function __construct()
    {
        $this->events = new Dispatcher();
    }
}