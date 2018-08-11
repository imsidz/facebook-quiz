<div class="col-sm-6 col-md-6 quiz-item">
	<div class="thumbnail">
	  <a href="{{ QuizHelpers::viewQuizUrl($quiz)}}"><img data-src="{{ content_url(!empty($quiz->ogImages->main) ? $quiz->ogImages->main .'_thumb.jpg' : $quiz->image)}}" src="{{ content_url(!empty($quiz->ogImages->main) ? $quiz->ogImages->main.'_thumb.jpg' : $quiz->image)}}" alt="..."></a>
	  <div class="caption">
		<a href="{{ route('viewQuiz', array('nameString' => QuizHelpers::getUrlString($quiz->topic), 'quizId' => $quiz->id))}}">
            <div class="quiz-title">{{{ $quiz->topic }}}</div>
        </a>
	  </div>
	</div>
</div>