<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:37 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


class WritableCheckStep extends AbstractStep {

    private $checks = array();
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

    public function checkWritable($dirPath){
        $this->checks[] = $dirPath;
        return true;
    }

    public function runChecks(){
        foreach($this->checks as $dirPath) {
            if(!is_writable($dirPath)) {
                $this->errors[] = "Directory  '" . $dirPath . "' is not writable. Please make it writable(including its sub-directories, if any)";
                return false;
            }
        }
    }
}