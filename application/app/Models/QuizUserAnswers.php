<?php
class QuizUserAnswers extends Eloquent {
	protected $table = "quiz_user_answers";
	protected $fillable = array('quiz_id', 'user_id', 'question_id', 'answer_id');
}