<div class="leaderboard-widget">
    <div class="panel panel-leaderboard">
        <div class="panel-heading bg-main-btn-color">
            <div class="panel-title">
                {{__('leaderboard')}}
            </div>
        </div>
        <div class="panel-body">
            <div class="text-center">
                <img class="leaderboard-banner" src="{{ LeaderboardHelpers::getBanner() }}">
            </div>
            <ol class="user-list">
                @forelse($leaderboardTopUsers as $index => $user)
                    <li class="@if($index > 4) rank-beyond-5 @endif">
                        <div class="user-row clearfix">
                            <div class="pull-left user-image-box">
                                <img src="{{$user->photo}}" class="img-circle user-img">
                                <span class="user-rank bg-main-btn-color">{{$user->leaderboard_rank}}</span>
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
                @if(count($leaderboardTopUsers) >= 10)
                    <a class="btn btn-block btn-primary view-leaderboard-btn" href="{{route('leaderboard')}}"><span>{{__('viewAllLeaderboard')}}</span></a>
                @endif
            </ol>
        </div>
        {{do_action_ref_array('show_widgets', ['leaderboardWidgetBottom', &$widgets])}}
        @if(!empty($widgets['leaderboardWidgetBottom']))
            <div class="row">
                <div class="col-md-12">
                    <div class="leaderboard-widget-bottom-widget-section">
                        @foreach($widgets['leaderboardWidgetBottom'] as $widget)
                            {!! do_shortcode($widget['content']) !!}
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if(count($leaderboardTopUsers) > 5)
            <div class="panel-footer bg-main-btn-color" onclick="toggleLeaderboardExpand()">
                <div class="expand-btn-text">{{__('expandLeaderboard')}}</div>
                <div class="contract-btn-text">{{__('contractLeaderboard')}}</div>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleLeaderboardExpand() {
        $('.leaderboard-widget').toggleClass('expanded');
    }

</script>
