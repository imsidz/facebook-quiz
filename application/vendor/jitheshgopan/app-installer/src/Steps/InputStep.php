<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:36 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


use Illuminate\Support\Facades\View;
use Jitheshgopan\AppInstaller\Installer;
use Jitheshgopan\AppInstaller\Exceptions;
use JsonSchema\Validator as JsonValidator;

class InputStep extends AbstractStep{
    public function process($prevStep){
        if(!$this->hasPassed()) {
            $this->wait();
        }
        $fieldsSchema = $this->getFieldsSchema();
        $output = View::make(Installer::view('steps.inputStep'))->with([
            'step' => $this,
            'fieldsSchema' => $fieldsSchema
        ])->render();
        $this->setOutput($output);
        return $output;
    }

    public function getFollowingSteps(){
        return array(
            new Step("Automatically added", function(){}, [

            ])
        );
    }
    /*
     * Get fields of the input step
     * @return fields array
     * @throws InvalidArgumentException
     */
    public function getFields(){
        try {
            $fields = $this->options('fields');
            return $fields;
        } catch (\InvalidArgumentException $e){
            return array();
        }
    }

    /*
     * Get fields schema of the input step
     * @return fields schema
     * @throws InvalidArgumentException
     */
    public function getFieldsSchema(){
        try {
            $fields = $this->options('fields');
            $schema = $this->fieldsToSchema($fields);
            return $schema;
        } catch (\InvalidArgumentException $e){
            return array();
        }
    }

    /*
     * Converting fields array to json schema
     * @throws Jitheshgopan\AppInstaller\Exceptions\InvalidInputSchemaException
     */
    public function fieldsToSchema($fields = array()){
        $schema = new \stdClass();
        foreach($fields as $field => $value){
            if(is_string($value)){
                $schema->$field = (object) $this->_makeStringSchemaProperty($value);
            } else if(is_array($value)){
                $schema->$field = (object) $value;
            } else {
                throw new InvalidInputSchemaException("Invalid schema for field: " . $field);
            }
        }
        return $schema;
    }

    /*
     * Make simple string schema property form just a title
     * @param $title The title of the property
     */
    public function _makeStringSchemaProperty($title){
        return [
            'type' => "string",
            "required" => "true",
            "title" => $title
        ];
    }

    /*
     * The step handler
     */
    public function handler(){
        if(!$this->getData()){
            //Data not set. Don't validate now - just return
            return;
        } else {
            $this->stopWaiting();
            return $this->validateData();
        }
    }

    /*
     * Validate data against Json Schema
     */
    public function validateData(){
        $schema = (object) $this->getFieldsSchema();
        $schema->properties = $this->getFieldsSchema();
        $data = (object) $this->getData();
        $validator = new JsonValidator();
        $validator->check($data, $schema);
        $isValid = $validator->isValid();
        if(!$isValid){
            $this->saveErrors($validator->getErrors());
        } else {
            $this->clearErrors();
        }
        return $isValid;
    }
}