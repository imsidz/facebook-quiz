<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('pages', function($table)
		{
			$table->increments('id');
			$table->string('title')->nullable();
			$table->string('description')->nullable();
			$table->string('urlString')->index()->nullable();
			$table->text('ogData')->nullable();
			$table->text('content')->nullable();
			$table->timestamps();
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
		Schema::drop('pages');
	}

}
