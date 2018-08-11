<?php

/*
 * This file goes to 'application' folder in packaged app
 */
class RequirementChecker {
    private $errors = array();
    private $checks = array();
    const MIN_PHP_VERSION = '5.6.4';

    public function getEnv()
    {
        $env = basename(__DIR__) == "application" ? 'production' : 'local';
        return $env;
    }

    public function isLocal()
    {
        return $this->getEnv() == "local";
    }

    public function isProduction()
    {
        return $this->getEnv() == "production";
    }

    /*
     * Check if running as packaged app
     */
    public function isPackaged()
    {
        return $this->isProduction();
    }
    public function addCheck($name) {
        $this->checks[] = $name;
    }

    public function installPath($path = '')
    {
        if($this->isPackaged())
            $installPath = __DIR__ . '/..';
        else
            $installPath = __DIR__;
        return $installPath . ($path ? '/'.$path : $path);
    }

    public function publicPath($path = '')
    {
        if($this->isPackaged())
            $publicPath = $this->installPath('application/public');
        else
            $publicPath = $this->installPath('public');
        return $publicPath . ($path ? '/'.$path : $path);
    }

    public function check($name, $callback) {

        $this->addCheck($name);
        call_user_func($callback);
    }

    public function addError($title, $message = '')
    {
        $this->errors[] = [
            'title' => $title,
            'message' => $message
        ];
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getChecks()
    {
        return $this->checks;
    }

    public function run()
    {
        $this->check("Need PHP version <b> >=" . self::MIN_PHP_VERSION . '</b>', function() {
            if(!version_compare(phpversion(), self::MIN_PHP_VERSION, '>=')) {
                $this->addError('PHP version error', "Laravel requires a minimum PHP version of " . self::MIN_PHP_VERSION . '<br>' . "You current PHP version is " . phpversion() . " which is not supported");
            }
        });
        $this->check("Mcrypt Extension is required", function(){
            if(!extension_loaded('mcrypt')){
                $this->addError("MCrypt Extension not loaded", "Laravel requires MCrypt PHP Extension");
            }
        });

        $this->check("PDO_MYSQL Extension is required", function(){
            if(!extension_loaded('pdo_mysql')){
                $this->addError("pdo_mysql Extension not loaded", "SocioQuiz requires pdo_mysql PHP Extension for Database connections");
            }
        });

        $this->check("MbString Extension is required", function(){
            if(!extension_loaded('mbstring')){
                $this->addError("MbString Extension not loaded", "SocioQuiz requires MbString PHP Extension for Facebook login");
            }
        });

        $this->check("JSON module required", function(){
            if(!function_exists( 'json_encode' )) {
                $this->addError("PHP JSON not available", "PHP JSON should be enabled as we require json_encode and similar functions.");
            }
        });

        //GD extension check
        $this->check("GD Extension required", function () {
            $this->extensionCheck('gd');
        });

        //PDO extension
        $this->check("PDO Extension required", function () {
            $this->extensionCheck('pdo');
        });

        //CURL extension
        $this->check("CURL Extension required", function () {
            $this->extensionCheck('curl');
        });

        //openssl extension
        $this->check("OpenSSL Extension required", function () {
            $this->extensionCheck('openssl');
        });

        //Tokenizer extension
        $this->check("Tokenizer Extension required", function () {
            $this->extensionCheck('tokenizer');
        });

        //XML extension
        $this->check("XML Extension required", function () {
            $this->extensionCheck('xml');
        });

        //allow_url_fopen settings
        $this->check("Checking allow_url_fopen settings", function () {
            if(!$this->iniCheck('allow_url_fopen')) {
                $this->addError("'allow_url_fopen' must be enabled", "You may contact your hosting support or change it in php.ini(if you have access to it");
            }
        });

        $this->check("'storage' directory should be writable", function(){
            if($this->isPackaged()) {
                $storageDirName = 'application/storage';
            } else {
                $storageDirName = 'storage';
            }
            $storageFilePath = __DIR__ . '/storage';
            if(!is_writable($storageFilePath)) {
                $this->makeWritablePermissionError($storageFilePath, "'{$storageDirName}' directory should be writable");
            } else {
                //Storage dir is writable, check if its sub directories are
                foreach (glob($storageFilePath . '/*', GLOB_ONLYDIR) as $dir) {
                    if(!is_writable($dir)) {
                        $this->makeWritablePermissionError($dir, "Sub directories of '{$storageDirName}' directory should be writable");
                    }
                }
            }
        });

        $this->check("'content' directory should be writable", function(){
            if($this->isPackaged()) {
                $contentDir = $this->installPath('content');
            } else {
                $contentDir = $this->publicPath('content');
            }
            if(!is_writable($contentDir)) {
                $this->makeWritablePermissionError($contentDir, "'content' directory should be writable");
            } else {
                //Content dir is writable, check if its sub directories are
                foreach (glob($contentDir . '/*', GLOB_ONLYDIR) as $dir) {
                    if(!is_writable($dir)) {
                        $this->makeWritablePermissionError($dir, "Sub directories of 'content' directory should be writable");
                    }
                }
            }
        });

        if($this->isPackaged()) {
            $this->check("'config.php' file should be writable", function() {
                $configFile = $this->installPath('config.php');
                if (!is_writable($configFile)) {
                    $this->makeWritablePermissionError($configFile, "'config.php' file should be writable", true);
                }
            });
        }

        $this->check("'.htaccess' file should be uploaded", function() {
            if($this->isPackaged()) {
                $htaccessPath = $this->installPath('.htaccess');
            } else {
                $htaccessPath = $this->publicPath('.htaccess');
            }
            if (!file_exists($htaccessPath)) {
                $this->addError("'.htaccess' file not uploaded", "The .htaccess file is missing in your installation. It is in the script folder which you unzipped from the SocioQuiz package. It might be a hidden file if you are using Mac or Linux as the filename starts with a dot. You might have to change your view settings to see it.");
            }
        });
    }

    public function makeWritablePermissionError($directory, $title = '', $isFile = false)
    {
        $type = $isFile ? 'file' : 'directory';
        if($title == '')
            $title = "'{$directory}' {$type} should be writable";
        $permission = 755;
        $this->addError($title, "<p>Make '" . $directory . "' {$type} writable by assigning appropriate permissions.<br>( For eg, via shell command: <code>chmod -R {$permission} " . $directory . "</code> ).</p><b>NOTE: </b> 755 is a common writable permission number with popular hosts like Hostgator. But, yours may be different! <b>Contact your hosting provider to get the correct permission number to use.</b>");
    }

    public function extensionCheck($extension){
        if(!extension_loaded($extension)) {
            $this->addError("Extension '" . $extension . "' not loaded.", "SocioQuiz requires '{$extension}' PHP Extension");
            return false;
        }
        return true;
    }

    public function iniCheck($key, $expectedValue = null){
        $iniVal = ini_get($key);
        if($expectedValue !== null) {
            if($iniVal != $expectedValue){
                return false;
            }
        } else if(!$iniVal) {
            return false;
        }
        return true;
    }

}