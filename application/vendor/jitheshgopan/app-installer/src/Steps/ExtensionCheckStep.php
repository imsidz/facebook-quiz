<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:36 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;
use \View;


class ExtensionCheckStep extends AbstractStep{

    public function process($prevStep){

    }

    public function handler(){
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
        return true;
    }

    public function check($extension){
        if(!extension_loaded($extension)) {
            $this->errors[] = "Extension '" . $extension . "' not loaded";
            return false;
        }
        return true;
    }
}