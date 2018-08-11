<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaderboardTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('boardable_id')->unsigned();
            $table->string('boardable_type');
            $table->integer('points')->unsigned();
            $table->integer('rank')->unsigned();
            $table->boolean('blacklisted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('leaderboard');
    }
}
