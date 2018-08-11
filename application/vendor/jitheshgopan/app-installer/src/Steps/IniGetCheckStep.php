<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:36 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;
use \View;


class IniGetCheckStep extends AbstractStep{

    public function process($prevStep){

    }

    public function handler(){
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
        return true;
    }

    public function check($key, $equalsValue = null, $errorMessage = 'Error!'){
        $iniVal = ini_get($key);
        if($equalsValue !== null) {
            if($iniVal != $equalsValue){
                $this->errors[] = $errorMessage;
                return false;
            }
        } else if(!$iniVal) {
            $this->errors[] = $errorMessage;
            return false;
        }
        return true;
    }
}