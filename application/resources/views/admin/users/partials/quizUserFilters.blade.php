
<div class="col-md-8">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Filter users</h3>
		</div>
		<div class="panel-body">
			<form id="userFilterForm" action="{{route('adminQuizUsers')}}" class="form-horizontal">
				<div class="form-group">
					<label class="col-md-4 control-label" for="">Choose quiz</label>
					<div class="col-md-8">
						<select name="quizId" id="chooseQuizDropdown" class="form-control">
							@foreach($quizes as $q)
								<option @if($q->id == @$quizId) selected="selected" @endif value="{{$q->id}}">{{$q->topic}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-md-4 control-label">Show users who:</label>
					<div class="col-md-8">
						<select name="activityType" id="userActionSelect" class="form-control">
							<option @if($activityType == 'attempted') selected="selected" @endif value="attempted">Attempted</option>
							<option @if($activityType == 'finished') selected="selected" @endif value="finished">Finished</option>
							<option @if($activityType == 'got-result') selected="selected" @endif value="got-result">Got the result</option>
							<option @if($activityType == 'chosen-answer') selected="selected" @endif value="chosen-answer">Has chosen an answer</option>
							<option @if($activityType == 'liked') selected="selected" @endif value="liked">Liked</option>
							<option @if($activityType == 'shared') selected="selected" @endif value="shared">Shared</option>
							<option @if($activityType == 'commented') selected="selected" @endif value="commented">Commented</option>
						</select>
					</div>
				</div>
				@if(!empty($quizResults))
					<div class="form-group form-sub-options action-filter hide" id="chooseResultFilter">
						<label for="" class="col-md-4 control-label">Choose result</label>
						<div class="col-md-8">
							<select name="resultId" id="" class="form-control">
								@foreach($quizResults as $result)
									<option @if($result->id == $resultId) selected="selected" @endif value="{{ $result->id }}">{{ $result->title }}</option>
								@endforeach
							</select>
						</div>
					</div>
				@endif
				@if(!empty($quizQuestions))
					<div class="form-group form-sub-options action-filter hide" id="chooseAnswerFilter">
						<label for="" class="col-md-4 control-label">Choose question</label>
						<div class="col-md-8">
							<select name="questionId" id="userActionQuestionSelect" class="form-control">
								@foreach($quizQuestions as $question)
									<option @if($question->id == $questionId) selected="selected" @endif value="{{ $question->id }}">{{ $question->question }}</option>
								@endforeach
							</select>
						</div>
					</div>
				@endif
				<div class="form-group form-sub-options action-question-filter hide" id="chooseAnswerChoiceFilter">
					<label for="" class="col-md-4 control-label">Choose choice/answer</label>
					<div class="col-md-8">
						<select name="answerId" id="userActionAnswerSelect" class="form-control">

						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-offset-4 col-md-4">
						<input type="submit" value="Show users" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
	<script>
		@if(!empty($quizQuestions))
			var QuizData = {{ json_encode($quiz) }};
		@else
			var QuizData = {};
		@endif
		$(function(){
			function showFilterFormSpinner() {
				var spinner = new Spinner().spin();
				$('#userFilterForm').append(spinner.el);
			}
			$('#chooseQuizDropdown').change(function(){
				var quizId = $(this).val();
				var url = new URI(window.location.href);
				url.removeSearch('quizId').addSearch({'quizId' : quizId});
				showFilterFormSpinner();
				window.location.href = url;
			});

			$('#userActionSelect').change(function(){
				var action = $(this).val();
				/*if(!$('.form-sub-options').find('select>option[selected="selected"]').length) {
					$('.form-sub-options').find('select>option:first-child').prop('selected', true);
				} else {
					$('.form-sub-options').find('select>option[selected="selected"]').prop('selected', true);
				}*/
				
				$('.action-filter,.action-question-filter').addClass('hide');
				if(action == 'got-result') {
					$('#chooseResultFilter').removeClass('hide');
				} else if(action == 'chosen-answer') {
					$('#chooseAnswerFilter').removeClass('hide');
					$('#chooseAnswerChoiceFilter').removeClass('hide');
					$('#userActionQuestionSelect').change();
				}
				//Set values of fields in hidden sub-forms to null
				//$('.form-sub-options.hide').find('input,select').val(null);
			});
			
			$('#userActionQuestionSelect').change(function(){
				var questionId = $(this).val();
				var question = _.findWhere(QuizData.questions, {id : questionId});
				var answers = question.choices,
					answerSelect = $('#userActionAnswerSelect');
				answerSelect.children().remove();
				for(var i in answers) {
					answerSelect.append('<option '+ ((answers[i].id == '{{$answerId}}') ? 'selected="selected"' : '') +'value="'+ answers[i].id +'">'+ answers[i].title +'</option>');
				}
			});
			$('#userActionSelect').change();
		});
	</script>