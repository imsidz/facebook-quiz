<?php

namespace Traits;
trait UserLeaderboardTrait {

    public static function getTopNUsers($count = 10) {
        //$topUserIds = self::getIdsOfTopN($count);
        //return self::whereIn('id', $topUserIds)->get();
        return self::getTopN($count);
    }

}