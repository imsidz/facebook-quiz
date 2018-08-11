<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizReferrals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('user_referrals', function($table)
        {
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->integer('referrals')->unsigned();
            $table->timestamps();
			$table->primary('user_id');
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
		Schema::drop('user_referrals');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}

}
