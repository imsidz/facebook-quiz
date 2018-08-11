<?php
	namespace Schemas;
	
	class LeaderboardConfigSchema extends BaseSchema {
		public function getSchema(){
			$schemaPath = $this->getSchemaPath('LeaderboardConfig');
			if(\File::exists($schemaPath)) {
				return \File::get($schemaPath);
			} else{
				throw new \Exception('Schema file not found');
			}
		}
	}