<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 16/01/16
 * Time: 4:17 AM
 */

namespace Themes;
use MyConfig;

use Illuminate\Support\Collection;

class Themes extends \Caffeinated\Themes\Themes{

    public function loadThemeTranslations()
    {
        \Event::listen('language:activated', function($languageCode) {
            $theme = $this->getActive();
            $languages = \Languages::getLanguages();
            $languagesKey = $this->getThemeLanguagesKey($theme);
            $themeLanguageData = MyConfig::get($languagesKey);
            $properties = $this->getProperties($theme);
            if(!$themeLanguageData || !is_array($themeLanguageData))
                return;
            foreach ($languages as $key => $language) {
                $themeStrings = array_map(function($l) use($language){
                    if($l['code'] == $language['code'])
                        return $l['strings'];
                }, $themeLanguageData);
                if(count($themeStrings)) {
                    $languages[$key]['strings']['themes'] = [];
                    $languages[$key]['strings']['themes'][$theme] = current($themeStrings);
                }
            }
            \Languages::setLanguages($languages);
            \Languages::exposeToView($languages);
        });
    }
    public function getProperties($theme)
    {
        return new Collection($this->getJsonContents($theme));
    }

    public function getThemeAsset($theme, $relativePath){
        return $this->getThemeRelativePath($theme).$relativePath;
    }

    public function getThemeRelativePath($theme)
    {
        return $this->getRelativePath()."/{$theme}/";
    }

    public function getRelativePath()
    {
        return $this->config->get('themes.paths.base');
    }

    public function getThemeImages($theme)
    {
        $properties = $this->getProperties($theme);
        $themeImages = is_array($properties->get('image')) ? $properties->get('image') : ($properties->get('image') ? [$properties->get('image')] : []);
        $themeImages = array_map(function($image) use ($theme) {
            if(strpos($image, 'http://') === 0)
                return $image;
            return call_user_func(\Config::get('themes.asset_method'), $this->getThemeAsset($theme, $image));
        }, $themeImages);
        return $themeImages;
    }
    
    /*
     * Get theme config storage key
     */
    public function getThemeConfigKey($theme)
    {
        return 'theme.config.'.$theme;
    }

    /*
     * Get theme translation storage key
     */
    public function getThemeLanguagesKey($theme)
    {
        return 'theme.languages.'.$theme;
    }

    public function isConfigurable($theme){
        $properties = $this->getProperties($theme);
        $configOptions = $properties->get('config');
        return (isset($configOptions['type']) && isset($configOptions['file']));
    }

    public function isTranslatable($theme){
        $properties = $this->getProperties($theme);
        $translationsFile = $properties->get('translation');
        return (!empty($translationsFile));
    }

    public function getSettings($theme) {
        $configKey = $this->getThemeConfigKey($theme);
        return \MyConfig::get($configKey);
    }

    public function getTheme($slug)
    {
        $theme = new Theme($slug, $this->getProperties($slug), $this);
        return $theme;
    }
}