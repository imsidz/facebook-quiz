<style>
    .latest-activity .media {
        margin-top: 10px;
    }
</style>
<div class="latest-activity">
    @forelse($latestActivity as $activity)
        @include('admin.partials.activityItem', [
            'userPhoto' => content_url($activity->user->photo),
            'userName' => $activity->user->name,
            'activityType' => $activity->type,
            'targetTitle' => $activity->quiz->topic,
            'targetLink' => QuizHelpers::viewQuizUrl($activity->quiz),
            'targetImage' => QuizHelpers::getThumbnail($activity->quiz),
            'time'  =>  Helpers::prettyTime($activity->created_at, false)
            ])
    @empty
        <div class="alert alert-warning">No recent activity</div>
    @endforelse
</div>