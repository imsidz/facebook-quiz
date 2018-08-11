<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 22/12/15
 * Time: 11:21 PM
 */

namespace StoredCollection;


use StoredCollection\Contracts\StoredCollectionStore;

class StoredCollectionFactory {


    private $store;
    function __construct(StoredCollectionStore $store)
    {
        $this->store = $store;
    }

    public function make($key, $name, $model, $itemKeys = []){
        $storedCollection = \App::make('StoredCollection\StoredCollection');
        $storedCollection->setKey($key);
        $storedCollection->setName($name);
        $storedCollection->setModel($model);
        $storedCollection->setItemKeys($itemKeys, false);
        return $storedCollection;
    }

    public function findByKey($key)
    {
        $storedCollectionData = $this->store->readByKey($key);
        if(!$storedCollectionData)
            return false;
        $storedCollection = $this->make($key, $storedCollectionData['name'], $storedCollectionData['model'], $storedCollectionData['itemKeys']);
        if(isset($storedCollectionData['data']))
            $storedCollection->setData($storedCollectionData['data']);
        return $storedCollection;
    }

    //Alias for findByKey
    public function get($key)
    {
        return $this->findByKey($key);
    }
}