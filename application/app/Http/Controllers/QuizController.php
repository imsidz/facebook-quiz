<?php

use ResultGenerator\Exceptions\ResultGeneratorException;

class QuizController extends BaseController {
    const DEFAULT_PER_PAGE_LIMIT = 10;
	const DEFAULT_ORDER_BY = 'created_at';
	const DEFAULT_ORDER_BY_TYPE = 'desc';
    const DEFAULT_YOU_MAY_LIKE_SHORT_CODE = '[random_quizzes limit="10"]';
	public static function incrementQuizStats($quiz, $type) {
		switch($type) {
			case 'attempt':
				$statColumnName = 'attempts';
				$methodName = 'attemptedUsers';
				break;
			case 'completion':
				$statColumnName = 'completions';
				$methodName = 'completedUsers';
				break;
			case 'like':
				$statColumnName = 'likes';
				$methodName = 'likedUsers';
				break;
			case 'share':
				$statColumnName = 'shares';
				$methodName = 'sharedUsers';
				break;
			case 'comment':
				$statColumnName = 'comments';
				$methodName = 'commentedUsers';
				break;
			default:
				$statColumnName = '';
				$methodName = '';
		}
		
		if(empty($statColumnName) || empty($methodName)) {
			throw new Exception('Invalid activity');
		}
		$activityCount = $quiz->$methodName()->count();
		
		$quizStat = $quiz->stats ? $quiz->stats : new QuizStats();
		$quizStat->$statColumnName = $activityCount;
		$quiz->stats()->save($quizStat);
		return true;
	}
	public function index($options = array()) {

        $perPageLimit = self::getPerPageLimit();
		$loadQuizOptions = ['limit' => $perPageLimit];
		$stream = 'latest';
		if(isset($options['stream'])) {
            $streamOptions = self::getQuizQueryStreamOptions($options);
            $stream = $options['stream'];
            $loadQuizOptions = array_merge($loadQuizOptions, $streamOptions);
		}

        if(isset($options['category'])) {
            $category = $options['category'];
            $loadQuizOptions['categoryId'] = $category->id;
            View::share('categoryName', $category->name);
        }

		self::_loadQuizes($loadQuizOptions);
		$titleLangKey = ($stream == "latest") ? 'latestQuizzes' : (($stream == "popular") ? 'popularQuizzes' : 'quizzes');
        $pageHeading = __($titleLangKey);
        if(!empty($options['stream']) && $options['stream'] == 'search' && !empty($options['query'])) {
            $pageHeading = '"'. $options['query'] .'"';
        }

        if(!empty($category)) {
            $pageHeading = $category->name;
        }
		$pageTitle = $pageHeading . ' | ' . \Helpers::getSiteName();
		$pageDescription = __('hereAreSomeQuizzes');
		return View::make('quizes/index')->with(array(
			'currentPage' => 'quizesIndex',
			'title' => $pageTitle,
			'ogTitle' => $pageTitle,
			'description' => $pageDescription,
			'ogDescription' => $pageDescription,
			'isStream' . ucfirst($stream) => true,
			'mainHeading' => $pageHeading
		));
	}

    public function category($categorySlug){
        $category = Category::findBySlug($categorySlug);
        if(!$category)
            return Response::notFound();
        return $this->index(['category' => $category]);
    }

	public function popular(){
		return $this->index(['stream' => 'popular']);
	}

    public function search() {
        $query = Input::get('q');
        do_action('search', $query);
        return $this->index(['stream' => 'search', 'query' => $query]);
    }

	public function iframeList(){
		$loadQuizesOptions = array();
		$loadQuizesOptions['limit'] = Input::get('limit');
        $loadQuizesOptions['stream'] = Input::get('stream');
        if(isset($loadQuizesOptions['stream'])) {
            $streamOptions = self::getQuizQueryStreamOptions($loadQuizesOptions);
            $loadQuizesOptions = array_merge($loadQuizesOptions, $streamOptions);
        }
		self::_loadQuizes($loadQuizesOptions);

		$pageTitle = __('quizzes') . ' | ' . \Helpers::getSiteName();
		$pageDescription = __('hereAreSomeQuizzes');
		return View::make('quizes/iframeList')->with(array(
			'currentPage' => 'quizesIndex',
			'title' => $pageTitle,
			'ogTitle' => $pageTitle,
			'description' => $pageDescription,
			'ogDescription' => $pageDescription
		));
	}
	
	public static function _getQuizes($options = array()) {
		$orderBy = !empty($options['order_by']) ? $options['order_by'] : self::DEFAULT_ORDER_BY;
		$orderByType = !empty($options['order_by_type']) ? $options['order_by_type'] : self::DEFAULT_ORDER_BY_TYPE;

		$quizesQuery = Quiz::where('active', '=', true);
        $quizesQuery->leftJoin('quiz_stats', 'quizes.id', '=', 'quiz_stats.quiz_id');
        $quizesQuery->orderBy($orderBy, $orderByType);
		$limit = isset($options['limit']) ? $options['limit'] : self::DEFAULT_PER_PAGE_LIMIT;
		if(!empty($options['exclude'])) {
			$quizesQuery->whereNotIn('id', array($options['exclude']));
		}
        if(!empty($options['categoryId'])) {
            $quizesQuery->where('category', $options['categoryId']);
        }
        if(!empty($options['search'])) {
            $quizesQuery->search($options['search']);
        }
        do_action_ref_array('pre_get_quizzes', [&$quizesQuery]);
        //dd($quizesQuery->toSql());
		$quizes = $quizesQuery->simplePaginate($limit);
		foreach($quizes as $key => $quiz) {
			$quizes[$key] = $quiz;
		}
        self::touchUpQuizes($quizes);
        //dd($quizes->toArray());
		return $quizes;
	}
	
	public static function _loadQuizes($options = array()) {
		$getQuizzesOptions = $options;
        if(!empty($options['related_to'])) {
            $getQuizzesOptions['exclude'] = $options['related_to'];
        }

		$quizes = self::_getQuizes($getQuizzesOptions);
		View::share('quizes', $quizes);
	}
		
	public function getRouteParams($quiz) {
		return QuizHelpers::viewQuizUrlParams($quiz);
	}
	
	public static function getViewQuizUrl($quiz, $result = null) {
		return QuizHelpers::viewQuizUrl($quiz, $result);
	}
	
	/*
	Save referrer - Increment the referrals of the referring user identified by query param 'ref-by'
	*/
	public static function saveReferrer() {
		try{
			//Saving user referral against the user who referred me
			if($referrerId = Input::get('ref-by')) {
				$userReferral = UserReferrals::firstOrNew(array('user_id' => $referrerId));
				$userReferral->referrals = (empty($userReferral->referrals) ? 0 : $userReferral->referrals) + 1;
				$userReferral->save();
			}
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			//return Response::notFound('Quiz not found');
		} catch(Illuminate\Database\QueryException $e) {
		}
	}
	
	public function viewQuiz($nameString, $quizId = null, $resultId = null) {
		self::saveReferrer();
        $quizConfig = Config::get('siteConfig')['quiz'];
        //User's custom result ID - used for custom result images for users
        $userResult = \Input::get('user-result');
		try {
			$sharedUserId = Input::get('user-fb-id');
			$quiz = Quiz::findOrFail($quizId);

            //Call the_quiz action
            do_action_ref_array( 'the_quiz', array( &$quiz, &$this ) );
            do_action('view_quiz', $quiz);

            self::_loadQuizes(array('related_to' => $quizId));
			//$quiz = Quiz::decodeQuizJson($quiz);
			$quiz->viewQuizUrl = self::getViewQuizUrl($quiz);
            if(!$quiz->active && (empty($quizConfig['inactiveQuizVisibleViaLink']) || $quizConfig['inactiveQuizVisibleViaLink'] != "true")) {
                View::share('quizInactive', true);
                if(!Session::get('admin')) {
                    App::abort(404);
                }
            }
			$ogTitle = $quiz->topic;
			if($resultId) {
				foreach($quiz->results as $res) {
					if($res->id == $resultId) {
						$result = $res;
					}
				}
				if(!empty($result)) {
					$ogImage = @$quiz->ogImages->$resultId;
					if(!empty($result->title)) {
						$ogTitle = __('iGot') . ' "' . $result->title . '" | ' . $quiz->topic;;
					}
				}
				View::share('quizResultId', $resultId);
			}
			if($userResult) {
			    $userResultRecord = QuizUserResults::find($userResult);
                if($userResultRecord) {
                    $userResultImagePath = static::getUserResultImagePath($userResultRecord);
                    if($userResultImagePath) {
                        $ogImage = content_url($userResultImagePath);
                        View::share('quizUserResultImage', $ogImage);
                    }
                }
            }
            $defaultSharingNetworks = array_keys(Config::get('sharingNetworks'));
            $sharingNetworks = @Config::get('siteConfig')['socialSharing']['sharingNetworks'];
            $sharingNetworks = !$sharingNetworks ? $defaultSharingNetworks : $sharingNetworks;

			$ogUrl = $canonicalUrl = !isset($result) ? $quiz->viewQuizUrl : self::getViewQuizUrl($quiz, $result);
            $ogImage = content_url(!empty($ogImage) ? $ogImage : QuizHelpers::getOgImage($quiz));
            $isEmbed = (Input::get('embed') == "true") ? true : false;
            $embedIframeElementIdPrefix = QuizController::getEmbedIframeElementIdPrefix();
			return View::make('quizes/viewQuiz')->with(array(
				'quiz' => $quiz,
				'viewQuizUrl' => self::getViewQuizUrl($quiz),
				'currentPage' => 'viewQuiz',
				'sharedUserId' => $sharedUserId,
				'ogImage' => $ogImage,
				'ogTitle' => $ogTitle,
				'ogUrl' => $ogUrl,
				'title' => $quiz->topic,
				'ogDescription' => $quiz->description,
				'description' => $quiz->description,
                'canonicalUrl' => $canonicalUrl,
                'isEmbed'   =>  $isEmbed,
                'embedIframeElementIdPrefix' =>  $embedIframeElementIdPrefix,
                'sharingNetworks'  =>  $sharingNetworks,
                'showEmbedCode' =>  true
			));
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Quiz not found');
		}
	}

	public function activity($nameString, $quizId = null, $activityType){
		try {
			$quiz = Quiz::findOrFail($quizId);
			$user = Auth::user();
			if(!$user) {
				return Response::json(array('error' => 'Not logged in') , 400);
			}
			$quizUserActivity = QuizUserActivity::firstOrNew(array('user_id' => $user->id, 'quiz_id' => $quiz->id, 'type' => $activityType));
			if($quizUserActivity->created_at) {
				$quizUserActivity->touch();
			} else {
				$quizUserActivity->save();
			}
            do_action('quiz_activity_recorded', $quiz, $activityType);
			self::incrementQuizStats($quiz, $activityType);
			return Response::json(array('message' => 'Activity recorded'));
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Quiz not found');
		}
	}
	
	public function saveUserResult($nameString, $quizId = null){
		try {
			$quiz = Quiz::findOrFail($quizId);
			$user = Auth::user();
			$resultId = Input::get('resultId');
            $defaultResultUrl = QuizHelpers::viewQuizUrl($quiz, $resultId);
			if(!$user) {
				return Response::json(array('url' => $defaultResultUrl));
			}
			$quizUserResult = QuizUserResults::firstOrNew(array('user_id' => $user->id, 'quiz_id' => $quiz->id));
			$quizUserResult->result_id = $resultId;
            if(!$quizUserResult->exists) {
                self::awardPointsForResult($user, $quiz, $resultId);
            }
			$quizUserResult->save();
            $response = array('message' => 'Activity recorded');
            $response['url'] = $defaultResultUrl;

            if(!empty($quiz->settings->addUserPicInResults) && Helpers::isTrue($quiz->settings->addUserPicInResults)) {
                try {
                    //Process custom result image for users- run the generator. If it returns true, an image has been generated, send a custom result url to show that image as og
                    if($resultImagePath = self::generateUserResultImage($quizUserResult)) {
                        $response['url'] .= '?user-result=' . $quizUserResult->id;
                        $response['imageUrl'] = content_url($resultImagePath);
                    }
                } catch (ResultGeneratorException $e) {
                    return Response::json(array('error' => $e->getMessage()) , 500);
                }
            }
			return Response::json($response);
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Quiz not found');
		}
	}
	
	public function saveUserAnswer($nameString, $quizId = null){
		try {
			$quiz = Quiz::findOrFail($quizId);
			$user = Auth::user();
			$questionId = Input::get('questionId');
			$choiceId = Input::get('choiceId');
			if(!$user) {
				return Response::json(array('error' => 'Not logged in') , 400);
			}
			$quizUserAnswer = QuizUserAnswers::firstOrNew(array('user_id' => $user->id, 'quiz_id' => $quiz->id, 'question_id' => $questionId, 'answer_id' => $choiceId));
			$quizUserAnswer->save();
			return Response::json(array('message' => 'Activity recorded'));
		}catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return Response::notFound('Quiz not found');
		} catch(Exception $e) {
			return Response::notFound($e->getMessage());
		}
	}

    public static function touchUpQuizes(&$quizes) {
        /*foreach ($quizes as $key => $quiz) {
            self::touchUpQuiz($quizes[$key]);
        }*/
    }

    public static function touchUpQuiz(&$quiz) {
        /*$quiz->image = !$quiz->image ? : content_url($quiz->image);
        $ogImages = new stdClass();
        foreach ($quiz->ogImages as $key => $image) {
            $ogImages->$key = !$quiz->ogImages->$key ? : content_url($quiz->ogImages->$key);
        }
        $quiz->ogImages = json_encode($ogImages);
        return $quiz;*/
    }

    public static function getEmbedIframeElementIdPrefix() {
        $embedIframeElementIdPrefix = str_replace(array(" ", ".", "-", "_"), '', $_SERVER['HTTP_HOST']) . "-embedQuiz-";
        return $embedIframeElementIdPrefix;
    }

    public static function getPerPageLimit() {
        $siteConfig = Config::get('siteConfig');
        $perPageLimit = @$siteConfig['quiz']['perPageLimit'];
        $perPageLimit = empty($perPageLimit) ? self::DEFAULT_PER_PAGE_LIMIT : $perPageLimit;
        $perPageLimit = apply_filters('post_per_page_limit', $perPageLimit);
        return $perPageLimit;
    }

    public static function isInfiniteScrollEnabled() {
        $siteConfig = Config::get('siteConfig');
         //If infinite scroll is enabled, set per page to custom
        $enableInfiniteScroll = @$siteConfig['quiz']['enableInfiniteScroll'];
        if($enableInfiniteScroll === true || $enableInfiniteScroll == 'true') {
            return true;
        }
    }

    public static function getQuizQueryStreamOptions($options) {
        $loadQuizOptions = [];
        switch($options['stream']) {
            case "popular":
                $loadQuizOptions['order_by'] = 'quiz_stats.attempts';
                $loadQuizOptions['order_by_type'] = 'desc';
                break;
            case "latest":
                $loadQuizOptions['order_by'] = 'created_at';
                $loadQuizOptions['order_by_type'] = 'desc';
                break;
            case "random":
                $loadQuizOptions['order_by'] = DB::raw('rand()');
                $loadQuizOptions['order_by_type'] = 'desc';
                break;
            case "search":
                if(!empty($options['query']))
                    $loadQuizOptions['search'] = $options['query'];
                break;
        }
        return $loadQuizOptions;
    }

    public static function awardPointsForResult($user, $quiz, $resultId)
    {
        $result = array_where($quiz->results, function($result, $key) use($resultId) {
            return $result->id == $resultId;
        });
        if(!count($result))
            return false;
        $result = current($result);
        if(!empty($result->pointsToAward))
            $user->reward(intval($result->pointsToAward));
    }

    public static function makeResultGenerator(QuizUserResults $result)
    {
        $resultGenerator = new ResultImageGenerator($result);
        return $resultGenerator;
    }
    public static function generateUserResultImage(QuizUserResults $result)
    {
        $resultGenerator = self::makeResultGenerator($result);
        if($resultGenerator->generateUserResultImage())
            return $resultGenerator->getResultImagePath();
        return false;
    }

    public static function getUserResultImagePath(QuizUserResults $result)
    {
        $resultGenerator = self::makeResultGenerator($result);
        $resultImagePath = $resultGenerator->getResultImagePath();
        $resultImagePath = apply_filters('user_result_image_path', $resultImagePath, $result);
        return $resultImagePath;
    }
}