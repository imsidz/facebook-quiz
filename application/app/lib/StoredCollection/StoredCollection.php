<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 22/12/15
 * Time: 11:21 PM
 */

namespace StoredCollection;


use Illuminate\Events\Dispatcher;
use StoredCollection\Contracts\StoredCollectionStore;

class StoredCollection {
    private $store;
    private $key;
    private $name;
    private $model;
    private $itemKeys = [];
    private $data;

    private $items;
    private $cache;
    private $cacheEnabled = false;
    private $cacheLifetime = 60;
    private $dbItemsLoader;

    private $events;

    protected $modelPrimaryKey = 'id';
    public static $cacheKeyNamespace = 'StoredCollection';

    function __construct(StoredCollectionStore $store, Dispatcher $events)
    {
        $this->store = $store;
        $this->cache = \App::make('cache');
        $this->events = $events;
        $this->registerEventHandlers();
    }

    public function registerEventHandlers()
    {
        $this->registerItemKeysChangedEventHandler();
    }

    public function registerItemKeysChangedEventHandler()
    {
        $this->events->listen('itemKeys:changed', function() {
            if($this->isCacheEnabled())
                $this->clearCache();
            $this->loadItems();
        });
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    
    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getItemKeys()
    {
        return $this->itemKeys;
    }

    /**
     * @param array $itemKeys
     */
    public function setItemKeys($itemKeys, $triggerChange = true)
    {
        $this->itemKeys = $itemKeys;
        if($triggerChange)
            $this->events->fire('itemKeys:changed');
    }

    /**
     * @return StoredCollectionStore
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param StoredCollectionStore $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    /**
     * @param int $cacheLifetime
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * @return string
     */
    public function getModelPrimaryKey()
    {
        return $this->modelPrimaryKey;
    }

    /**
     * @param string $modelPrimaryKey
     */
    public function setModelPrimaryKey($modelPrimaryKey)
    {
        $this->modelPrimaryKey = $modelPrimaryKey;
    }

    /**
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**

     */
    public function enableCache()
    {
        $this->cacheEnabled = true;
    }

    /**
     */
    public function disableCache()
    {
        $this->cacheEnabled = false;
    }



    public function store()
    {
        $this->store->store($this);
    }

    public function cacheItems(){
        $items = $this->getItems();
        $this->cache->remember($this->getCacheKey(), $this->getCacheLifetime(), function() use($items) {
            return $items;
        });
    }

    public function getCachedItems(){
        return $this->cache->get($this->getCacheKey());
    }

    public function saveCache()
    {
        //Clear first
        $this->clearCache();
        $this->cacheItems();
    }

    public function clearCache(){
        $this->cache->forget($this->getCacheKey());
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /*
     * @param $tryCache boolean Check in cache first
     */
    public function loadItems($tryCache = true){
        $items = null;
        if($tryCache && $this->isCacheEnabled())
            $items = $this->getCachedItems();
        if(!$items)
            $items = $this->getItemsFromDB();
        $this->items = $items;

        if($this->isCacheEnabled())
            $this->saveCache();

        return $this;
    }

    public function defaultDBItemsLoader(){
        return \App::make($this->model)->whereIn($this->modelPrimaryKey, $this->getItemKeys())->get();
    }
    public function getItemsFromDB()
    {
        if(is_callable($this->dbItemsLoader))
            return call_user_func($this->dbItemsLoader, $this);
        else
            return \App::make($this->model)->whereIn($this->modelPrimaryKey, $this->getItemKeys())->get();
    }

    public function getCacheKey()
    {
        return self::$cacheKeyNamespace . ':' . $this->key;
    }

    public function setDBItemsLoader($callback)
    {
        $this->dbItemsLoader = $callback;
    }

    public function hasItemWithId($itemKey)
    {
        return in_array($itemKey, $this->getItemKeys());
    }

    public function hasItem($item)
    {
        $itemPrimaryKeyVal = $this->getItemPrimaryKey($item);
        return $this->hasItemWithId($itemPrimaryKeyVal);
    }

    public function addItem($item)
    {
        $itemPrimaryKeyVal = $this->getItemPrimaryKey($item);
        $this->addItemById($itemPrimaryKeyVal);
    }

    public function addItemById($itemId) {
        if($this->hasItemWithId($itemId))
            return;
        $this->itemKeys[] = $itemId;
        $this->events->fire('itemKeys:changed');
    }

    public function removeItem($item)
    {
        $itemPrimaryKeyVal = $this->getItemPrimaryKey($item);
        $this->removeItemById($itemPrimaryKeyVal);
    }

    public function removeItemById($itemId) {
        if(!$this->hasItemWithId($itemId))
            return;
        $key = array_search($itemId, $this->itemKeys);
        if($key !== false) {
            unset($this->itemKeys[$key]);
            $this->events->fire('itemKeys:changed');
        }
    }

    public function getItemPrimaryKey($item)
    {
        $itemPrimaryKeyVal = $item->{$this->modelPrimaryKey};
        return $itemPrimaryKeyVal;
    }

    /*
     * Alias to store
     */
    public function save()
    {
        $this->store();
    }

    public function getItemsCount()
    {
        return count($this->getItemKeys());
    }
}