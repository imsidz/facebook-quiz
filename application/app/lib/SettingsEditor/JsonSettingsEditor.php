<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 06/01/16
 * Time: 7:51 PM
 */

namespace SettingsEditor;
use Request;
use Response;

class JsonSettingsEditor extends AbstractSettingsEditor{
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
        $this->readData();
        if(Request::method() == "GET") {
            return \View::make('admin.settingsEditor.jsonSettingsEditor.edit', [
                'pageTitle' =>  $this->getTitle(),
                'pageDescription' =>  $this->getDescription(),
                'configSchema' =>  $this->getSchema(),
                'configData' =>  json_encode($this->getData())
            ]);
        } else {
            $settingsData = \Input::get('settings');
            $this->setData($settingsData);
            $this->saveData();
            return Response::make("Success");
        }
    }
}