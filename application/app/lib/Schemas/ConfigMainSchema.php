<?php
	namespace Schemas;
	
	class ConfigMainSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('ConfigMain');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}