<?php
class QuizStats extends Eloquent {
	protected $table = "quiz_stats";
	protected $fillable = array('quiz_id', 'attempts', 'completions', 'shares', 'likes');
	protected $primaryKey = 'quiz_id';
	public $timestamps = false;
}