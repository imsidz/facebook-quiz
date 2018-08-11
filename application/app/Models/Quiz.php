<?php
class Quiz extends Eloquent {
	protected $table = "quizes";
	public static $snakeAttributes = false;
	/*public static function decodeQuizJson($quiz)
    {
        $quiz->questions = json_decode($quiz->questions);
		$quiz->results = json_decode($quiz->results);
		$quiz->ogImages = json_decode($quiz->ogImages);
		$quiz->settings = json_decode($quiz->settings);
		return $quiz;
    }*/
	public function getActiveAttribute($value) {
		return ($value ? true : false);
	}
	public function getQuestionsAttribute($value) {
		return json_decode($value);
	}
	public function getResultsAttribute($value) {
		return json_decode($value);
	}
	public function getOgImagesAttribute($value) {
		return json_decode($value);
	}
	public function getSettingsAttribute($value) {
		return json_decode($value);
	}
	public function stats(){
		return $this->hasOne('QuizStats');
	}
	public function users(){
		return $this->belongsToMany('User', 'quiz_user_activity');
	}
	public function attemptedUsers() {
		return $this->users()->wherePivot('type', 'attempt');
	}
	public function completedUsers() {
		return $this->users()->wherePivot('type', 'completion');
	}
	public function sharedUsers() {
		return $this->users()->wherePivot('type', 'share');
	}
	public function likedUsers() {
		return $this->users()->wherePivot('type', '=', 'like');
	}
	public function commentedUsers() {
		return $this->users()->wherePivot('type', 'comment');
	}
	public function usersWhoGotResult($resultId) {
		return $this->belongsToMany('User', 'quiz_user_results')->wherePivot('result_id', $resultId);
	}
	public function usersWhoAnswered($questionId, $answerId) {
		return $this->belongsToMany('User', 'quiz_user_answers')->wherePivot('question_id', $questionId)->wherePivot('answer_id' , $answerId);
	}
	public function resultDistribution(){
		$data = QuizUserResults::groupBy('quiz_id', 'result_id')->having('quiz_id', '=', $this->id)->select(DB::raw('count(*) as count'), 'result_id', 'quiz_id')->get()->toArray();
		$resultDistribution = array();
		foreach($data as $result) {
			$resultDistribution[$result['result_id']] = $result['count'];
		}
		return $resultDistribution;
	}
	public function scopeJoinStats($query){
		return $query->leftJoin('quiz_stats', 'quizes.id', '=', 'quiz_stats.quiz_id');
	}

    public function scopeSearch($query, $q) {
        return empty($q) ? $query : $query->where('topic', 'LIKE', "%$q%")->get();
    }
}