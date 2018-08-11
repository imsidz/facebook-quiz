<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:33 AM
 */

namespace Jitheshgopan\AppInstaller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Jitheshgopan\AppInstaller\Exceptions\InvalidStageTypeException;
use Jitheshgopan\AppInstaller\Exceptions\InstallerException;
use Jitheshgopan\AppInstaller\Stages;
use Jitheshgopan\AppInstaller\Steps;


class Installer {

    private $stages;
    private $dataStore;
    public function __construct() {
        $this->stages = array();
        $this->dataStore = new DataStore();
    }

    /*
     * Add a stage
     * @param $name Name of the stage
     * @param $options Options
     * @return Stage
     * @throws Jitheshgopan\AppInstaller\Exceptions\InvalidStageTypeException
     */
    public function addStage($name, $options = array()) {
        $stageType = !empty($options['type']) ? $options['type'] : 'Stage';
        $stage = Stages\AbstractStage::createStage($stageType, $name, $options);
        $stage->setId(count($this->stages));
        $stage->restoreStoredData();
        //$stage = $this->restoreStageData($stage);
        $this->stages[] = $stage;
        return $stage;
    }

    /*
     * Add multiple stages at once
     * @param $stagesData The stages array to be added
     */
    public function addStages($stagesData = array()){
        foreach ($stagesData as $stage) {
            $stageName = !empty($stage['name']) ? $stage['name'] : trans("app-installer::stages.untitled");
            $this->addStage($stageName, $stage);
        }
    }

    /*
     * Remove a stage
     * @param $stage The stage to be removed
     * @return true/false - true if remove succeeded
     */
    public function removeStage($stage){
        $key = array_search($stage, $this->stages);
        if($key) {
            unset($this->stages[$key]);
            return true;
        }
        return false;
    }

    /*
     * Get stages
     * @return Stages (Array)
     */
    public function getStages(){
        return $this->stages;
    }

    /*
     * Set stages
     * @param $stages Stages to be added
     */
    public function setStages($stages){
        return $this->stages = $stages;
    }

    /*
     * Helper function to add Stage of type 'FinalStage'
     * @param $options Options
     * @return Stage finalStage
     */
    public function finalStage($name = "Finished", $options = array()){
        $options['type'] = 'FinalStage';
        return $this->addStage($name, $options);
    }

    /*
     * Get stage at index
     * @param $index
     * return Stage/false - false if no stage at given index
     */
    public function getStageAtIndex($index){
        return !empty($this->stages[$index]) ? $this->stages[$index] : false;
    }

    /*
     * Get stage at position (1,2,3, etc)
     * @param $position
     * return Stage/false - false if no stage at given position
     */
    public function getStageAtPosition($position){
        return $this->getStageAtIndex($position - 1);
    }

    /*
     * Get current stage
     * @return Current stage (Stage)
     */
    public function getCurrentStage(){
        $currentStagePosition = (int) Input::get('stage', 1);
        return $this->getStageAtPosition($currentStagePosition);
    }

    /*
     * Run the installer
     * @return HTML (string) of the rendered form
     */
    public function run(){
        $currentStage = $this->getCurrentStage();

        if($currentStage) {
            View::share('currentStage', $currentStage);
        }
        if(Input::ajax()){
            //Is ajax
            if(Input::get('action') == 'saveStepData') {
                $stepIndex = Input::get('step', false);
                $data = Input::get('data', array());
                if($stepIndex === false){
                    //Step index not passed
                    throw new InstallerException("Step id not passed");
                }
                $step = $currentStage->getStepAtIndex($stepIndex);
                $prevStep = $currentStage->getStepAtIndex($stepIndex - 1);
                $step->setData($data);
                $step->run($prevStep);
                $currentStage->storeStepData($step);
                return;
            }
        }

        if($currentStage) {
            View::share('currentStageContent', $currentStage->run());
            $this->storeStageData($currentStage);
        } else {
            return "Invalid stage";
        }
        View::share('stages', $this->getStages());
        return View::make(INSTALLER_NAMESPACE . '::installer');
    }

    /*
     * Easy asset path retriever for package specific assets
     */
    public static function asset($path){
        return asset('packages/' . INSTALLER_VENDOR_PATH . '/' . $path);
    }

    /*
     * Easy trans string retriever for package specific translation
     * @param $key The string key
     */
    public static function trans($key){
        return trans(INSTALLER_NAMESPACE . '::' . $key);
    }

    /*
     * Easy view retriever for package specific views
     * @param $view name
     */
    public static function view($view){
        return INSTALLER_NAMESPACE . '::' . $view;
    }

    /*
     * Easy Config retriever for package specific config
     * @param $key config key
     */
    public static function config($key){
        return Config::get(INSTALLER_NAMESPACE . '.' . $key);
    }

    /*
     * Get Installer route
     */
    public static function route(){
        return route(self::config('routeName'));
    }

    public function getStoredStageData($stage){
        return $this->dataStore->read($this->getStageDataStoreKey($stage));
    }

    public function storeStageData($stageToStore = null) {
        if(!$stageToStore) {
            $stages = $this->getStages();
        } else {
            $stages = array($stageToStore);
        }
        foreach($stages as $stage) {
            $stage->storeData();
            $stage->storeStepData();
        }
    }

    public function getStageDataStoreKey($stage){
        if(!$stage->getId() && $stage->getId() !== 0){
            //Stage id not set
            throw new \Exception("Stage id not set! cant get key to store stage data");
        }
        return 'stage-' . $stage->getId();
    }

    public function restoreStageData($stage){
        $storedStageData = $this->getStoredStageData($stage);
        if($storedStageData) {
            $stage = unserialize($storedStageData);
        }
        return $stage;
    }

    public function addFinishStage($name = "Installation finished", $options){
        return $this->addStage($name, array_merge($options, [
            'type' => 'Finish'
        ]));
    }
}