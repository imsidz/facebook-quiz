<div class="leaderboard-container"></div>
<script>
    (function() {
        var loadedClass = 'leaderboard-loaded';
        var leaderboardElm = $('.leaderboard-container').not('.' + loadedClass);
        leaderboardElm.load('{{route('leaderboardWidget')}}');
        leaderboardElm.addClass(loadedClass);
    })();
</script>