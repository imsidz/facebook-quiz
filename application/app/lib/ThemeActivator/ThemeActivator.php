<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 16/01/16
 * Time: 5:22 AM
 */

namespace ThemeActivator;


use MyConfigManager\ConfigManager;

class ThemeActivator {

    const ACTIVE_THEME_CONFIG_KEY = 'theme.activeTheme';

    function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function getActiveTheme()
    {
        $activeTheme = \MyConfig::get(self::ACTIVE_THEME_CONFIG_KEY, []);
        if(!$activeTheme)
            return false;
        if(is_array($activeTheme))
            $activeTheme = @$activeTheme[0];
        return $activeTheme;
    }

    public function isThemeActive($theme)
    {
        return ($theme == self::getActiveTheme());
    }

    public function activateTheme($theme) {
        $this->configManager->saveConfig(self::ACTIVE_THEME_CONFIG_KEY, $theme);
    }
}