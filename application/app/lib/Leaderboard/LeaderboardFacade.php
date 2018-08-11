<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 05/04/15
 * Time: 9:40 PM
 */

namespace App\Leaderboard;
use Illuminate\Support\Facades\Facade;

class LeaderboardFacade extends Facade{
    public static function getFacadeAccessor() {
        return 'leaderboard';
    }
}