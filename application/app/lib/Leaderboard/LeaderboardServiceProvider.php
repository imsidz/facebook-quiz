<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 05/04/15
 * Time: 9:41 PM
 */
namespace App\Leaderboard;
class LeaderboardServiceProvider extends \Illuminate\Support\ServiceProvider{

    public function register() {
        $this->app->singleton('leaderboard', function() {
            return new Leaderboard();
        });
        \Event::listen('config:loaded', function() {
            \Leaderboard::initialize();
        });
    }
}