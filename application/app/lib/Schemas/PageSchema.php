<?php
	namespace Schemas;
	
	class PageSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('Page');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}