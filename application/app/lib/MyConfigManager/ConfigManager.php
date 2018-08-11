<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 13/11/15
 * Time: 11:42 PM
 */

namespace MyConfigManager;


class ConfigManager {
    public function loadConfig() {
        //Loading config
        $config = [];
        try {
            $configRows = \SiteConfig::all();
        } catch (Exception $e) {
            //Config cant be read! DB cant be accessed or Installation not completed
            //return Response::configurationError("Please complete installation and check again.", "Installation incomplete!");
            return false;
        }
        foreach($configRows as $row) {
            $config[$row->name] = (array) json_decode($row->value, true);
        }
        \MyConfig::setConfig($config);
        \Event::fire('config:loaded');
    }

    public function saveConfig($key, $config) {
        $newConfigRow = \SiteConfig::findOrNew($key);
        $newConfigRow->name = $key;
        $newConfigRow->value = json_encode($config);
        $newConfigRow->save();
        \MyConfig::set($key, $config);
    }
}