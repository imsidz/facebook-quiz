<div class="sharing-buttons-block @if($showEmbedCode) embed-code-enabled @endif">
    <div class="row sharing-buttons-set">
    @foreach($sharingNetworks as $index => $network)
        @if($index == 0 || $index == 1)
            <div class="@if($showEmbedCode) col-md-4 col-xs-4 @else col-md-5 col-xs-5  @endif share-quiz-button-box">
                <a class="btn btn-block btn-social-{{$network}} social-sharing-btn" data-social-network="{{$network}}" data-url="{{QuizHelpers::viewQuizUrl($quiz)}}" data-title="{{{$quiz->topic}}}" data-media="{{QuizHelpers::getOgImage($quiz)}}" href="#">
                    <span>
                        <i class="fa fa-{{$sharingNetworkIcons[$network]}} pull-left"></i>
                        <div class="hidden-sm hidden-xs pull-left">
                            {{__('shareOn' . $network)}}
                        </div>
                        <div class="hidden-xs hidden-md hidden-lg pull-left">
                            @if($network == 'twitter')
                                {{__('tweet')}}
                            @else
                                {{__('share')}}
                            @endif
                        </div>
                    </span>
                </a>
            </div>
            @if($index == 1)
                <div class="col-md-2 col-xs-2 share-quiz-more-btn">
                    <a class="btn btn-block btn-black" href="#">
                        <span><i class="fa fa-plus"></i></span>
                    </a>
                </div>
                @if($showEmbedCode)
                    <div class="col-md-2 col-xs-2 embed-quiz-btn">
                        <a class="btn btn-block btn-white" id="embedThisQuizBtn" href="#" data-action="embed-quiz">
                            <span><i class="fa fa-code"></i>
                                <div class="hidden-sm hidden-xs hidden-md inline-block">&nbsp; {{__('embed')}}</div>
                            </span>
                        </a>
                    </div>
                @endif
            </div>
            @endif
        @endif
        @if($index == 1 || $index == 6)
            @if($index == 6)
                </div>
            @endif
            <div class="row more-sharing-buttons-set">
        @endif
        @if($index > 1)
             <div class="col-md-3 col-xs-3 share-quiz-button-box">
                 <a class="btn btn-block btn-social-{{$network}} social-sharing-btn" data-social-network="{{$network}}" data-url="{{QuizHelpers::viewQuizUrl($quiz)}}" data-title="{{{$quiz->topic}}}" data-media="{{QuizHelpers::getOgImage($quiz)}}" href="#">
                     <span><i class="fa fa-{{$sharingNetworkIcons[$network]}}"></i></span>
                 </a>
             </div>
        @endif
    @endforeach
    </div>
</div>

<script>
    $(function() {
        var body = $('body');
        var shareButtonsBlocks = $('.sharing-buttons-block');
        shareButtonsBlocks.each(function() {
            if($(this).data('initiated'))
                return;
            var shareButtonsBlock = $(this);
            $(this).find('.share-quiz-more-btn').click(function(e) {
                shareButtonsBlock.toggleClass('show-more');
                e.preventDefault();
                return false;
            });
            $(this).data('initiated', true);
        });
    });
</script>

<div id="embedThisQuizModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{__('closeBtn')}}</span></button>
                <h4 class="modal-title">{{__("embed")}}</h4>
            </div>
            <div class="modal-body">
                <br>
                <p><strong>{{__('copyCodeBelowToEmbedQuiz')}}</strong></p>
                <textarea name="" id="quizEmbedCode" style="width: 100%;" rows="5">@include('partials.quizEmbedCode')</textarea>
                <script>
                    $('#quizEmbedCode').focus(function() {
                        $(this).select();
                    });
                </script>
                <br><br>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $('body').on('click', '[data-action="embed-quiz"]', function(e) {
        $('#embedThisQuizModal').modal('show');
        e.preventDefault();
    });
</script>