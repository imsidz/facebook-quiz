<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 07/01/16
 * Time: 12:47 AM
 */

namespace LaravelTranslate;


use Illuminate\Support\Facades\Facade;

class LaravelTranslateFacade extends Facade{
    protected static function getFacadeAccessor() {
        return 'Languages';
    }
}