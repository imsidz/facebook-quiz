<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 11/09/15
 * Time: 4:09 PM
 */

namespace LaravelTranslate;


class FormFieldStringsSaver {

    public function __construct() {

    }

    public function activate() {
        $formFieldsSaver = $this;
        \SiteConfig::saved(function($config) use($formFieldsSaver) {
            if($config->name == 'languages') {
                $formFieldsSaver->saveStringsFromConfigObject($config);
            }
        });
    }

    public function saveStringsFromConfigObject($config) {
        $languageData = json_decode($config->value, true);
        try {
            $this->saveStringsOfAllLanguages($languageData);
        } catch(\PermissionDeniedException $e) {
            \App::abort(400, $e->getMessage());
        }
    }

    public function saveStringsOfAllLanguages($languageData) {
        if(empty($languageData['languages']))
            return;
        foreach($languageData['languages'] as $language) {
            $formFields = $this->getFormFieldStringsFromLanguage($language);
            if(!$formFields)
                continue;
            $this->saveStringsOfLanguage($language['code'], $formFields);
        }
    }

    public function saveStringsOfLanguage($language, $formFieldStrings) {
        if(!$formFieldStrings) {
            return;
        }
        $langDir = app('path.lang');
        $validationStringsFile = $langDir . '/' . \App::getLocale() . '/validation.php';
        if(file_exists($validationStringsFile)) {
            $validateStings = require($validationStringsFile);
            if(empty($validateStings['attributes']))
                $validateStings['attributes'] = [];
            $validateStings['attributes'] = array_merge($validateStings['attributes'], $formFieldStrings);
            $newValidationFileContent = "<?php \n return " . var_export($validateStings, true) .';';
            //dd($newValidationFileContent );
            try {
                if(file_put_contents($validationStringsFile, $newValidationFileContent)) {
                    return true;
                } else {
                    throw new \Exception();
                }
            } catch(\Exception $e) {
                throw new \PermissionDeniedException('Error saving languages file. Permission denied. "' . $langDir . '" should be writable."');
            }
        }
    }

    public function getFormFieldStringsFromLanguage($languageData) {
        if(empty($languageData['formFields']))
            return false;
        return $languageData['formFields'];
    }
}