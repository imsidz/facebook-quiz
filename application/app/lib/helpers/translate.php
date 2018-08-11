<?php

// Custom Translate function that uses custom language data from database
function __($key){
    $languageStrings = Config::get('languageStrings');
    $defaultLanguageStrings = Config::get('defaultLanguageStrings');
    if(!empty($languageStrings[$key])){
        return $languageStrings[$key];
    } else if(!empty($defaultLanguageStrings[$key])){
        return $defaultLanguageStrings[$key];
    } else {
        return $key;
    }
}
