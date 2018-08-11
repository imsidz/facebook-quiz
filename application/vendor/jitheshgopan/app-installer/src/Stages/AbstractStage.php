<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:32 AM
 */

namespace Jitheshgopan\AppInstaller\Stages;
use Jitheshgopan\AppInstaller\DataStore;
use Jitheshgopan\AppInstaller\Steps\AbstractStep;


use Jitheshgopan\AppInstaller\Exceptions\InvalidStepTypeException;

abstract class AbstractStage {

    protected $id;
    protected $steps;
    protected $name;
    protected $stepOutputs = array();
    protected $dataStore;
    protected $currentStep;
    protected $isComplete;
    protected $hasError;
    protected $options;

    /*
     * The abstract process method
     */
    public abstract function process();

    public function __sleep(){
        return $this->getKeysToPersist();
    }

    /*
     * The wakeup magic method - wake steps up when run
     */
    public function __wakeup($data = array()){
        $this->dataStore = new DataStore();
    }

    public function __construct($name, $options= array()){
        $this->setName($name);
        $this->setOptions($options);
        $this->steps = array();
        $this->dataStore = new DataStore();
    }

    /*
     * The preRun hook function
     */
    public function preRun(){
        $this->hasRun = true;
    }

    /*
     * The postRun hook function
     */
    public function postRun(){

    }

    /*
     * Run the stage
     */
    public function run(){
        $this->preRun();
        $result = $this->process();
        $this->postRun();
        return $result;
    }


    /*
     * Set Stage Id
     * @param $id the id to be set
     * @return $this
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /*
     * Get Stage Id
     * @return the id
     */
    public function getId(){
        return($this->id);
    }

    /*
     * Get Options
     * @return $options
     */
    public function getOptions(){
        return $this->options;
    }

    /*
     * Set options of the step
     * @param $options The options of the stage
     */
    public function setOptions($options){
        return $this->options = $options;
    }

    /*
     * Get a particular Option
     * @return option value
     * @throws InvalidArgumentException
     */
    public function getOption($key){
        $options = $this->getOptions();
        if(empty($options[$key])){
            throw new \InvalidArgumentException("Option '" . $key . "' not found in options set");
        }
        return $options[$key];
    }

    /*
     * CHeck if the stage has a particular Option
     * @return boolean
     */
    public function hasOption($key){
        $options = $this->getOptions();
        if(empty($options[$key])){
            return false;
        }
        return true;
    }

    /*
     * Set a single option
     * @param $options The options of the stage
     */
    public function setOption($key, $value){
        return $this->options[$key] = $value;
    }

    /*
     * Options Helper method for getting all/specific keys and to set value of a particular key
     * @param $key the option key
     * @param $value the value to be assigned to the option with specified key
     */
    public function options($key = null, $value = null){
        if(!$key)
            return $this->getOptions();
        else {
            if($value === null){
                return $this->getOption($key);
            } else {
                $this->setOption($key, $value);
            }
        }
    }

    /*
     * Check if a stage type is valid
     * @param $stageTypeName stage type
     * return boolean
     */
    public static function isValidStageType($stageTypeName){
        $stageClassName = self::getStageClassName($stageTypeName);
        if(!class_exists($stageClassName)){
            return false;
        }
        return true;
    }

    /*
     * Get stage class name form stage type
     * @param $stageTypeName stage type
     * @return string stage class name
     */
    public static function getStageClassName($stageTypeName){
        $stageClassName = ($stageTypeName == "Stage") ? "Stage" : $stageTypeName . "Stage";
        $stageClassName = 'Jitheshgopan\AppInstaller\Stages\\' . $stageClassName;
        return $stageClassName;
    }

    /*
     * Create stage of a type
     * @param $stageType Stage type name
     * @param $name Stage name
     * @param options stage options
     * @return Stage of required type
     */
    public static function createStage($stageType, $name,  $options = array()){
        if(!self::isvalidStageType($stageType)){
            throw new InvalidStageTypeException("Stage of type '" . $stageType . "' doesn't exist");
        }
        $stageClassName = self::getStageClassName($stageType);
        return new $stageClassName($name, $options);
    }

    /*
     * Get name
     */
    public function getName(){
        return $this->name;
    }

    /*
     * Set name of the stage
     * @param $name The name of the stage
     */
    public function setName($name){
        return $this->name = $name;
    }

    /*
     * Add a step
     * @param $name Name of the step
     * @param $options Options
     * @return Step
     * @throws Jitheshgopan\AppInstaller\Exceptions\InvalidStepTypeException
     */
    public function addStep($name, $options = array()){
        $stepType = !empty($options['type']) ? $options['type'] : 'Step';
        $step = AbstractStep::createStep($stepType, $name, $options);

        //Get the steps that follows this step which are automatically generated by this step
        $precedingSteps = $step->getPrecedingSteps();
        $this->appendSteps($precedingSteps);

        $this->appendStep($step);

        //Get the steps that follows this step which are automatically generated by this step
        $followSteps = $step->getFollowingSteps();
        $this->appendSteps($followSteps);
        return $step;
    }

    /*
     * Add multiple steps at once
     * @param $stepsData The steps array to be added
     * @throws InvalidArgumentException
     */
    public function addSteps($stepsData = array()){
        foreach ($stepsData as $step) {
            $stepName = !empty($step['name']) ? $step['name'] : trans("app-installer::steps.untitled");
            if(empty($step['handler'])){
                throw new InvalidArgumentException("Step 'handler' not specified for step: '" . $stepName . "'.");
            }
            $this->addStep($stepName, $step['handler'], $step);
        }
    }

    /*
     * Append an array of steps
     * @param $steps array of steps
     * @return $this
     */
    public function appendSteps($steps = array()){
        if(!$steps)
            return;
        foreach($steps as $step){
            $this->appendStep($step);
        }
        return $this;
    }

    /*
     * Append a step
     * @param $step Step to be appended
     * @return $this
     */
    public function appendStep($step){

        //The key for the next step
        $stepId = count($this->steps);
        $step->setId($stepId);
        $step->setStage($this);
        $step = $this->restoreStepData($step);
        $this->steps[] = $step;
        return $this;
    }

    /*
     * Remove a step
     * @param $step The step to be removed
     * @return true/false - true if remove succeeded
     */
    public function removeStep($step){
        $key = array_search($step, $this->steps);
        if($key) {
            unset($this->steps[$key]);
            return true;
        }
        return false;
    }

    /*
     * Get step
     * @return Step (Array)
     */
    public function getSteps(){
        return $this->steps;
    }

    /*
     * Set steps
     * @param $steps Steps to be added
     */
    public function setSteps($steps){
        return $this->steps = $steps;
    }

    /*
     * TODO
     * Check if stage is active
     */
    public function isActive(){
        return false;
    }

    /*
     * TODO
     * Check if stage is complete
     */
    public function markIfComplete(){
        $steps = $this->getSteps();
        $passed = true;
        foreach ($steps as $step) {
            if(!$step->hasPassed()) {
                $passed = false;
            }
        }
        if($this->hasError()){
            $this->hasError = true;
        }
        $this->isComplete = $passed;
    }

    /*
     * TODO
     * Check if stage is complete
     */
    public function isComplete(){
        return $this->isComplete;
    }

    public function hasError(){
        return $this->hasError;

        /*$steps = $this->getSteps();
        $hasErrors = false;
        foreach ($steps as $step) {
            if($step->hasError()) {
                $hasErrors = true;
            }
        }
        return $hasErrors;*/
    }

    /*
     * Get step at index
     * @param $index
     * return Step/false - false if no step at given index
     */
    public function getStepAtIndex($index){
        return !empty($this->steps[$index]) ? $this->steps[$index] : false;
    }

    /*
     * Get step at position (1,2,3, etc)
     * @param $position
     * return Step/false - false if no step at given position
     */
    public function getStepAtPosition($position){
        return $this->getStepAtIndex($position - 1);
    }

    /*
     * Get step with the given key
     * @param $key the key to search the step for
     * @return Step/false
     */
    public function getStepByKey($key){
        $steps = $this->getSteps();
        foreach ($steps as $step) {
            if($step->getKey() == $key) {
                return $step;
            }
        }
        return false;
    }
    /*
     * Run steps
     */
    public function runSteps($skipAlreadyRun = false){
        $steps = $this->getSteps();
        $prevStep = null;
        $startFromStepIndex = 0;
        if($skipAlreadyRun) {
            foreach ($steps as $index => $step) {
                if(!$step->hasRun()){
                    $startFromStepIndex = $index;
                    break;
                }
                $prevStep = $step;
            }
        }
        $startFromStep = $this->getStepAtIndex($startFromStepIndex);
        if($prevStep && (!$prevStep->hasPassed() && $prevStep->isBlocking())){
            //If previous step failed and is of type blocking
            return;
        }
        $this->runStep($startFromStep, $prevStep, true);
        //Run complete check to mark if complete
        $this->markIfComplete();
    }

    /*
     * Run a step
     */
    public function runStep($step, $prevStep = null, $runNext = false){
        //dd($step);
        $this->setCurrentStep($step);
        $stepResult = $step->run($prevStep);
        if($runNext) {
            $index = array_search($step, $this->getSteps());
            $this->onStepRun($step, $index, $runNext);
        }
        return $stepResult;
    }

    public function onStepRun($step, $index, $runNextStep = false){
        if(!$step->hasPassed() && $step->isBlocking()){
            //If step failed and is of type blocking
            return;
        }
        $nextStep = $this->getStepAtIndex($index + 1);
        if($nextStep && $runNextStep){
            $this->runStep($nextStep, $step, $runNextStep);
        }
    }

    /*
     * get the current step
     * @retun Step/null
     */
    public function getCurrentStep(){
        return $this->currentStep;
    }

    /*
     * Set a step as current step
     */
    public function setCurrentStep($step){
        return $this->currentStep = $step;
    }

    /*
     * Check if a step is teh current step
     */
    public function isCurrentStep($step){
        return($step == $this->getCurrentStep());
    }

    public function getStoredStepData($step){
        $storeKey = $this->getStepDataStoreKey($step);
        return $this->dataStore->read($storeKey);
    }

    public function storeStepData($stepToStore = null) {
        if(!$stepToStore) {
            $steps = $this->getSteps();
        } else {
            $steps = array($stepToStore);
        }
        foreach($steps as $step) {
            $this->dataStore->store($this->getStepDataStoreKey($step), serialize($step));
        }
    }

    public function getStepDataStoreKey($step){
        if(!$this->getId() && $this->getId() !== 0){
            //Stage id not set
            throw new \Exception("Stage id not set! cant get key to store step data");
        }

        if(!$step->getId() && $step->getId() !== 0){
            //Step id not set
            throw new \Exception("Step id not set! cant get key to store step data");
        }
        return 'stage-' . $this->getId() . '-step-' . $step->getId();
    }

    public function restoreStepData($step){
        $storedStepData = $this->getStoredStepData($step);
        if($storedStepData) {
            $step->restoreFromSerializedData($storedStepData);
        }
        return $step;
    }

    /*
     * Get stage serial number
     */
    public function getStageNumber(){
        return($this->getId() + 1);
    }

    /*
     * Get previous stage serial number
     */
    public function getPreviousStageNumber(){
        //return index = previous serial number
        return($this->getId());
    }

    /*
     * Get next stage serial number
     */
    public function getNextStageNumber(){
        return($this->getStageNumber() + 1);
    }

    public function restoreStoredData() {
        $serializedData = $this->getStoredData();
        if(!$serializedData) {
            return;
        }
        $rebornStage = unserialize($serializedData);
        if(!$rebornStage){
            return;
        }
        $preservedKeys = $this->getKeysToPersist();
        foreach($preservedKeys as $key){
            $this->$key = $rebornStage->$key;
        }
    }

    public function getStoredData(){
        $storeKey = $this->getDataStoreKey();
        return $this->dataStore->read($storeKey);
    }

    public function storeData() {
        $this->dataStore->store($this->getDataStoreKey(), serialize($this));
    }

    public function getDataStoreKey(){
        if(!$this->getId() && $this->getId() !== 0){
            //Stage id not set
            throw new \Exception("Stage id not set! cant get key to store step data");
        }

        return 'stage-' . $this->getId();
    }

    public function getKeysToPersist() {
        $keysToPersist = [
            'id',
            'name',
            'isComplete',
            'hasError'
        ];
        return $keysToPersist;
    }
}