<?php
class QuizUserActivity extends Eloquent {
	protected $table = "quiz_user_activity";
	protected $fillable = array('quiz_id', 'user_id', 'type');

    public function quiz()
    {
        return $this->belongsTo('Quiz');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

	public function scopeOfType($query, $activityType){
		return $query->whereType($activityType);
	}
	public function scopeOfQuiz($query, $ofQuiz){
		return $query->whereQuizId($ofQuiz);
	}
}