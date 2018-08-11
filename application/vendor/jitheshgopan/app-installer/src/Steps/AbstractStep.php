<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:34 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;
use Jitheshgopan\AppInstaller\Exceptions\StepHandlerMissingException;
use \View;
use \Jitheshgopan\AppInstaller\Installer;

abstract class AbstractStep {

    /*
     * The abstract process method
     */
    public abstract function process($prevStep);

    protected $id;
    protected $key;
    protected $stage;
    protected $name;
    protected $options;
    protected $output;
    protected $errors = array();
    protected $hasPassed = false;
    protected $muted = false;
    protected $data = array();
    protected $hasRun = false;
    protected $blocking = false;
    protected $handler = null;
    protected $waiting = false;
    protected $sourceStep;

    public function __construct($name, $options){
        $this->setName($name);
        $this->setOptions($options);
        if(isset($options['blocking'])){
            $this->blocking = $options['blocking'];
        }

        if(isset($options['data'])){
            $this->data = $options['data'];
        }

        if(isset($options['handler'])){
            $this->handler = $options['handler'];
        }
    }

    public function __sleep(){
        return $this->getKeysToPersist();
    }

    public function __wakeup($data = array()){
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

    }

    public function getDataToPersist(){
        $keysToPersist = $this->getKeysToPersist();
        $allData = get_object_vars($this);
        $dataToPersist = array_intersect_key($allData, array_flip($keysToPersist));
        return $dataToPersist;
    }

    public function getKeysToPersist(){
        return [
            'data'/*,
            'output',
            'errors',
            'hasPassed',
            'hasRun'*/
        ];
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
     * Run the step
     */
    public function run($prevStep){
        $this->preRun();
        $result = $this->process($prevStep);
        $this->postRun();
        if(is_callable($this->handler)) {
            $handlerResult = call_user_func($this->handler, $this);
        } else if(method_exists($this, 'handler')){
            $handlerResult = $this->handler();
        } else {
            throw new StepHandlerMissingException();
        }
        if($handlerResult) {
            $this->markAsPassed();
        } else {
            $this->hasPassed = false;
        }
        return $handlerResult;
    }

    /*
     * Get steps that should follow the current step(Varies with different step. Created by the step itself)
     * @return array Steps
     */
    public function getFollowingSteps(){
        return array();
    }

    /*
     * Get steps that should precede the current step(Varies with different step. Created by the step itself)
     * @return array Steps
     */
    public function getPrecedingSteps(){
        return array();
    }

    /*
     * Set Step Id
     * @param $id the id to be set
     * @return $this
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /*
     * Get Step Id
     * @return the id
     */
    public function getId(){
        return($this->id);
    }

    /*
     * Set the Stage the step belongs to
     * @param $id the id to be set
     * @return $this
     */
    public function setStage($stage){
        $this->stage = $stage;
        return $this;
    }

    /*
     * Get the stage the step belongs to
     * @return the id
     */
    public function getStage(){
        return($this->stage);
    }

    /*
    * Set Step Key
    * @param $key the key to be set
    * @return $this
    */
    public function setKey($key){
        $this->key = $key;
        return $this;
    }

    /*
     * Get Step key
     * @return the key
     */
    public function getKey(){
        return($this->key);
    }

    /*
     * Check if a step type is valid
     * @param $stepTypeName step type
     * return boolean
     */
    public static function isValidStepType($stepTypeName){
        $stepClassName = self::getStepClassName($stepTypeName);
        if(!class_exists($stepClassName)){
            return false;
        }
        return true;
    }

    /*
     * Get step class name form step type
     * @param $stepTypeName step type
     * @return string step class name
     */
    public static function getStepClassName($stepTypeName){
        $stepClassName = ($stepTypeName == "Step") ? "Step" : $stepTypeName . "Step";
        $stepClassName = 'Jitheshgopan\AppInstaller\Steps\\' . $stepClassName;
        return $stepClassName;
    }

    /*
     * Create step of a type
     * @param $stepType Step type name
     * @param $name Step name
     * @param options step options
     * @return Step of required type
     */
    public static function createStep($stepType, $name,  $options = array()){
        if(!self::isValidStepType($stepType)){
            throw new InvalidStepTypeException("Step of type '" . $stepType . "' doesn't exist");
        }
        $stepClassName = self::getStepClassName($stepType);
        return new $stepClassName($name, $options);
    }

    /*
     * Get name
     * @return $name
     */
    public function getName(){
        return $this->name;
    }

    /*
     * Set name of the step
     * @param $name The name of the step
     */
    public function setName($name){
        return $this->name = $name;
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
     * @param $options The options of the step
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
     * Set a single option
     * @param $options The options of the step
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
     * Set output of the step
     * @param $output the output html
     */
    public function setOutput($output){
        $this->output = $output;
    }

    /*
     * Get output of the step
     * @return output html
     */
    public function getOutput(){
        return($this->output);
    }

    /*
     * Mark the step as passed
     */
    public function markAsPassed(){
        $this->hasPassed = true;
    }

    /*
     * Check if the step has passed
     * @return boolean
     */
    public function hasPassed(){
        return $this->hasPassed;
    }

    /*
     * Check if the step has finished
     */
    public function hasFinished(){
        if($this->isWaiting()){
            return false;
        }
        return $this->hasRun();
    }

    /*
     * Check if the step has errors
     * @return boolean
     */
    public function hasError(){
        return !empty($this->errors);
    }

    /*
     * Check if the step has output to be shown
     * @return boolean
     */
    public function hasOutput(){
        return !!$this->getOutput();
    }

    /*
     * Mute the step to disable showing in stats
     * @return $this
     */
    public function mute(){
        $this->muted = true;
       return $this;
    }

    /*
     * Check if the step is muted
     * @return boolean
     */
    public function isMuted(){
        return $this->muted;
    }

    /*
     * Get step Data
     * @return Array Data
     */
    public function getData(){
        return $this->data;
    }

    /*
     * Set step Data
     * @param the data to be set
     */
    public function setData($data){
        return $this->data = $data;
    }

    /*
     * Check if the step has run
     * @return boolean
     */
    public function hasRun(){
        return $this->hasRun;
    }

    /*
     * Check if the step is blocking type
     * @return boolean
     */
    public function isBlocking(){
        return $this->blocking;
    }

    /*
     * Save errors
     * @param $errors array
     */
    public function saveErrors($errors){
        $this->errors = $this->errors ?: array();
        $this->errors = array_merge($this->errors, $errors);
    }

    /*
     * Get errors
     * @return $errors array
     */
    public function getErrors(){
        $this->errors = $this->errors ?: array();
        return $this->errors;
    }

    /*
     * Clear errors
     */
    public function clearErrors(){
        $this->errors = array();
    }

    /*
     * Mark step as waiting
     */
    public function wait(){
        $this->waiting = true;
    }

    /*
     * Mark step as not waiting
     */
    public function stopWaiting(){
        $this->waiting = false;
    }

    /*
     * Check if step is in waiting state
     */
    public function isWaiting(){
        return($this->waiting);
    }

    /*
     * Set step handler function
     * @param $handler closure handler function
     */
    public function setHandler($handler){
        $this->handler = $handler;
    }

    /*
     * Restore data from serialized object
     */
    public function restoreFromSerializedData($serializedData){
        $rebornStep = unserialize($serializedData);
        $preservedKeys = $this->getKeysToPersist();
        foreach($preservedKeys as $key){
            $this->$key = $rebornStep->$key;
        }
    }

    /*
     * Check if the step is current step in the stage it belongs to(managed by the stage)
     */
    public function isCurrent(){
        return $this->stage->isCurrentStep($this);
    }

    /*
     * Set a step as a data source for this step
     * @param $step Step - The source step
     */
    public function setSourceStep($step){
        $this->sourceStep = $step;
    }

    /*
     * Get a step as a data source for this step
     * @return Step - the source step
     */
    public function getSourceStep(){
        return($this->sourceStep);
    }

    /*
     * Render the error view and set the result as output
     */
    public function renderErrorView(){
        $errorView = View::make(Installer::view('steps.errors'))->with([
            'step' => $this
        ]);
        $this->setOutput($errorView->render());
    }
}