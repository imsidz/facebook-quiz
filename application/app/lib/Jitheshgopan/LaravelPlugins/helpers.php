<?php

if(!function_exists('get_plugin_settings')) {
    function get_plugin_settings($slug, $key) {
        $plugins = \App::make('plugins');
        $settings = $plugins->getSettings($slug);
        $settings = collect($settings);
        return $settings->get($key, null);
    }
}