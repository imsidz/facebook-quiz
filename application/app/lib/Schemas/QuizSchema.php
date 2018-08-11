<?php
	namespace Schemas;
	
	class QuizSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Quiz');
			if(\File::exists($schemaPath)) {
				$schema = \File::get($schemaPath);
                $schema = apply_filters('schema', $schema, 'quiz');
                return $schema;
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}