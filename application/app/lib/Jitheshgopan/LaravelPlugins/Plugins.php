<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 09/11/15
 * Time: 7:36 PM
 */

namespace Jitheshgopan\LaravelPlugins;


use Caffeinated\Modules\Modules;
use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\version;
use MyConfig;

class Plugins extends Modules{
    protected $appVersion;

    public function loadPluginTranslations()
    {
        \Event::listen('language:activated', function($languageCode) {
            $modules = $this->repository->enabled();
            $languages = \Languages::getLanguages();
            $modules->each(function($properties) use(&$languages) {
                $languagesKey = $this->getPluginLanguagesKey($properties['slug']);
                $pluginLanguageData = MyConfig::get($languagesKey);
                if(!$pluginLanguageData || !is_array($pluginLanguageData))
                    return;
                foreach ($languages as $key => $language) {
                    $pluginStrings = array_map(function($l) use($language){
                        if($l['code'] == $language['code'])
                            return $l['strings'];
                    }, $pluginLanguageData);
                    if(count($pluginStrings)) {
                        $languages[$key]['strings']['plugins'] = [];
                        $languages[$key]['strings']['plugins'][$properties['slug']] = current($pluginStrings);
                    }
                }
            });
            \Languages::setLanguages($languages);
            \Languages::exposeToView($languages);
        });
    }
    public function isCompatible($slug)
    {
        if(!$this->exists($slug)) {
            return Exceptions\PluginNotFoundException();
        }
        $appSemver = new version($this->appVersion);
        $supportedVersionString = $this->getProperty($slug . '::supports');
        $supportedVersion = new expression($supportedVersionString);
        return $appSemver->satisfies($supportedVersion);
    }

    public function setAppVersion($appVersion) {
        $this->appVersion = $appVersion;
        return $this;
    }

    public function getAppVersion() {
        return $this->appVersion;
    }

    public function getPlugin($slug)
    {
        $plugin = new Plugin($slug, $this->getProperties($slug), $this);
        return $plugin;
    }

    public function getProperties($slug)
    {
        $plugin = $this->where('slug', $slug);
        return $plugin;
    }

    public function isInstalled($slug)
    {
        $plugin = $this->getPlugin($slug);
        return $plugin->isInstalled();
    }

    public function install($slug){
        if($this->isInstalled($slug))
            throw new Exceptions\PluginAlreadyInstalledException();
        $plugin = $this->getPlugin($slug);
        $plugin->install();
        $this->markPluginAsInstalled($slug);
    }

    public function uninstall($slug){
        if(!$this->isInstalled($slug))
            throw new Exceptions\PluginNotInstalledException();

        //Disable first
        $this->disable($slug);

        $plugin = $this->getPlugin($slug);
        $plugin->uninstall();
        $this->markPluginAsUninstalled($slug);
    }

    public function enable($slug, $ensureInstalled = true)
    {
        if($ensureInstalled && !$this->isInstalled($slug))
            throw new Exceptions\PluginNotInstalledException();
        return $this->repository->enable($slug);
    }

    public function disable($slug)
    {
        return $this->repository->disable($slug);
    }

    /*
     * Marks plugin as installed
     */
    protected function markPluginAsInstalled($slug) {
        return $this->repository->set($slug.'::installed', true);
    }

    protected function markPluginAsUninstalled($slug) {
        return $this->repository->set($slug.'::installed', false);
    }

    protected function getInstalledSlugsFromCache()
    {
        $installed = $this->where('installed', true);
        if(!$installed)
            $installed = [];
        return $installed;
    }

    public function getPluginAsset($properties, $relativePath){
        return \Config::get('plugins.public_path').$this->getModuleRelativeFilePath($properties, $relativePath);
    }

    public function getPluginImages($slug)
    {
        $properties = $this->getProperties($slug);
        $pluginImages = is_array($properties['image']) ? $properties['image'] : ($properties['image'] ? [$properties['image']] : []);
        $pluginImages = array_map(function($image) use ($properties) {
            if(strpos($image, 'http://') === 0)
                return $image;
            return call_user_func(\Config::get('plugins.asset_method'), $this->getPluginAsset($properties, $image));
        }, $pluginImages);
        return $pluginImages;
    }

    /*
     * Get plugin config storage key
     */
    public function getPluginConfigKey($slug)
    {
        return 'plugin.config.'.$slug;
    }

    /*
     * Get plugin translation storage key
     */
    public function getPluginLanguagesKey($slug)
    {
        return 'plugin.languages.'.$slug;
    }

    public function isConfigurable($slug){
        $properties = $this->getProperties($slug);
        $configOptions = $properties['config'];
        return (isset($configOptions['type']) && isset($configOptions['file']));
    }

    public function isTranslatable($slug){
        $properties = $this->getProperties($slug);
        return (!empty($properties['translation']));
    }

    public function getSettings($slug) {
        $configKey = $this->getPluginConfigKey($slug);
        return \MyConfig::get($configKey);
    }

    public function all()
    {
        $plugins = parent::all();
        $pluginObjects = collect();
        $pluginObjects = $plugins->each(function($plugin) use($pluginObjects) {
            $pluginObjects->push($this->getPlugin($plugin['slug']));
        });
        return $pluginObjects;
    }

    /**
     * Get path of a file in a plugin
     *
     * @param $slug
     *
     * @return string
     */
    public function getPluginFilePath($slug, $fileName)
    {
        return $this->repository->getModulePath($slug) . $fileName;
    }

    public function requirePluginFile($slug, $relativePath)
    {
        $filePath = $this->getPluginFilePath($slug, $relativePath);
        if(!\File::exists($filePath))
            return;
        require_once($filePath);
    }

}