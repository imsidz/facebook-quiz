<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizStats extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('quiz_stats', function($table) {
			$table->integer('quiz_id')->unsigned();
			$table->foreign('quiz_id')->references('id')->on('quizes')->onDelete('cascade');
			$table->integer('attempts')->unsigned();
			$table->integer('completions')->unsigned();
			$table->integer('shares')->unsigned();
			$table->integer('likes')->unsigned();
			$table->integer('comments')->unsigned();
			$table->primary('quiz_id');
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
		Schema::drop('quiz_stats');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}

}
