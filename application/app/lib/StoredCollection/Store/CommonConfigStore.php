<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 22/12/15
 * Time: 11:23 PM
 */

namespace StoredCollection\Store;


use MyConfigManager\ConfigManager;
use StoredCollection\Contracts\StoredCollectionStore;
use StoredCollection\StoredCollection;

class CommonConfigStore implements StoredCollectionStore{
    private $nameSpace = 'StoredCollection';
    private $myConfig;
    private $siteConfigModel;

    function __construct(\SiteConfig $siteConfigModel, ConfigManager $configManager)
    {
        $this->myConfig = \App::make('siteConfig');
        //If config not loaded, load it now
        if(!$this->myConfig->all()) {
            $configManager->loadConfig();
        }

        $this->siteConfigModel = $siteConfigModel;
    }

    public function store(StoredCollection $storedCollection)
    {
        $configKey = $this->getConfigStoreKey($storedCollection->getKey());
        $configRow = $this->siteConfigModel->firstOrNew(['name' =>  $configKey]);
        $configValue = json_encode([
            'name' =>  $storedCollection->getName(),
            'model' =>  $storedCollection->getModel(),
            'itemKeys' =>  $storedCollection->getItemKeys(),
            'data' =>  $storedCollection->getData()
        ]);
        $configRow->value = $configValue;
        $this->myConfig->set($configKey, $configRow->value);
        $configRow->save();
    }

    public function readByKey($key)
    {
        $configKey = $this->getConfigStoreKey($key);
        $configData = $this->myConfig->get($configKey);
        if(!$configData)
            return false;
        return $configData;
    }

    public function readAllByModel($model)
    {
        // TODO: Implement readAllByModel() method.
    }

    public function getConfigStoreKey($key)
    {
        return $this->nameSpace . ':' . $key;
    }

    /**
     * @return string
     */
    public function getNameSpace()
    {
        return $this->nameSpace;
    }

    /**
     * @param string $nameSpace
     */
    public function setNameSpace($nameSpace)
    {
        $this->nameSpace = $nameSpace;
    }

    /**
     * @return \siteConfig
     */
    public function getMyConfig()
    {
        return $this->myConfig;
    }

    /**
     * @return \SiteConfig
     */
    public function getSiteConfigModel()
    {
        return $this->siteConfigModel;
    }



}