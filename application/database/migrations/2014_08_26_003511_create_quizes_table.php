<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('quizes', function($table)
		{
			$table->increments('id');
			$table->string('topic')->nullable()->index();
			$table->string('image')->nullable();
			$table->text('description')->nullable();
			$table->text('pageContent')->nullable();
			$table->string('type')->nullable();
			$table->text('questions')->nullable();
			$table->text('results')->nullable();
			$table->text('ogImages')->nullable();
			$table->boolean('active')->default(false)->index();
			$table->timestamps();
			$table->index('created_at');
			$table->index('updated_at');
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
		Schema::drop('quizes');
	}

}
