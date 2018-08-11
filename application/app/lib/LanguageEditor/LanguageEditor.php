<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 06/01/16
 * Time: 7:51 PM
 */

namespace LanguageEditor;


use MyConfigManager\ConfigManager;
use Request;
use Response;

class LanguageEditor implements LanguageEditorInterface {

    public $title;
    public $description;
    public $configKey;
    public $data;
    public $languages;
    public $configManager;
    private $myConfig;

    function __construct(ConfigManager $configManager, \LaravelTranslate\Languages $languages)
    {
        $this->configManager = $configManager;
        $this->myConfig = \App::make('siteConfig');
        $this->languages = $languages;
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

    protected $schema;

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param mixed $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function render()
    {
        //$this->readData();
        if(Request::method() == "GET") {
            return \View::make('admin.languageEditor.edit', [
                'pageTitle' =>  $this->getTitle(),
                'pageDescription' =>  $this->getDescription(),
                'languagesSchema' =>  $this->getSchema(),
                'languagesData' =>  json_encode($this->getData())
            ]);
        } else {
            $languagesData = \Input::get('languages');
            $languagesData = json_decode($languagesData, true);
            $this->setData($languagesData);
            $this->saveData();
            return Response::make("Success");
        }
    }
}