<?php

use Jitheshgopan\Leaderboard\Repositories\EloquentBoardRepository as Board;
class LeaderboardController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $perPage = 10;
        $topUsersQuery = User::getTopQuery();
        $topUsersPagination = User::paginateTop($perPage);
        //dd($topUsersPagination);
        $topUsers = User::getItemsFromBoardEntries($topUsersPagination);
        $rank = $topUsersPagination->firstItem();
        foreach($topUsers as $topUser) {
            $topUser['leaderboard_rank'] = $rank;
            $rank++;
        }
        return View::make('leaderboard.leaderboard')->with([
            'leaderboardTopUsers' =>  $topUsers,
            'leaderboardTopUsersPagination'   =>  $topUsersPagination
        ]);
    }


    /**
     * Show the leaderboard widget
     *
     * @return Response
     */
    public function widget()
    {
        //
        $topUsers = User::getTopNUsers();
        return View::make('leaderboard.widget')->with([
            'leaderboardTopUsers' =>  $topUsers
        ]);
        //dd(DB::getQueryLog());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


}
