<?php
namespace Jitheshgopan\Leaderboard\Repositories;

use Jitheshgopan\Leaderboard\Contracts\BoardRepository;
use Jitheshgopan\Leaderboard\Exceptions\BlacklistedException;
use Jitheshgopan\Leaderboard\Exceptions\InsufficientFundsException;
use Jitheshgopan\Leaderboard\Models\Board;

/**
 * Class EloquentBoardRepository.
 */
class EloquentBoardRepository implements BoardRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param \Jitheshgopan\Leaderboard\Traits\Boardable $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Reward the given of amount of points.
     *
     * @param int $points
     *
     * @throws BlacklistedException
     */
    public function reward($points)
    {
        $this->abortIfBlacklisted();

        if ($this->getBoardQuery()->count()) {
            $this->getBoard()->increment('points', $points);
        } else {
            $this->getBoardQuery()->save(
                new Board(['points' => $points])
            );
        }

        $this->calculateRank();
    }

    /**
     * Remove the given amount of points.
     *
     * @param int $points
     *
     * @throws BlacklistedException
     */
    public function penalize($points)
    {
        $this->abortIfBlacklisted();

        $this->getBoard()->decrement('points', $points);

        $this->saveBoardInstance();
    }

    /**
     * Multiply all points by the given factor.
     *
     * @param float|int $multiplier
     *
     * @throws BlacklistedException
     */
    public function multiply($multiplier)
    {
        $this->abortIfBlacklisted();

        $this->getBoard()->points = $this->getBoard()->points * $multiplier;

        $this->saveBoardInstance();
    }

    /**
     * Redeem the given amount of points.
     *
     * @param int $points
     *
     * @throws BlacklistedException
     * @throws InsufficientFundsException
     *
     * @return bool
     */
    public function redeem($points)
    {
        $this->abortIfBlacklisted();

        $afterRedemeption = $this->getBoard()->points - $points;

        if ($afterRedemeption < 0) {
            throw new InsufficientFundsException(
                $this->getBoard()->getType(),
                $this->getBoard()->getId(),
                $afterRedemeption
            );
        }

        $this->penalize($points);

        return true;
    }

    /**
     * Disable an account for receiving points.
     */
    public function blacklist()
    {
        $this->getBoard()->blacklisted = true;

        $this->saveBoardInstance();
    }

    /**
     * Enable an account for receiving points.
     */
    public function whitelist()
    {
        $this->getBoard()->blacklisted = false;

        $this->saveBoardInstance();
    }

    /**
     * Reset an accounts points.
     *
     * @throws BlacklistedException
     */
    public function reset()
    {
        $this->abortIfBlacklisted();

        $this->getBoard()->points = 0;

        $this->saveBoardInstance();
    }

    /**
     * Calculate the ranks based on points.
     */
    public function calculateRank()
    {
        $rank = $this->getBoard()->select(\DB::raw('FIND_IN_SET( points, (
            SELECT GROUP_CONCAT( points
            ORDER BY points DESC )
            FROM '. $this->getBoard()->getTable() .' )
            ) AS rank'))->where('boardable_id', $this->model->id)->first();

        $rank = $rank->rank;
        $board = $this->getBoard();
        $board->rank = $rank;
        $board->save();
    }

    /**
     * Cancel the current action if a user is blacklisted.
     *
     * @throws BlacklistedException
     *
     * @return bool
     */
    protected function abortIfBlacklisted()
    {
        if ($this->model->isBlacklisted()) {
            throw new BlacklistedException(
                $this->getBoard()->getType(),
                $this->getBoard()->getId()
            );
        }

        return false;
    }

    /**
     * Save the board model and recalculate all ranks.
     */
    protected function saveBoardInstance()
    {
        $this->getBoard()->save();

        $this->calculateRankings();
    }

    /**
     * @return mixed
     */
    protected function getBoard()
    {
        if(!$this->model->board) {
            $this->model->board = $this->model->board()->first();
        }
        return $this->model->board;
    }

    /**
     * @return mixed
     */
    protected function getBoardQuery()
    {
        return $this->model->board();
    }

    /**
     * Calculate the ranks based on points.
     */
    public static function calculateRankings()
    {
        $boards = Board::orderBy('points', 'DESC')->get();

        foreach ($boards as $index => $board) {
            $board->rank = $index + 1;
            $board->push();
        }
    }

    public static function getTopN($boardableType, $count = 10) {
        return (self::getTopQuery($boardableType)->take($count)->get());
    }

    public static function getTopQuery($boardableType) {
        return (Board::where('boardable_type', $boardableType)->orderBy('points', 'DESC')->with('boardable'));
    }

    public static function paginateTop($boardableType, $limit) {
        return self::getTopQuery($boardableType)->paginate($limit);
    }

}
