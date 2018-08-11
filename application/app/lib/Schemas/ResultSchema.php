<?php
	namespace Schemas;
	
	class ResultSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Result');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}