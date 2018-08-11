<div class="row quiz-items-row @if(!empty($paginate)) with-pagination @endif">
	@forelse ($quizes as $quiz)
		@include('quizes/quizItem')
	@empty
		<div class="col-md-12">
            <p class="text-center">{{__('noQuizzesYet')}}</p>
        </div>
	@endforelse
</div>
@if(!empty($paginate))
    <div class="text-center pagination-container @if(!empty($paginate) && QuizController::isInfiniteScrollEnabled()) with-infinite-scroll @endif">
        {{ $quizes->render() }}
    </div>
@endif

@if(!empty($paginate) && QuizController::isInfiniteScrollEnabled())
    <script src="{{assetWithCacheBuster('bower_components/jquery-infinite-scroll/jquery.infinitescroll.min.js')}}"></script>
    <script>
        $(function(){
            var containerSelector = '.main-content-col .quiz-items-row:first';
            var $container = $(containerSelector);
            $('.main-content-col ul.pager:first').hide();
            $container.infinitescroll({
                        navSelector  : '.main-content-col .pagination-container.with-infinite-scroll ul.pager:first',    // selector for the paged navigation
                        nextSelector : '.pager li:nth-child(2) a',  // selector for the NEXT link (to page 2)
                        itemSelector : containerSelector + ' .quiz-item',     // selector for all items you'll retrieve
                        loading: {
                            finishedMsg: __('noMoreItemsToLoad'),
                            img: '{{asset('images/loading.gif')}}',
                            msgText: __('loadingNextSetOfItems')
                        }
                    },
                    // call masonry as a callback
                    function( newElements ) {
                        $container.masonry('appended', $(newElements));
                        imagesLoaded($container, function() {
                            $container.masonry('layout');
                        });
                    }
            );

        });
    </script>
@endif