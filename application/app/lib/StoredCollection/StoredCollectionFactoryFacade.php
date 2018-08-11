<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 22/12/15
 * Time: 11:21 PM
 */

namespace StoredCollection;


use Illuminate\Support\Facades\Facade;

class StoredCollectionFactoryFacade extends Facade{
    protected static function getFacadeAccessor() {
        return 'StoredCollection\StoredCollectionFactory';
    }
}