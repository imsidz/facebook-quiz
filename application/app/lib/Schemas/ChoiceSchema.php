<?php
	namespace Schemas;
	
	class ChoiceSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Choice');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}