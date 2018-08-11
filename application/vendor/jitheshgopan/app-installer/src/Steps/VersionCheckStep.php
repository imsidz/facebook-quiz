<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:36 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


use Jitheshgopan\AppInstaller\Exceptions\InstallerException;
use Jitheshgopan\AppInstaller\Installer;
use JsonSchema\Exception\InvalidArgumentException;
use \View;

class VersionCheckStep extends AbstractStep{

    private $checks = array();
    private $knownKeys = [
        'php'
    ];
    public function process($prevStep){

    }
    public function handler(){
        $this->runChecks();
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
        return true;
    }

    public function checkVersion($key, $version, $comparisonOperator = '='){
        /*if(!$this->isKnownKey($key)){
            throw new InstallerException("Checking version of : '" . $key . "' is currently not supported.");
        }*/
        $this->checks[] = array(
            'key' => $key,
            'version' => $version,
            'comparisonOperator' => $comparisonOperator
        );
    }

    public function isKnownKey($key){
        return(in_array($key, $this->knownKeys));
    }

    public function getVersion($key){
        switch($key){
            case 'php' :
                return phpversion();
            default :
                //Is a php extension
                return phpversion($key);
        }
    }

    public function getFailedMessage($key, $version,  $operator, $availableVersion) {
        $failedMsg = "'" . $key . "' version should be ";
        switch($operator){
            case "=" :
                $failedMsg .= "exactly " . $version;
                break;
            case ">" :
                $failedMsg .= "greater than " . $version;
                break;
            case ">=" :
                $failedMsg .= "greater than or equal to " . $version;
                break;
            case "<" :
                $failedMsg .= "less than " . $version;
                break;
            case "<=" :
                $failedMsg .= "less than or equal to " . $version;
                break;
            case "!=" :
                $failedMsg .= "not be equal to " . $version;
                break;
            default :
                throw new InstallerException("Invalid version compare operator");
        }

        $failedMsg = [
            'title' => $failedMsg
        ];
        $failedMsg['message'] = 'Current version is: ' . $availableVersion;
        return $failedMsg;
    }

    public function runChecks() {
        foreach ($this->checks as $check) {
            $key = $check['key'];
            $version = $check['version'];
            $comparisonOperator = $check['comparisonOperator'];
            $availableVersion = $this->getVersion($key);
            if($availableVersion === false) {
                throw new InstallerException("Error checking version of : '" . $key . "'. It is currently not supported.");
            }
            if(!version_compare($availableVersion, $version, $comparisonOperator)){
                $this->errors[$key] = $this->getFailedMessage($key, $version, $comparisonOperator, $availableVersion);
            }
        }
    }
}