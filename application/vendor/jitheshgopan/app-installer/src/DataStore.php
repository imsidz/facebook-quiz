<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:40 AM
 */

namespace Jitheshgopan\AppInstaller;


use \Session;

class DataStore {
    function store($key, $value){
        Session::put($key, $value);
        return true;
    }
    function read($key){
        return Session::get($key);
    }
}