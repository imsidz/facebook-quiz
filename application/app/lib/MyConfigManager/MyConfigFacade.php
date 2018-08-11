<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 13/11/15
 * Time: 9:54 PM
 */

namespace MyConfigManager;


use Illuminate\Support\Facades\Facade;

class MyConfigFacade extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'siteConfig';
    }

}