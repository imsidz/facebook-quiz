<?php
	namespace Schemas;

	class BaseSchema {
		public function getSchemaPath($schema) {
			return rtrim(__dir__, '/') . '/' . $schema . '.json';
		}
	}