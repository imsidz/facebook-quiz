<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizUserResults extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('quiz_user_results', function($table)
        {
			$table->increments('id');
            $table->integer('quiz_id')->unsigned();
			$table->foreign('quiz_id')->references('id')->on('quizes')->onDelete('cascade');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('result_id', 100);
            $table->timestamps();
			$table->unique(array('quiz_id', 'user_id', 'result_id'));
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
		Schema::drop('quiz_user_results');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}

}
