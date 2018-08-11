<?php

class LeaderboardHelpers {

    public static function getPointsIcon() {
        $siteConfig = Config::get('siteConfig');
        $pointsIcon = 'images/leaderboard/star.png';
        $pointsIcon = asset($pointsIcon);
        if(!empty($siteConfig['leaderboard'])) {
            $leaderboardConfig = $siteConfig['leaderboard'];
            if(!empty($leaderboardConfig['pointsIcon'])) {
                $pointsIcon = $leaderboardConfig['pointsIcon'];
            }
            $pointsIcon = content_url($pointsIcon);
        }
        return $pointsIcon;
    }

    public static function getBanner() {
        $siteConfig = Config::get('siteConfig');
        $banner = 'images/leaderboard/trophy.png';
        $banner = asset($banner);
        if(!empty($siteConfig['leaderboard'])) {
            $leaderboardConfig = $siteConfig['leaderboard'];
            if(!empty($leaderboardConfig['banner'])) {
                $banner = $leaderboardConfig['banner'];
            }
            $banner = content_url($banner);
        }
        return $banner;
    }

    public static function getTopBadgeIcon($pos) {
        $positionsWithBadges = [1,2,3];
        if(!in_array($pos, $positionsWithBadges))
            return false;
        $badge = 'images/leaderboard/top-'. $pos .'.png';
        return asset($badge);
    }

}