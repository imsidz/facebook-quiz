<?php
namespace Jitheshgopan\Leaderboard;

use Illuminate\Support\ServiceProvider;

/**
 * Class LeaderboardServiceProvider.
 */
class LeaderboardServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/migrations/' => database_path('/migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(
            'Jitheshgopan\Leaderboard\Contracts\BoardRepository',
            'Jitheshgopan\Leaderboard\Repositories\EloquentBoardRepository'
        );
    }
}
