<?php

class AdminUsersController extends BaseController {
	public static $perPage = 20;
	public static $sortableFields = array('created_at', 'referrals'); 
	public static function processSort(){
		$sort = Input::get('sort') ? Input::get('sort') : 'created_at';
		$sortType = (Input::get('sortType') === 'asc') ? 'asc' : 'desc';
		
		if(!in_array($sort, self::$sortableFields)) {
			Response::notFound();
		}
		View::share(array(
			'sort' => $sort,
			'sortType' => $sortType
		));
		if($sort == 'created_at') {
			$sort = 'users.created_at';
		}
		return array('sort' => $sort, 'sortType' => $sortType);
	}
	public static function addSerialNumbers(&$users){
		$slNo = $users->firstitem();
		foreach($users as $user) {
			$user->slNo = $slNo;
			$slNo++;
		}
	}
	
	public static function getUsersQueryByActivity($quiz, $activityType, $options = array(), $readAll = false) {
		$sortOptions = self::processSort();
		switch($activityType) {
			case 'attempted':
				$usersQuery = $quiz->attemptedUsers();
				break;
			case 'finished':
				$usersQuery = $quiz->completedUsers();
				break;
			case 'got-result':
				if(empty($options['resultId']))
					throw new Exception('Result Id not passed');
				$usersQuery = $quiz->usersWhoGotResult($options['resultId']);
				break;
			case 'chosen-answer':
				if(empty($options['questionId']) || empty($options['answerId']))
					throw new Exception('Result Id not passed');
				$usersQuery = $quiz->usersWhoAnswered($options['questionId'], $options['answerId']);
				break;
			case 'liked':
				$usersQuery = $quiz->likedUsers();
				break;
			case 'shared':
				$usersQuery = $quiz->sharedUsers();
				break;
			case 'commented':
				$usersQuery = $quiz->commentedUsers();
				break;
			default:
				throw new Exception('Invalid activity type');
		}
		//dd(DB::getQueryLog());
		return $usersQuery->with('profiles')->select('users.*')->leftJoin('user_referrals', 'users.id', '=', 'user_referrals.user_id')->select('referrals');
	}
	
	public static function downloadEmails($usersQuery) {
		
		$usersQuery = $usersQuery->select(array('email', 'name'));
		if(Input::get('downloadLimit')) {
			$usersQuery = $usersQuery->take(Input::get('downloadLimit'));
		}
		$users = $usersQuery->get();
		$downloadData = array();
		foreach($users as $user) {
			$userRow = Input::get('includeName') ? '"' . $user->name . '",' : '';
			$userRow .= $user->email;
			$downloadData[] = $userRow;
		}
		if(Input::get('displayOnScreen') == "true")
			return Response::make(implode("<br>", $downloadData));
		else {
			return Response::make(implode("\n", $downloadData))->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="emails.csv"');
		}
	}
	
	public function index() {
		$sortOptions = self::processSort();
		$usersQuery = User::with('profiles')->select('users.*', 'referrals')->leftJoin('user_referrals', 'users.id', '=', 'user_referrals.user_id')->orderBy($sortOptions['sort'], $sortOptions['sortType']);
		if(Input::get('download')) {
			return self::downloadEmails($usersQuery);
		}
		$users = $usersQuery->paginate(self::$perPage);
		//dd(DB::getQueryLog());
		self::addSerialNumbers($users);
		//dd($users->toArray());
		View::share(array(
			'users' => $users
		));
		return View::make('admin/users/index')->with(array(
			'currentPage' => 'usersIndex'
		));
	}
	public function quizUsers(){
		try {
			$sortOptions = self::processSort();
			$activityType = Input::get('activityType');
			$quizId = Input::get('quizId');
			$users = null;
			if($quizId) {
				$quiz = Quiz::findOrFail($quizId);
				$quizStats = $quiz->stats;
				$quizResultsDistribution = array();
				$quizResultsDistribution = $quiz->resultDistribution();
				if($activityType) {
					$activityOptions = array();
					if($activityType == 'got-result') {
						$activityOptions = array('resultId' => Input::get('resultId'));
					} else if($activityType == 'chosen-answer') {
						$activityOptions = array('questionId' => Input::get('questionId'), 'answerId' => Input::get('answerId'));
					}
					$usersQuery = self::getUsersQueryByActivity($quiz, $activityType, $activityOptions);
					$usersQuery = $usersQuery->orderBy($sortOptions['sort'], $sortOptions['sortType']);
					if(Input::get('download')) {
						return self::downloadEmails($usersQuery);
					}
					$users = $usersQuery->paginate(self::$perPage);
                    $users->appends(Input::get());
					//dd(DB::getQueryLog());
					//dd($users->toArray());
					self::addSerialNumbers($users);
				}
				$results = $quiz->results;
				$questions = $quiz->questions;
				$quizShareRate = $quizLikeRate = 0;
				if($quizStats && !empty($quizStats->attempts)) {
					$quizShareRate =  number_format(($quizStats->shares / $quizStats->attempts) * 100, 2);
					$quizLikeRate =  number_format(($quizStats->likes / $quizStats->attempts) * 100, 2);
				}

				$last30DaysAttempts = AdminController::getLastNDaysActivity(30, 'attempt', $quiz);
				View::share(array(
					'users' => $users,
					'quizId' => $quizId,
					'quiz' => $quiz,
					'quizResults' => $results,
					'quizQuestions' => $questions,
					'quizStats' => $quizStats,
					'quizShareRate' => $quizShareRate,
					'quizLikeRate' => $quizLikeRate,
					'last30DaysAttempts' => json_encode($last30DaysAttempts),
					'quizResultsDistribution' => $quizResultsDistribution
				));
			} else {

			}
			View::share(array(
				'activityType' => $activityType,
				'resultId' => Input::get('resultId'),
				'questionId' => Input::get('questionId'),
				'answerId' => Input::get('answerId')
			));
			QuizController::_loadQuizes([
                'limit' =>  10000
            ]);
			return View::make('admin/users/quizUsers')->with(array(
				'currentPage' => 'quizUsers'
			));
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Quiz not found');
		}catch(Exception $e) {
			return Response::make($e->getMessage(), 400);
		}
	}
}