<script>
    function disqus_config() {
        var body = $('body');
        this.callbacks.onNewComment = [
            function (comment) {
                $('body').trigger('disqus:new-comment', comment);
            }
        ];
    }
</script>

@unless(@$config['quiz']['comments'] == 'disabled')
    <div class="quiz-comments-container">
        <h4>{{__('comments')}}</h4>
        @if(@$config['quiz']['comments'] == 'disqus')
            @if(empty($config['quiz']['disqusShortname']))
                <div class="alert alert-warning">
                    <h4>Disqus comments cannot be displayed.</h4>
                    'Disqus shortname' is missing. Add it in "Quiz config" page in admin panel.
                </div>
            @else
                <div class="quiz-comments" id="disqus_thread"></div>
                <script type="text/javascript">
                    var disqus_shortname = '{{@$config['quiz']['disqusShortname']}}'; // Required - Replace example with your forum shortname
                    var disqus_identifier = '{{@$config['quiz']['disqusShortname']}}-socioquiz-quiz-{{$quiz->id}}';
                    var disqus_title = '{{{$quiz->title}}}';
                    var disqus_url = '{{QuizHelpers::viewQuizUrl($quiz)}}';

                    /* * * DON'T EDIT BELOW THIS LINE * * */
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
            @endif
        @elseif(@$config['quiz']['comments'] == 'facebook' || (!empty($config['quiz']['showFacebookComments']) && $config['quiz']['showFacebookComments'] != "false"))
            <div class="quiz-comments fb-comments" data-href="{{QuizHelpers::viewQuizUrl($quiz)}}" data-width="100%" data-numposts="10" data-colorscheme="light"></div>
        @endif
    </div>
@endunless
