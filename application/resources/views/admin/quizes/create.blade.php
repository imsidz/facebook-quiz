@extends('admin/layout')

@section('head')
    @parent
    {!! do_action('admin_create_post_page_head') !!}
@stop

@section('content')

    <h1 class="page-header">
        @if($creationMode) Create a @elseif($editingMode) Edit @endif
        quiz</h1>

    @if(!empty($duplicateQuiz))
        <div class="alert alert-success">Duplicating quiz <b>"{{$duplicateQuiz->topic}}"</b></div>
    @endif

    <div class="row">
        <div  class="col-md-8" id="quizEditorLoading">
            <br/><p class="text-center"><b>Loading quiz editor</b></p>
            <div id="quizEditorLoadingSpinner" style="position: relative; margin-top: 10px; height: 80px;"></div>
            <br/>
        </div>
    </div>
    {!! do_action('admin_create_post_above_editor') !!}
    <div class="row">
        <div class="col-md-8 hidden" id="quizEditor">
            <div role="tabpanel">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist" id="tabLinks">
                </ul>
                <!-- Tab panes -->
                <div class="tab-content" id="tabContents">

                </div>

            </div>
            <nav class="navbar navbar-default" style="border-top-left-radius: 0px;border-top-right-radius: 0px;border-top: 0px;">
                <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <p class="navbar-text">
                            Quick links&nbsp;
                            <i class="fa fa-chevron-right"></i>
                        </p>
                        <ul class="nav navbar-nav" role="tablist" id="footerTabLinks">

                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
            <br/>

            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <i style="color: #ffb100;" class="fa fa-3x fa-lightbulb-o pull-left"></i><b>Click "Save the quiz" button</b>
                        <p class="small">to save the quiz to database!</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn btn-red btn-block btn-lg save-quiz-btn" style="padding-top: 22px;padding-bottom: 22px;">Save the quiz</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 pull-right hidden-sm hidden-xs">
            <div class="alert alert-success text-center save-quiz-hint-side-block" data-spy="affix" data-offset-top="85" >
                <div class="text-center">
                    <i style="color: #ffb100;" class="fa fa-5x fa-lightbulb-o"></i>
                </div>
                <h2>Save the quiz to Database here</h2>
                <p>You have to click the "Save Quiz" button below to save the changes to Database. Otherwise all the changes you made will be discarded.</p>
                <br/>
                <div class="btn btn-lg btn-red save-quiz-btn">Save Quiz</div>
            </div>
        </div>
    </div>

    {!! do_action('admin_create_post_below_editor') !!}

    <div class="row embed-code-section">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Embed this quiz</div>
                </div>
                <div class="panel-body">
                    <h4>Copy the code below to your website to embed this quiz</h4>
                    <textarea name="" id="quizEmbedCode" style="width: 100%;" rows="5">@include('partials.quizEmbedCode')</textarea>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#quizEmbedCode').focus(function() {
            $(this).select();
        });
    </script>


    @if(!empty($quiz))
        <div class="row">
            <div class="col-md-8" style="margin-top: 20px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">Quiz Preview urls</div>
                            </div>
                            <div class="panel-body">
                                <form class="form-inline">
                                    <div class="form-group" style="margin-top: 10px;">
                                        <label for="" class="control-label"><a
                                                    href="{{ route('viewQuiz', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id))}}">View
                                                Quiz</a></label><br>
                                        <input class="form-control preview-url-field" style="width: 400px; max-width: 100%" type="text"
                                               value="{{ htmlspecialchars(rawurldecode(route('viewQuiz', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id))))}}">
                                        <a href="https://developers.facebook.com/tools/debug/og/object/?q={{ urlencode(route('viewQuiz', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id)))}}&rescrape=true"
                                           target="_blank" class="btn btn-default">Refresh OG data</a>
                                    </div>
                                </form>
                                @if(!empty($quiz->results))
                                    <h3>Results</h3>
                                    <form class="form-inline">
                                        @foreach($quiz->results as $result)
                                            <div class="form-group" style="margin-top: 10px;">
                                                <label for="" class="control-label"><a
                                                            href="{{ route('viewQuizResultLandingPage', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id, 'resultId' => $result->id))}}">{{$result->title}}</a>
                                                </label><br>
                                                <input class="form-control preview-url-field"
                                                       style="width: 400px; max-width: 100%" type="text"
                                                       value="{{ htmlspecialchars(rawurldecode(route('viewQuizResultLandingPage', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id, 'resultId' => $result->id))))}}">
                                                <a href="https://developers.facebook.com/tools/debug/og/object/?q={{ urlencode(route('viewQuizResultLandingPage', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id, 'resultId' => $result->id)))}}&rescrape=true"
                                                   target="_blank" class="btn btn-default">Refresh OG data</a>
                                            </div>
                                        @endforeach
                                    </form>
                                @endif
                                <br/><div class="alert alert-success">
                                    <p>If you change the og image after publishing the quiz, Facebook may not update the image automatically because it caches the Open graph image the first time a user shares it.</p>
                                    <p><strong>So if you need to update the cached open graph data click on "debug og tags" and use the interface provided by facebook</strong></p>
                                    <p><a href="https://developers.facebook.com/tools/debug/og/object/" target="_blank" class="btn btn-primary">Debug OG data</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        var isCreationMode = @if($creationMode) true @else false @endif;
        var isEditingMode = @if($editingMode) true @else false @endif;
        $('.preview-url-field').focus(function () {
            $(this).select();
        });
        $('.preview-url-field').mouseup(function () {
            return false;
        });
        $('.save-quiz-btn').click(function () {
            var action = @if($creationMode) 'create'
            @elseif($editingMode)
                    'edit' @endif;
            var params = {
                quiz: QuizData
            };
            if(QuizData['id'])
                params.quizId = QuizData['id'];
            $.post(BASE_PATH + '/admin/quizes/create?action=' + action , params).success(function (res) {
                if (res.success) {
                    lastSavedQuizDataJson= JSON.stringify(QuizData);
                    if(!QuizData.id) {
                        //Is a new quiz - go to viewQuizzes page
                        window.location.href = '{{route('adminViewQuizes')}}';
                        return
                    }
                    bootbox.dialog({
                        message: '<h3 class="text-center text-success">Quiz successfully saved!</h3><br>',
                        title: "Quiz saved",
                        buttons: {
                            moveOn: {
                                label: "Show all quizzes!",
                                className: "btn-success",
                                callback: function () {
                                    window.location.href = '{{route('adminViewQuizes')}}';
                                }
                            },
                            stayBack: {
                                label: "Close",
                                className: "btn-default",
                                callback: function () {

                                }
                            }
                        }
                    });
                } else if (res.error) {
                    dialogs.error('Error occured\n' + res.error);
                } else {
                    dialogs.error('Some Error occured');
                }
            }).fail(function (res) {
                dialogs.error(res.responseText);
            });
        })
    </script>

    <script>
        var Schemas = {};

        var QuizData = {!! $quizData !!};
                @if($quiz)
        var QuizMeta = {
                    viewQuizUrl: '{{QuizHelpers::viewQuizUrl($quiz)}}'
                };
                @endif
                @unless($quiz)
        var QuizMeta = {};
        @endunless

        var QuizConfig = {!! json_encode($config['quiz']) !!};
        //If forceLogin not set for the quiz, use the global config for forceLogin
        if (!QuizData.settings || !QuizData.settings.forceLogin) {
            QuizData.settings = {};
            QuizData.settings.forceLogin = '{{ $config['quiz']['forceLogin'] }}';
        }

        function touchupQuizData() {
            QuizData.active = (QuizData.active === 'true' || QuizData.active === true) ? true : false;
            //assign indexes to questions
            for(var i in QuizData.questions) {
                //QuizData.questions[i].id = i;
                QuizData.questions[i].skippable = (QuizData.questions[i].skippable === 'true' || QuizData.questions[i].skippable === true) ? true : false;
                QuizData.questions[i].index = i;
                //assign indexed to choices
                for(var j in QuizData.questions[i].choices) {
                    //QuizData.questions[i].choices[j].id = j;
                    if(QuizData.questions[i].choices[j].correct !== undefined) {
                        QuizData.questions[i].choices[j].correct = (QuizData.questions[i].choices[j].correct === 'true' || QuizData.questions[i].choices[j].correct === true) ? true : false;
                    }
                }
            }
        }
        touchupQuizData();

        var lastSavedQuizDataJson= JSON.stringify(QuizData);

        (function() {
            var quizSchema = {!! $quizSchema or 'null' !!};
            var questionSchema = {!! $questionSchema or 'null' !!};
            var choiceSchema = {!! $choiceSchema or 'null' !!};
            var resultSchema = {!! $resultSchema or 'null' !!};
            var settingsSchema = {!! $settingsSchema or 'null' !!};
            Schemas = {
                'quizSchema': quizSchema,
                'settingsSchema': settingsSchema,
                'questionSchema': questionSchema,
                'choiceSchema': choiceSchema,
                'resultSchema': resultSchema,
                'ogImagesSchema': quizSchema.ogImages
            };
            Schemas.questionSchema.choices.items.properties = choiceSchema;
            delete Schemas.quizSchema.questions;
            delete Schemas.quizSchema.results;
            delete Schemas.quizSchema.ogImages;
            delete Schemas.quizSchema.settingsSchema;
            //Schemas.quizSchema.questions.items.properties = questionSchema;
        })();
    </script>

@stop


@section('foot')
    @parent

    <script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>
    <script src="{{assetWithCacheBuster('js/admin/createQuiz.js')}}"></script>

    @if(App::isLocal())
        <script data-main="{{ assetWithCacheBuster('js/admin/createQuiz/init.js')}}"
                src="{{ assetWithCacheBuster('bower_components/requirejs/require.js')}}"></script>
    @else
        <script data-main="{{ assetWithCacheBuster('js/admin/createQuiz/bundle.min.js')}}"
                src="{{ assetWithCacheBuster('bower_components/requirejs/require.js')}}"></script>
    @endif

    <script>

        window.onbeforeunload = function(){
            var message = isCreationMode ? 'You haven\'t saved this quiz yet. Click "Create quiz" button to save the quiz.' : "You have made some changes in the Quiz that are not saved. You have to click the 'Save the quiz' button at the bottom to save the changes."
            var newQuizDataJson = JSON.stringify(QuizData);
            if(lastSavedQuizDataJson != newQuizDataJson) {
                return message;
            }
        };
    </script>

    <script>
        (function(){
            var spinner = new Spinner().spin();
            $('#quizEditorLoadingSpinner').append(spinner.el);
        })();
        (function () {
            $('body').on('click', '.copy-profile-pic-config-from-global', function () {
                var configToCopy = ['userPicXPos', 'userPicYPos', 'userPicSize'];
                $.each(configToCopy, function(key, val) {
                    $('input[name="settings.'+ val +'"]').val(QuizConfig[val]);
                })
            });
            $(window).on('quiz-editor-tab-shown', function(tabId) {
                $('input[name="settings.userPicXPos"], input[name="settings.userPicYPos"], input[name="settings.userPicSize"]').attr('type', 'number');
                $('<p class="clearfix"><span class="btn btn-info copy-profile-pic-config-from-global">Copy values from global config</span></p>').insertAfter('.form-group.jsonform-error-settings---addUserPicInResults');
            });
        })();

        (function () {
            var beforeRenderJsonFormCallbacks = [];
            window.beforeRenderJsonForm = function(formName, schema, formOptions) {
                for(var i in beforeRenderJsonFormCallbacks) {
                    beforeRenderJsonFormCallbacks[i].call(this, formName, schema, formOptions);
                }
            }
            window.onBeforeRenderJsonForm = function(callback) {
                beforeRenderJsonFormCallbacks.push(callback);
            }
        })();
    </script>

    {!! do_action('admin_create_post_page_foot') !!}
@stop

