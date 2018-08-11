@extends('admin/layout')

@section('belowNavbar')
	{!! do_action('admin_dashboard_header') !!}
    <div class="container-fluid" style="background: #262730;">
        <div class="row">
            <div class="col-md-12" style="padding-top: 10px;padding-bottom: 10px;">
                <h4>
                    <a href="{{Config::get('appMeta.scriptHomepageLink')}}" target="_blank" style="color: #ffffff;">{{Config::get('appMeta.name')}} <small style="color: inherit;">v</small>{{Config::get('appMeta.version')}}</a>
                    &nbsp;
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Help
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="scriptHelpDropdown">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{Config::get('appMeta.documentationLink')}}" target="_blank">Documentation</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{Config::get('appMeta.supportLink')}}" target="_blank">Support</a></li>

                        </ul>
                    </div>
                </h4>
                <div class="update-checker"></div>
                @if(!empty($installFileExistsError))
                    <br/>
                    <div class="alert alert-danger">
                        <b>Important:</b> {{$installFileExistsError}}
                    </div>
                @endif

            </div>

            <script>
                (function(){
                    var objectName = 'socioquiz-update-check-widget';
                    window[objectName] = {
                        showUpdateCheck: function (container) {
                            var containerId = 'updateChecker-' + (new Date()).getTime();
                            container.setAttribute('id', containerId);
                            container.innerHTML = '<iframe style="width:100%; height: 1.5em;" src="{{Config::get('appMeta.updateCheckUrl')}}?my-version={{Config::get('appMeta.version')}}&elm=' + encodeURIComponent('#' + containerId) +  '" frameborder="0"></iframe>';
                            window.addEventListener('message', function(e){
                                var data = e.data;
                                try{
                                    data = JSON.parse(data);
                                    if(data.type == "quizzes-iframe-height-change") {
                                        var containerElement = document.querySelector(data.element);
                                        var iframe = containerElement.children[0];
                                        iframe.style.height = parseInt(data.height) + "px";
                                    }
                                } catch (ex){
                                    console.log(ex.message);
                                }
                            });
                        }
                    };
                    window[objectName].showUpdateCheck($('.update-checker')[0]);
                })();
            </script>

        </div>
    </div>
@stop

@section('content')

    <style>
        .latest-activity-panel .panel-body{
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 20px;
        }
        #activityComparisonChart {
            height: 300px;
        }
        .max-500-scroll {
            height: 500px;
            overflow-y: scroll;
        }
    </style>

	<!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard
                        </h1>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Today's stats</h3>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-pencil-square-o fa-5x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$todayStats['quizzes']}}</div>
														<div>New Quizzes</div>
													</div>
												</div>
											</div>
                                            <a href="{{route('adminViewQuizes')}}">
                                                <div class="panel-footer">
                                                    <span class="pull-left">View Quizzes</span>
                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-green">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-user fa-5x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$todayStats['users']}}</div>
														<div>New Users</div>
													</div>
												</div>
											</div>
                                            <a href="{{route('adminUsersHome')}}">
                                                <div class="panel-footer">
                                                    <span class="pull-left">View Users</span>
                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-yellow">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-check-square fa-5x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$todayStats['attempt']}}</div>
														<div>Quiz Attempts</div>
													</div>
												</div>
											</div>
                                            <a href="{{route('adminViewQuizes')}}">
                                                <div class="panel-footer">
                                                    <span class="pull-left">View Quizzes</span>
                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-red">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-share fa-5x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$todayStats['share']}}</div>
														<div>Quiz social shares</div>
													</div>
												</div>
											</div>
                                            <a href="{{route('adminViewQuizes')}}">
                                                <div class="panel-footer">
                                                    <span class="pull-left">View Quizzes</span>
                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Overall stats</h3>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminViewQuizes')}}" style="display:block;" class="panel panel-primary">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-pencil-square-o fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$overallStats['quizzes']}}</div>
														<div>Quizzes</div>
													</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminUsersHome')}}" style="display:block;" class="panel panel-green">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-user fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$overallStats['users']}}</div>
														<div>Users</div>
													</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-yellow">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-check-square fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$overallStats['attempt']}}</div>
														<div>Quiz Attempts</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<div class="panel panel-red">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-share fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$overallStats['share']}}</div>
														<div>Quiz social shares</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
                <!-- /.row -->
	<div class="row">
		<div class="col-md-12 col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4><i class="fa fa-bar-chart-o"></i> Quiz attempts in the last 30 days</h4>
				</div>
				<div class="panel-body">
					<div id="attemptsChart" style="height: 200px;"></div>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4><i class="fa fa-users"></i> New users in the last 30 days</h4>
				</div>
				<div class="panel-body">
					<div id="newUsersChart" style="height: 200px;"></div>
				</div>
			</div>
		</div>
	</div>

    <!-- Latest activity -->
    <div class="row latest-activity-stats">
        <div class="col-md-5">
            <div class="panel panel-default latest-activity-panel">
                <div class="panel-heading">
                    <h4>Latest activity</h4>
                </div>
                <div class="panel-body">
                    @include('admin.partials.activityFeed', ['latestActivity' => $latestActivity])
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="panel panel-default latest-activity-panel">
                <div class="panel-heading">
                    <h4>Activity stats for the last 30 days</h4>
                </div>
                <div class="panel-body">
                    <div id="activityComparisonChart"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 col-lg-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <h4>New Users</h4>
                </div>
                <div class="panel-body max-500-scroll">
                    @foreach($newUsers as $user)
                        <div class="media">
                            <a class="media-left pull-left" href="#">
                                <img class="img-circle" src="{{ content_url($user->photo) }}" style="width: 50px; height: 50px;">
                            </a>

                            <div class="media-body">
                                <p class="media-heading"><b>{{$user->name}}</b></p>
                                <p class="text-muted">Joined on {{ Helpers::prettyTime($user->created_at, false)}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{route('adminUsersHome')}}">
                    <div class="panel-footer">View all users</div>
                </a>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="panel panel-black">
                <div class="panel-heading">
                    <h4>New quizzes</h4>
                </div>
                <div class="panel-body max-500-scroll" style="background: #F5F5F5;">
                    <div class="x-quizzes-list-widget" data-limit="10" data-stream="latest"></div>
                </div>
                <a href="{{route('adminViewQuizes')}}">
                    <div class="panel-footer">
                        <span class="pull-left">View Quizzes</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <h4>Most Popular quizzes</h4>
                </div>
                <div class="panel-body max-500-scroll" style="background: #F5F5F5;">
                    <div class="x-quizzes-list-widget" data-limit="10" data-stream="popular"></div>
                </div>
                <a href="{{route('adminViewQuizes')}}">
                    <div class="panel-footer">
                        <span class="pull-left">View Quizzes</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        @include('admin.quizes.partials.embedQuizzesListCode')
    </div>

	{!! do_action('admin_dashboard_footer') !!}

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

			new Morris.Line({
				// ID of the element in which to draw the chart.
				element: 'newUsersChart',
				// Chart data records -- each entry in this array corresponds to a point on
				// the chart.
				data: {{$last30DaysUserRegistrations}},
				// The name of the data record attribute that contains x-values.
				xkey: 'date',
				// A list of names of data record attributes that contain y-values.
				ykeys: ['registrations'],
				// Labels for the ykeys -- will be displayed when you hover over the
				// chart.
				labels: ['Registrations'],
				resize: true
			});

            // Area Chart
            Morris.Area({
                element: 'activityComparisonChart',
                data: {{$last30DaysActivities}},
                xkey: 'date',
                ykeys: ['completion', 'like', 'share', 'comment'],
                labels: ['Completions', 'Likes', 'Shares', 'Comments'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });

        })
	</script>
@stop