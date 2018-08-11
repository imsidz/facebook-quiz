<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 06/01/16
 * Time: 7:51 PM
 */

namespace SettingsEditor;


use MyConfigManager\ConfigManager;

abstract class AbstractSettingsEditor implements SettingsEditorInterface {

    public $title;
    public $description;
    public $configKey;
    public $data;
    public $configManager;
    private $myConfig;

    function __construct(ConfigManager $configManager)
    {
        $this->myConfig = \App::make('siteConfig');
        $this->configManager = $configManager;
        //If config not loaded, load it now
        if(!$this->myConfig->all()) {
            $configManager->loadConfig();
        }
    }

    public function initialize($title, $description, $configKey)
    {
        $this->title = $title;
        $this->description = $description;
        $this->configKey = $configKey;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setConfigKey($key)
    {
        $this->configKey = $key;
    }

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

    public function readData()
    {
        $settingsData = $this->myConfig->get($this->configKey, []);
        $this->data = $settingsData;
        return $settingsData;
    }

    public function saveData()
    {
        $this->configManager->saveConfig($this->configKey, $this->getData());
    }

    public abstract function render();
}