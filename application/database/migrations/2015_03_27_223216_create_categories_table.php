<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create('categories', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('categories');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}

}
