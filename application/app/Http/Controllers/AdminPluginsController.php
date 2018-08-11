<?php


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use App\Exceptions\InvalidConfigurationException;

class AdminPluginsController extends BaseController
{
    private $plugins;
    private $files;

    function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->plugins = \App::make('plugins');
        if(is_callable('parent::__construct')) {
            parent::__construct();
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        //
        $this->plugins->optimize();
        $plugins = $this->plugins->all();
        return View::make('admin.plugins.index')->with([
            'plugins' => $plugins,
        ]);
    }

    /**
     * Show the confure page
     *
     * @param $slug
     * @return Response
     */
    public function getConfigure($slug)
    {
        try {
            $plugin = $this->plugins->getPlugin($slug);
            $settingsEditor = $this->_makeSettingsEditor($slug);
        } catch (InvalidConfigurationException $e) {
            return Response::error($e->getMessage());
        }
        return View::make('admin.plugins.configure', [
            'pluginName'    =>  $plugin->get('name'),
            'editorContent' =>  $settingsEditor->render()
        ]);
    }

    /**
     * submit edited config data
     *
     * @param $slug
     * @return Response
     */
    public function postConfigure($slug)
    {
        try {
            $settingsEditor = $this->_makeSettingsEditor($slug);
        } catch (InvalidConfigurationException $e) {
            return Response::error($e->getMessage());
        }
        return $settingsEditor->render();
    }

    /**
     * Show the language editor
     *
     * @param $slug
     * @return Response
     */
    public function getTranslate($slug)
    {
        try {
            $settingsEditor = $this->_makeLanguageEditor($slug);
        } catch (InvalidConfigurationException $e) {
            return Response::error($e->getMessage());
        }
        return $settingsEditor->render();
    }

    /**
     * submit languages data
     *
     * @param $slug
     * @return Response
     */
    public function postTranslate($slug)
    {
        try {
            $settingsEditor = $this->_makeLanguageEditor($slug);
        } catch (InvalidConfigurationException $e) {
            return Response::error($e->getMessage());
        }
        return $settingsEditor->render();
    }

    public function postActivate($slug)
    {
        $active = Input::get('active');
        $active = ($active == "true") ? true : false;
        if($active)
            $this->plugins->enable($slug);
        else
            $this->plugins->disable($slug);
    }

    public function postInstall($slug)
    {
        $this->plugins->install($slug);
        Session::flash('plugin-install-success', true);
    }

    public function postUninstall($slug)
    {
        $this->plugins->uninstall($slug);
        Session::flash('plugin-uninstall-success', true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function _makeSettingsEditor($slug)
    {
        $plugin = $this->plugins->getPlugin($slug);
        $configKey = $this->plugins->getPluginConfigKey($slug);
        $schemaFile = $plugin->getProperty('config.file');
        if(!$schemaFile)
            throw new InvalidConfigurationException("Config file not specified");
        $schemaFile = $this->plugins->getPluginFilePath($plugin->getSlug(), $schemaFile);
        try {
            $schema = $this->files->get($schemaFile);
        } catch(FileNotFoundException $e) {
            throw new InvalidConfigurationException("Config file not found");
        }
        $settingsEditor = App::make('JsonSettingsEditor');
        $editorTitle = $plugin->getProperty('name') . ' - Plugin settings';
        $editorDescription = "This page lets you edit the settings for the plugin : " . $plugin->getProperty('name');
        $settingsEditor->initialize($editorTitle, $editorDescription, $configKey);
        $settingsEditor->setSchema($schema);
        return $settingsEditor;
    }

    public function _makeLanguageEditor($slug)
    {
        $plugin = $this->plugins->getPlugin($slug);
        $languageKey = $this->plugins->getPluginLanguagesKey($slug);
        $schemaFile = $plugin->getProperty('translation');
        if(!$schemaFile)
            throw new InvalidConfigurationException("Translations file not specified");
            $schemaFile = $this->plugins->getPluginFilePath($plugin->getSlug(), $schemaFile);
        try {
            $schema = $this->files->get($schemaFile);
            $schema = $this->_generateLanguageSchema($schema);
        } catch(FileNotFoundException $e) {
            throw new InvalidConfigurationException("Translations file not found");
        }
        $languageEditor = App::make('LanguageEditor');
        $editorTitle = $plugin->getProperty('name') . ' - Plugin translation';
        $editorDescription = "This page lets you translate the plugin : " . $plugin->getProperty('name');
        $languageEditor->initialize($editorTitle, $editorDescription, $languageKey);
        $languageEditor->setSchema($schema);
        $languageEditor->readData();
        $newData = $this->_generateLanguageData($languageEditor->getData());
        $languageEditor->setData($newData);
        return $languageEditor;
    }

    public function _addMissingTypesToLanguageSchema($schema)
    {
        $schemaObj = json_decode($schema, true);
        foreach($schemaObj as $key => $val){
            $schemaObj[$key]['type'] = "string";
        }
        $schema = json_encode($schemaObj);
        return $schema;
    }

    public function _generateLanguageSchema($originalSchema)
    {
        $originalSchema = $this->_addMissingTypesToLanguageSchema($originalSchema);
        return $originalSchema;
    }

    public function _generateLanguageData($existingData)
    {
        if(!$existingData)
            $existingData = [];
        $languages = \Languages::getLanguages();
        foreach ($languages as $key => $language) {
            if(!empty($existingData[$key]) && !empty($existingData[$key]['strings']))
                $languages[$key]['strings'] = $existingData[$key]['strings'];
            else
                $languages[$key]['strings'] = [];
        }
        return $languages;
        //dd($schema);
    }
}
