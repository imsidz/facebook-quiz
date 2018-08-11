@extends('layout')

@section('content')
    {{do_action_ref_array('show_widgets', ['aboveQuizzesList', &$widgets])}}
    @if(!empty($widgets['aboveQuizzesList']))
        <div class="quizzes-page-head-widget-section">
            @foreach($widgets['aboveQuizzesList'] as $widget)
                {!! do_shortcode($widget['content']) !!}
            @endforeach
        </div>
    @endif

    <h1 class="page-header">
        @if(!empty($categoryName))
            {{$categoryName}}
        @else
            {{$mainHeading}}
        @endif
    </h1>


    @include('quizes/quizesList', ['paginate' =>  true])

    {{do_action_ref_array('show_widgets', ['belowQuizzesList', &$widgets])}}
    @if(!empty($widgets['belowQuizzesList']))
        <div class="quizzes-page-foot-widget-section">
            @foreach($widgets['belowQuizzesList'] as $widget)
                {!! do_shortcode($widget['content']) !!}
            @endforeach
        </div>
    @endif

@stop

@section('foot')
    @parent
    <script src="{{ asset('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script>
        $(function(){
            var $container = $('.quiz-items-row');
            imagesLoaded($container, function(){
                $container.masonry( $container[0], {
                    itemSelector: '.quiz-item'
                });
            });
        });
    </script>
@stop