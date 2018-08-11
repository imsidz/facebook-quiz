<?php
	namespace Schemas;
	
	class QuestionSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Question');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}