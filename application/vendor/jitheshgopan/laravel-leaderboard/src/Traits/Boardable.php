<?php
namespace Jitheshgopan\Leaderboard\Traits;

use Jitheshgopan\Leaderboard\Models\Board;
use Jitheshgopan\Leaderboard\Repositories\EloquentBoardRepository;

/**
 * Class Boardable.
 */
trait Boardable
{
    /**
     * Reward the given of amount of points.
     *
     * @param int $points
     *
     * @return mixed
     */
    public function reward($points)
    {
        return $this->leaderboard()->reward($points);
    }

    /**
     * Calculate the rank of the user
     *
     */
    public function calculateRank()
    {
        return $this->leaderboard()->calculateRank();
    }

    /**
     * Remove the given amount of points.
     *
     * @param int $points
     *
     * @return mixed
     */
    public function penalize($points)
    {
        return $this->leaderboard()->penalize($points);
    }

    /**
     * Multiply all points by the given factor.
     *
     * @param int|float $multiplier
     *
     * @return mixed
     */
    public function multiply($multiplier)
    {
        return $this->leaderboard()->multiply($multiplier);
    }

    /**
     * Redeem the given amount of points.
     *
     * @param int $points
     *
     * @return boolean
     */
    public function redeem($points)
    {
        return $this->leaderboard()->redeem($points);
    }

    /**
     * Disable an account for receiving points.
     *
     * @return mixed
     */
    public function blacklist()
    {
        return $this->leaderboard()->blacklist();
    }

    /**
     * Enable an account for receiving points.
     *
     * @return mixed
     */
    public function whitelist()
    {
        return $this->leaderboard()->whitelist();
    }

    /**
     * Reset all points of an entity to zero.
     *
     * @return mixed
     */
    public function reset()
    {
        return $this->leaderboard()->reset();
    }

    /**
     * Get get total points of the entity.
     *
     * @return int
     */
    public function getPoints()
    {
        return ($this->board ? $this->board->points : 0);
    }

    /**
     * Get the current rank of the entity.
     *
     * @return int
     */
    public function getRank()
    {
        return ($this->board ? $this->board->rank : null);
    }

    /**
     * @return bool
     */
    public function isBlacklisted()
    {
        return $this->board && $this->board->blacklisted;
    }

    /**
     * @return mixed
     */
    public function board()
    {
        return $this->morphOne('Jitheshgopan\Leaderboard\Models\Board', 'boardable');
    }

    /**
     * @return EloquentBoardRepository
     */
    protected function leaderboard()
    {
        return new EloquentBoardRepository($this);
    }

    public static function getIdsOfTopN($count = 10) {
        $topBoardEntries = EloquentBoardRepository::getTopN(__CLASS__, $count);
        $topItemIds = $topBoardEntries->lists('boardable_id');
        return $topItemIds;
    }

    public static function getTopN($count = 10) {
        $topBoardEntries = EloquentBoardRepository::getTopN(__CLASS__, $count);
        $topItems = self::getItemsFromBoardEntries($topBoardEntries);
        $rank = 1;
        foreach($topItems as $key => $item) {
            $topItems[$key]['leaderboard_rank'] = $rank;
            $rank++;
        }
        return $topItems;
    }

    public static function getTopQuery($count = 10) {
        $topBoardEntriesQuery = EloquentBoardRepository::getTopQuery(__CLASS__);
        return $topBoardEntriesQuery;
    }

    public static function getItemsFromBoardEntries($boardEntries) {
        $items = [];
        $boardEntries->each(function($boardEntry) use(&$items){
            $item = self::getItemFromBoardEntry($boardEntry);
            $items[] = $item;
        });
        return $items;
    }

    public static function getItemFromBoardEntry($boardEntry) {
        $item = $boardEntry->boardable;
        return $item;
    }

    public static function paginateTop($limit) {
        return EloquentBoardRepository::getTopQuery(__CLASS__)->paginate($limit);
    }
}
