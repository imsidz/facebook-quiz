@extends('layout')
@section('content')
    @if(!empty($quizInactive))
        <div class="alert alert-danger clearfix" style="margin-top: 30px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4><strong>Hey Admin!</strong></h4>
            <div class="btn btn-red pull-right" data-dismiss="alert"><span>Okay</span></div>
            <strong>This Quiz is currently inactive.</strong>
            <p>Only you could view this.</p>
        </div>
    @endif
    <script>
        var QuizData = {!! json_encode($quiz)  !!};
        var QuizMeta = {
            viewQuizUrl: '{{QuizHelpers::viewQuizUrl($quiz)}}'
        };
        @if(!empty($quizResultId))
            var quizResultId = "{{$quizResultId}}";
        @endif
        @if(!empty($quizUserResultImage))
            var quizUserResultImage = "{{$quizUserResultImage}}";
        @endif
        @if(!empty($sharedUserId))
            var quizSharedUserId = '{{$sharedUserId}}';
        @endif
    </script>
    <div id="quizHeader"></div>
    <div class="row quiz-progress-row">
        <div class="col-md-12">
            <div id="quizProgress"></div>
        </div>
    </div>
    <div id="quizCanvasContainer">
        {{do_action_ref_array('show_widgets', ['aboveQuizQuestion', &$widgets])}}
        @if(!empty($widgets['aboveQuizQuestion']))
            <div class="above-quiz-widget-section text-center">
                @foreach($widgets['aboveQuizQuestion'] as $widget)
                    {!! do_shortcode($widget['content']) !!}
                @endforeach
            </div>
        @endif

        <div id="quizCanvas">
            <br><br>
            <h4 class="text-center">{{__('loadingQuiz')}}</h4>
            <div id="quizLoadingSpinner" style="position: relative; margin-top: 20px; height: 80px;"></div>
            <br>
        </div>
    </div>

    <div class="top-share-buttons-section">
        @include('quizes.partials.share-buttons', array('quiz' => $quiz, 'showEmbedCode'  =>  @$showEmbedCode))
    </div>

    {{do_action_ref_array('show_widgets', ['belowQuiz', &$widgets])}}
    @if(!empty($widgets['belowQuiz']))
        <div class="below-quiz-widget-section">
            @foreach($widgets['belowQuiz'] as $widget)
                {!! do_shortcode($widget['content']) !!}
            @endforeach
        </div>
    @endif

    {{do_action('above_post_comments', $quiz)}}

    @if(!$isEmbed)
        @include('partials.comments')
    @endif

    {{do_action('below_post_comments', $quiz)}}

    @if(!$isEmbed)
        <div id="belowQuizMoreQuizzesBlock">
            <h4>{{__('youMayAlsoLike')}}</h4>
            {!! do_shortcode(empty($config['quiz']['youMayAlsoLikeShortCode']) ? QuizController::DEFAULT_YOU_MAY_LIKE_SHORT_CODE : $config['quiz']['youMayAlsoLikeShortCode']) !!}
            @if(is_array($quizes) && count($quizes))
                <div class="text-center">
                    <a href="{{route('quizes')}}" class="btn btn-primary"><span>{{__('viewMoreQuizzes')}}</span></a>
                </div>
            @endif
        </div>
    @endif

    @if($isEmbed)
        <div class="row">
            <div class="col-md-12">
                {{do_action_ref_array('show_widgets', ['embedQuizFooter', &$widgets])}}
                @if(!empty($widgets['embedQuizFooter']))
                    <div class="embed-quiz-footer-widget-section">
                        @foreach($widgets['embedQuizFooter'] as $widget)
                            {!! do_shortcode($widget['content']) !!}
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <br/>
    @endif

    {{do_action('view_post_page_bottom', $quiz)}}

@stop

@section('foot')
    @parent
    {{do_action('view_post_page_foot', $quiz)}}

    <script src="{{ assetWithCacheBuster('bower_components/spinjs/spin.js')}}"></script>
    <script src="{{assetWithCacheBuster('js/social-sharing.js')}}"></script>
    <script src="{{assetWithCacheBuster('js/jquery.circliful.js')}}"></script>
    <script>
        (function() {
            var body = $('body');
            SocialSharing.parse();
            body.on('click', '.social-sharing-btn', function() {
                body.trigger('social:share');
            });
        })();
    </script>
    <script>
        (function(){
            var spinner = new Spinner({
                zIndex: 999,
                color: '#888'
            }).spin();
            $('#quizLoadingSpinner').append(spinner.el);
        })();
    </script>

    <script src="{{ assetWithCacheBuster('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
    <script src="{{ assetWithCacheBuster('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>

    @if(App::isLocal())
        <script data-main="{{ assetWithCacheBuster('js/quiz/init.js')}}" src="{{ assetWithCacheBuster('bower_components/requirejs/require.js')}}"></script>
    @else
        <script data-main="{{ assetWithCacheBuster('js/quiz/bundle.min.js')}}" src="{{ assetWithCacheBuster('bower_components/requirejs/require.js')}}"></script>
    @endif

    @if(!$isEmbed)
        <script>
            $(function(){
                var $container = $('.quiz-items-row');
                imagesLoaded($container, function(){
                    var masonry = new Masonry( $container[0], {
                        itemSelector: '.quiz-item'
                    });
                });
            });
        </script>
    @endif

    <script>
        $(function(){
            var bodyContainer = $('body');
            function informHeightChange(){
                var message = {
                    type: "quiz-embed-iframe-height-change",
                    height: bodyContainer.height(),
                    quizId: '{{$quiz->id}}',
                    elmPrefix: '{{$embedIframeElementIdPrefix}}'
                };
                parent.postMessage(JSON.stringify(message), '*');
            }
            setInterval(function() {
                informHeightChange();
            }, 500);
            $('body').on('scroll-top', function() {
                var message = {
                    type: "quiz-embed-scroll-top",
                    quizId: '{{$quiz->id}}',
                    elmPrefix: '{{$embedIframeElementIdPrefix}}',
                    topPos: $('#quizCanvasContainer').offset().top
                };
                parent.postMessage(JSON.stringify(message), '*');
            });
        });
    </script>
    @if(isConfigEnabled('quiz.autoQuizStart'))
        <script>
            $('body').on('quiz:ready', function() {
                AppMan.trigger('quiz:start');
            });
        </script>
    @endif
@stop

@if($isEmbed)
@section('head')
    @parent
    <style>
        .main-content-col {
            padding-bottom: 0px !important;
        }
        .sidebar-col {
            display: none;
        }
        body .body-container {
            max-width: none !important;
        }
        .quiz-topic-head-row, .quiz-banner-head-row {
            border: 0px !important;
        }
    </style>
    <script>
        window.isQuizEmbedded = true;
    </script>
@stop

@section('sidebar')
@stop

@section('header')
@stop

@section('footer')
    <script>
        $('.main-content-col').removeClass('col-md-8 col-sm-7 col-xs-12').addClass('col-md-12');
    </script>
@stop
@endif