<?php
namespace Schemas;

class LanguagesSchema extends BaseSchema {
    public function getSchema(){
        $schemaPath = $this->getSchemaPath('Languages');
        if(\File::exists($schemaPath)) {
            $schema = \File::get($schemaPath);
            $schemaObj = json_decode($schema, true);
            foreach($schemaObj['items']['properties']['strings']['properties'] as $key => $val){
                $schemaObj['items']['properties']['strings']['properties'][$key]['type'] = "string";
            }
            //dd($schemaObj['items']['properties']['strings']['properties']);
            $schema = json_encode($schemaObj);
            return apply_filters('languages_schema', $schema);
            return $schema;
        } else{
            throw new \Exception('Schema file not found');
        }
    }
}