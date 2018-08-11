<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:33 AM
 */

namespace Jitheshgopan\AppInstaller\Stages;


use Illuminate\Support\Facades\View;

class Stage extends AbstractStage{

    public function process(){
        $this->runSteps();
        View::share('steps', $this->getSteps());
        View::share('currentStage', $this);
        return View::make(INSTALLER_NAMESPACE . '::stage')->render();
    }

    /*
     * Easy functions for creating specific steps
     */


    public function addDbConfigStep($dbType = 'mysql',$options = array()){
        if(!is_array($options)){
            throw new \InvalidArgumentException("Invalid Options! Should be an array");
        }
        $options = array_merge($options, [
            'type' => "DbConfig"
        ]);
        return $this->addStep("Database config", $options);
    }

    public function addPhpVersionCheckStep($version, $comparisonOperator = '>=') {
        $options = [
            'type' => "PhpVersionCheck"
        ];
        $step = $this->addStep("Checking php version", $options);
        $step->check($version, $comparisonOperator);
        return $step;
    }
}