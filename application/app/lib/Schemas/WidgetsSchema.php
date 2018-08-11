<?php
	namespace Schemas;
	
	class WidgetsSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Widgets');
			if(\File::exists($schemaPath)) {
			    $schema = \File::get($schemaPath);
				return apply_filters('widgets_schema', $schema);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}