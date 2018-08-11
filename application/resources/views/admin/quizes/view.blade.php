@extends('admin/layout')


@section('content')

	<style>
		.share-rate-low {
			color: #999;
		}
		.share-rate-medium {
			color: #87b45e;
		}
		.share-rate-fair {
			color: #ed6300;
		}
		.share-rate-high {
			color: #ef1a00;
		}
	</style>


<h2 class="page-header">
	Viewing quizzes
</h2>

<div class="row">
	<div class="col-md-12">
		@if($search)
			<h4>Searching for <small>"{{$search}}"</small></h4>
		@endif
		<div class="table-responsive">
		@if(!empty($quizes))
			<table class="table table-bordered table-striped">
				<tr>
					<td colspan="42" class="active">
						<form class="form-inline" action="{{route('adminViewQuizes')}}">
                            <a class="btn btn-success" href="{{route('adminCreateQuiz')}}"><i class="fa fa-plus"></i> Create new Quiz</a> &nbsp;
							<div class="form-group">
								<input type="text" name="search" class="form-control" id="searchField" placeholder="Search quizzes" value="{{$search}}">
							</div>
							<input type="submit" class="btn btn-default" value="Search"/>
							<?php do_action('admin_quizzes_view_page_actions', get_defined_vars()) ?>
						</form>
					</td>
				</tr>
				<tr class="">

					<th style="width: 50px;">Photo</th>
					<th>Topic</th>
					<th>
						<a @if($sort === 'created_at') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'created_at', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Created on @if($sort === 'created_at')

								@if($sortType === 'asc')
									<i class="fa fa-caret-up">
										@else
											<i class="fa fa-caret-down">
												@endif
											</i>
								@endif
						</a>
					</th>
					<th>
						<a @if($sort === 'created_at') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'shareRate', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Sharing rate @if($sort === 'shareRate')

								@if($sortType === 'asc')
									<i class="fa fa-caret-up">
										@else
											<i class="fa fa-caret-down">
												@endif
											</i>
								@endif
						</a>
					</th>
					<th>Active</th>
					<th>Actions</th>
				</tr>
		@forelse (@$quizes as $quiz)
			<tr>

				<td><a target="_blank" href="{{ QuizHelpers::viewQuizUrl($quiz)}}"><img src="{{ content_url($quiz->image) }}" alt="" width="120"></a></td>
				<td width="40%"><h5 style="line-height: 1.5em;"><a style="color: #333;" target="_blank" href="{{ QuizHelpers::viewQuizUrl($quiz)}}">{{$quiz->topic}}</a></h5></td>
				<td>{{ Helpers::prettyTime($quiz->created_at, false)}}</td>
				<td><h4 style="margin-top: 0px;" class="share-rate-{{$quiz->shareRateRange}}">{{$quiz->shareRate}}%</h4>
					<div class="progress" style="height: 1em;">
						<div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{{$quiz->shareRate}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$quiz->shareRate}}%">
						</div>
					</div>
				</td>
				<td>@if($quiz->active)
					<div class="label label-success">Active</div>
				@else
					<div class="label label-danger">Inactive</div>
				@endif
				</td>
				<td>
					<a href="{{ route('adminCreateQuiz', array('action' => 'edit', 'quizId' => $quiz->id)) }}" class="btn btn-sm btn-success" role="button" data-toggle="tooltip" title="Edit quiz"><i class="fa fa-edit"></i></a>
					<a href="{{route('adminQuizUsers')}}?quizId={{$quiz->id}}" class="btn btn-sm btn-warning" role="button" data-toggle="tooltip" title="View users"><i class="fa fa-users"></i></a>
					<a href="javascript:void(0)" data-quiz-id="{{$quiz->id}}" class="btn btn-sm btn-danger quiz-delete-btn" role="button" title="Delete quiz" data-toggle="tooltip" title="Delete quiz"><i class="fa fa-times"></i></a>
                    <a href="{{ route('adminCreateQuiz', array('action' => 'create', 'duplicate-quiz' => $quiz->id)) }}" class="btn btn-sm btn-info" role="button" data-toggle="tooltip" title="Duplicate quiz"><i class="fa fa-copy"></i></a>
					<!--<a href="#" class="btn btn-danger" role="button"><i class="fa fa-trash-o"></i></a>-->
				</td>
			</tr>
		@empty
			<tr><td colspan="42" class="text-center">
					<br/>
					@if($search)
						<div class="alert alert-info"><b>No quizzes matching "{{$search}}"</b></div>
					@else
						<div class="alert alert-info"><b>No quizzes yet!</b></div>
					@endif
				</td></tr>
		@endforelse
	</table>
		</div>
		<br>{{ $quizes->render() }}
	@endif
	</div>
	<script>
		$(function(){
			$('body').on('click', '.quiz-delete-btn', function(){
				var quizId = $(this).data('quizId');
				dialogs.confirm("Are you sure to delete the quiz?", function(confirm){
					if(confirm){
						$.post('{{route('adminDeleteQuiz')}}', {
							quizId : quizId
						}).success(function (res) {
							if (res.success) {
								dialogs.success('Quiz Deleted', function () {
									window.location.href = '{{route('adminViewQuizes')}}';
								});
							} else if (res.error) {
								dialogs.error('Error occured\n' + res.error);
							} else {
								dialogs.error('Some Error occured');
							}
						}).fail(function (res) {
							dialogs.error(res.responseText);
						});
					}
				})
			});
		})


        $(function(){
           $('[data-toggle="tooltip"]').tooltip();
        });
	</script>
</div>

@stop