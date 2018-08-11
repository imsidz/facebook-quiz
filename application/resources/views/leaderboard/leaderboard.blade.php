@extends('layout')
@section('content')
    {{do_action_ref_array('show_widgets', ['leaderboardPageHeader', &$widgets])}}
@if(!empty($widgets['leaderboardPageHeader']))
    <div class="row">
        <div class="col-md-12">
            <div class="leaderboard-page-header-widget-section">
                @foreach($widgets['leaderboardPageHeader'] as $widget)
                    {!! do_shortcode($widget['content']) !!}
                @endforeach
            </div>
        </div>
    </div>
@endif
<div class="row">
    <div class="leaderboard col-md-8 col-md-offset-2">
        <div class="panel panel-leaderboard">
            <div class="panel-heading text-left bg-main-btn-color">
                <div class="panel-title text-center">
                    <h1 class="leaderboard-heading">{{__('leaderboard')}}</h1>
                </div>
            </div>
            <div class="panel-body">
                <ol class="user-list">
                @forelse($leaderboardTopUsers as $index => $user)
                    <li>
                        <div class="user-row clearfix">
                            <div class="leaderboard-rank-container pull-left">
                                <span class="leaderboard-rank">{{$user->leaderboard_rank}}</span>
                            </div>
                            @if(LeaderboardHelpers::getTopBadgeIcon($user->leaderboard_rank))
                                <div class="pull-right badge-container">
                                    <img src="{{LeaderboardHelpers::getTopBadgeIcon($user->leaderboard_rank)}}" width="80" data-pin-nopin="true">
                                </div>
                            @endif
                            <div class="pull-left user-image-box">
                                <img src="{{$user->photo}}" class="img-circle user-img">
                            </div>
                            <div class="user-details">
                                <p class="user-name">{{$user->name}}</p>
                        <span class="user-points">
                            {{--<i class="fa fa-star star-icon"></i>--}}
                            <span class="star-icon"><img src="{{LeaderboardHelpers::getPointsIcon()}}" alt="" width="20"/></span>
                            <b class="main-btn-color user-points-value">{{$user->points}}</b> {{__('points')}}
                        </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li>{{__('noUsersYet')}}</li>
                @endforelse
            </ol>
            </div>
            {{$leaderboardTopUsersPagination->render()}}
        </div>
    </div>
</div>

@stop