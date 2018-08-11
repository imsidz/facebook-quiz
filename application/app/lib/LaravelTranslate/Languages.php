<?php
namespace LaravelTranslate;
use \Config as Config;
use \App as App;
use \View as View;
use \Input as Input;

class Languages {
    private $languages;
    private $defaultLanguage = 'en';
    private $defaultDirection = 'ltr';
    private $defaultFbCode = 'en_US';

    function __construct()
    {
    }

    public function initialize($languages, $defaultLanguage = null){
        if(!$languages){
            throw new \InvalidArgumentException("Languages array is invalid or empty");
        }
        $this->languages = $languages;
        if($defaultLanguage)
            $this->setDefaultLanguage($defaultLanguage);
        $formFieldStringsSaver = new FormFieldStringsSaver();
        $formFieldStringsSaver->activate();
        return $this;
    }

    public function setDefaultLanguage($defaultLanguage){
        $this->defaultLanguage = $defaultLanguage;
        Config::set('defaultLangauge', $defaultLanguage);
    }

    public function getDefaultLanguage(){
        return $this->defaultLanguage;
    }

    public function activateLanguage($languageCode){
        App::setLocale($languageCode);
        $this->exposeToView();
        \Event::fire('language:activated', $languageCode);
    }

    public function getActivateLanguageCode(){
        return App::getLocale();
    }

    public function getLanguage($languageCode) {
        $languages = $this->languages;
        $language = array_where($languages, function($value, $key) use ($languageCode) {
            return $value['code'] == $languageCode;
        });
        return current($language);
    }
    public function getLanguageStrings($languageCode) {
        $language = $this->getLanguage($languageCode);
        if($language) {
            $languageStrings = $language['strings'];
        } else {
            $languageStrings = [];
        }
        return $languageStrings;
    }

    public function getLanguageDirection($languageCode) {
        $language = $this->getLanguage($languageCode);
        if(is_array($language) && !empty($language['direction'])) {
            return $language['direction'];
        } else {
            return $this->defaultDirection;
        }
    }

    public function getLanguageFbCode($languageCode){
        $language = $this->getLanguage($languageCode);
        if(is_array($language) && !empty($language['fb_code'])) {
            return $language['fb_code'];
        } else {
            return $this->defaultFbCode;
        }
    }

    public function exposeToView(){

        //Load current language strings to config and view
        $currentLang = App::getLocale();
        $languageStrings = $this->getLanguageStrings($currentLang);
        $languageDirection = $this->getLanguageDirection($currentLang);
        $languageFbCode = $this->getLanguageFbCode($currentLang);
        $defaultLanguageStrings = $this->getLanguageStrings(Config::get('defaultLangauge'));
        Config::set('languageStrings', $languageStrings);
        View::share('languageStrings', $languageStrings);

        $cookieLanguageDirection = @$_COOKIE['language_direction'];
        if($cookieLanguageDirection)
            $languageDirection  =   $cookieLanguageDirection;
        if(Input::get('rtl-mode'))
            $languageDirection  = 'rtl';

        Config::set('languageDirection', $languageDirection);
        View::share('languageDirection', $languageDirection);

        Config::set('languageFbCode', $languageFbCode);
        View::share('languageFbCode', $languageFbCode);

        //Load default languages too to config and view
        Config::set('defaultLanguageStrings', $defaultLanguageStrings);
        View::share('defaultLanguageStrings', $defaultLanguageStrings);

        $languagesMap = [];
        if(count($this->languages)) {
            foreach ($this->languages as $language) {
                $languagesMap[$language['code']] = $language['name'];
            }
        }
        \Config::set('languages', $this->languages);
        \Config::set('languagesMap', $languagesMap);
        \View::share('languages', $this->languages);
        \View::share('languagesMap', $languagesMap);
    }

    public function isLanguageAvailable($languageCode) {
        return !!$this->getLanguage($languageCode);
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param mixed $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    public function getUserNativeLanguage() {
        $browser_lang = substr(\Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        return $browser_lang;
    }

    public function isMyNativeLanguage($languageCode)
    {
        return $languageCode == $this->getUserNativeLanguage();
    }

    public function isActiveLanguageMyNative()
    {
        return $this->getActivateLanguageCode() == $this->getUserNativeLanguage();
    }
}