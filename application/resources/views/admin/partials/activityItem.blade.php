<div class="media">
    <a class="media-left pull-left" href="#">
        <img class="img-circle" src="{{ $userPhoto }}" style="width: 50px; height: 50px;">
    </a>
    <a class="media-right pull-right" href="{{$targetLink}}" target="_blank">
        <img class="" src="{{ $targetImage }}" style="width: 120px; max-height: 80px;">
    </a>

    <div class="media-body">
        <div class="media-heading">
            <div><b>{{$userName}}</b></div>
            <div>
                @if($activityType == 'attempt')
                        <b class="label label-default">Attempted</b>
                @endif
                @if($activityType == 'share')
                        <b class="label label-warning">Shared</b>
                @endif
                @if($activityType == 'comment')
                        <b class="label label-info">Commented</b>
                @endif
                @if($activityType == 'like')
                        <b class="label label-warning">Liked</b>
                @endif
                @if($activityType == 'completion')
                        <b class="label label-success">Completed</b>
                @endif
            </div>
            <div><small class="text-muted">on {{ $time }}</small></div>
        </div>
    </div>
</div>