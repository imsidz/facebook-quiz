<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 16/01/16
 * Time: 4:18 AM
 */

namespace Themes;


use Illuminate\Support\Facades\Facade;

class ThemesFacade extends Facade{
    protected static function getFacadeAccessor() {
        return 'themes';
    }
}