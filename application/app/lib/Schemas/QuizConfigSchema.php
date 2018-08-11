<?php
	namespace Schemas;
	
	class QuizConfigSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('QuizConfig');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}