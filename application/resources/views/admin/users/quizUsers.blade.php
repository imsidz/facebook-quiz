@extends('admin/layout')


@section('content')
    <style>
        .huge {
            font-size: 30px;
        }
    </style>
    <h1>Quiz users and stats</h1>
    <hr/>
    @if(!empty($quiz))
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Quiz</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <img src="{{ content_url($quiz->image) }}" alt="" width="100%" style="width: 100%;"/>
                            </div>
                            <div class="col-md-9">
                                <h2>Quiz: <a target="_blank" href="{{ QuizHelpers::viewQuizUrl($quiz)}}">{{$quiz->topic}}</a></h2>
                                <a target="_blank" class="btn btn-success" href="{{ QuizHelpers::viewQuizUrl($quiz)}}">View Quiz</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart-o"></i> Attempts in the last 30 days</h4>
                    </div>
                    <div class="panel-body">
                        <div id="attemptsChart" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart-o"></i> Result distribution</h4>
                    </div>
                    <div class="panel-body">
                        <div id="resultsDistributionChart" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function(){
                new Morris.Line({
                    // ID of the element in which to draw the chart.
                    element: 'attemptsChart',
                    // Chart data records -- each entry in this array corresponds to a point on
                    // the chart.
                    data: {{$last30DaysAttempts}},
                    // The name of the data record attribute that contains x-values.
                    xkey: 'date',
                    // A list of names of data record attributes that contain y-values.
                    ykeys: ['attempt'],
                    // Labels for the ykeys -- will be displayed when you hover over the
                    // chart.
                    labels: ['Attempts'],
                    resize: true
                });
            })
        </script>
        <script>
            $(function(){
                var quizResultsDistribution = {{json_encode($quizResultsDistribution)}};
                var quizResults = {{json_encode($quizResults)}};
                var resultsDistributionChartData = [],
                        titleMaxLength = 20;
                for(var i in quizResults){
                    resultsDistributionChartData.push({
                        label: (quizResults[i].title.length > titleMaxLength) ? quizResults[i].title.substr(0, titleMaxLength) + '...' : quizResults[i].title,
                        value: quizResultsDistribution.hasOwnProperty(quizResults[i]['id']) ? quizResultsDistribution[quizResults[i]['id']] : 0
                    });
                }
                new Morris.Donut({
                    element: 'resultsDistributionChart',
                    data: resultsDistributionChartData,
                    colors: ['#3BD3F7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
                    resize: true
                });
            });
        </script>
        @if(!empty($quizStats))
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Stats</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-user fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizStats->attempts}}</div>
                                            <div>Attempts</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-check-square fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizStats->completions}}</div>
                                            <div>Completions</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-share-alt-square fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizStats->shares}}</div>
                                            <div>Shares</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-thumbs-up fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizStats->likes}}</div>
                                            <div>Likes</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-comments fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizStats->comments}}</div>
                                            <div>Comments</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-share-alt-square fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizShareRate}}%</div>
                                            <div>Share rate</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-yellow">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-thumbs-up fa-3x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">{{$quizLikeRate}}%</div>
                                            <div>Like rate</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <br/>
    @endif

    <h2>Quiz users</h2>
    <hr/>
    <div class="row">
        @include('admin.users.partials.quizUserFilters')
        @include('admin.users.partials.userDownloadOptions')
    </div>
    @include('admin.users.partials.userList')
@stop