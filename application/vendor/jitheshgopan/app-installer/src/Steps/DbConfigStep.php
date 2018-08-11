<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:36 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


use Illuminate\Support\Facades\File;
use Jitheshgopan\AppInstaller\Installer;
use \View;

class DbConfigStep extends AbstractStep {

    public function __construct($name, $options) {
        parent::__construct($name, $options);
        $this->setOptions($options);
        //Set blocking to true
        $this->blocking = true;
        //$this->mute();
    }
    public function process($prevStep){

    }

    public function handler(){
        $sourceStep = $this->getSourceStep();
        $newDbConfigData = $sourceStep ? $sourceStep->getData() : $this->getData();
        $dbConfigProcessor = \Config::get('app-installer.processDBConfig');
        try{
            if(is_callable($dbConfigProcessor)) {
                if(!call_user_func($dbConfigProcessor, $newDbConfigData)) {
                    $this->errors[] = "Database connection process returned false.";
                }
            }
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
        return true;
    }

    public function getFollowingSteps(){

    }

    public function getPrecedingSteps(){
        $options = $this->getOptions();
        $inputData = null;
        if(isset($options['inputData'])) {
            $inputData = $options['inputData'];
        }
        //Step to get input
        $getInputStep = new InputStep(Installer::trans("installer.inputDbConfigStepName"),[
            'type' => "Input",
            'fields' => [
                'host'      => 'Host',
                'database'  => 'Database',
                'username'  => 'Username',
                'password'  => [
                    'title' => 'Password',
                    'type'  => 'string'
                ]
            ],
            'data'          => $inputData,
            'blocking'      => true
        ]);

        //Step to test DB connection
        $checkConnectionStep = new CheckDbConnectionStep(Installer::trans("installer.checkDbConfigStepName"),[
            'blocking'      => true
        ]);
        $checkConnectionStep->setSourceStep($getInputStep);
        $this->setSourceStep($getInputStep);
        return array(
            $getInputStep,
            $checkConnectionStep
        );
    }

}