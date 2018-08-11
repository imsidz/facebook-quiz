<?php

class AdminQuizesController extends BaseController{
	public static $perPage = 10;
	public static $sortableFields = array('created_at', 'shareRate');
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
		if($sort == 'shareRate') {
			$sort = DB::raw('quiz_stats.shares/quiz_stats.attempts');
		}
		return array('sort' => $sort, 'sortType' => $sortType);
	}
	public function listQuizes(){
		$search = Input::get('search', null);
		$sortOptions = self::processSort();
		//Uses normal left join than "with('stats')" to be able to sort by shareRate
		$quizzesQuery = Quiz::joinStats()->orderBy($sortOptions['sort'], $sortOptions['sortType']);
		if($search){
			$quizzesQuery->where('topic', 'like', '%' . $search . '%');
		}
        $quizzesQuery = apply_filters('admin_quizzes_list_query', $quizzesQuery);
		$quizes = $quizzesQuery->paginate(self::$perPage);
		if($search){
			$quizes->appends(['search' => $search]);
		}
		//dd(DB::getQueryLog());
		//dd($quizes->toArray());
		self::addShareRates($quizes);

		return View::make('admin/quizes/view')->with(array(
			'quizes' => $quizes,
			'search' => $search
		));
	}
	
	public function createEdit(){
		
		$quizId = Input::get('quizId', null);
		$quizData = Input::get('quiz', array());
        $duplicateQuiz = Input::get('duplicate-quiz', array());
        $duplicateQuizData = null;
		try {
            if($duplicateQuiz) {
                $duplicateQuizObject = Quiz::findOrFail($duplicateQuiz);
                $duplicateQuizData = $duplicateQuizObject->toArray();
                unset($duplicateQuizData['id']);
                View::share(array(
                    'duplicateQuiz' =>  $duplicateQuizObject
                ));
            }
			if($quizId || !empty($quizData['id'])) {
				//die(var_dump($quizId));
				$quiz = Quiz::findOrFail($quizId ? $quizId : $quizData['id']);
			} else {
				$quiz = new Quiz;
			}
		} catch(ModelNotFoundException $e) {
			return Response::json(array(
				'error' => 1,
				'message' => $e->getMessage()
			));
		}
		
		if(Request::ajax() && Request::isMethod('post')) {
			//Form submitted- Create the quiz
			
			//$keys = ['topic', 'description', 'pageContent', 'image', 'questions', 'results', 'ogImages'];

            self::addQuizData($quizData, $quiz);
			
			$quiz->active = (!empty($quizData['active']) && ($quizData['active'] === "true" || $quizData['active'] === true)) ? true : false;
			try {
                self::saveThumbnails($quiz);
            } catch (InvalidArgumentException $e) {
                return Response::error("Error! Quiz not saved! " . $e->getMessage());
            }

            $quiz->save();
			return Response::json(array(
				'success' => 1,
				'quiz' => $quiz
			));
		} else {
			//Form submitted- Create the quiz or parse and show forms if basic quizData is passed
			$populateQuizData = Input::get('quizData', '{}');
            $populateQuizData = json_decode($populateQuizData);
            if(!empty($duplicateQuizData)) {
                $populateQuizData = $duplicateQuizData;
            }
			
			$quizSchema = new \Schemas\QuizSchema();
			$questionSchema = new \Schemas\QuestionSchema();
			$choiceSchema = new \Schemas\ChoiceSchema();
			$resultSchema = new \Schemas\ResultSchema();
			/*if(!empty(Input::get('test')))
				die(var_dump($quiz));*/
            $embedIframeElementIdPrefix = str_replace(array(" ", ".", "-", "_"), '', $_SERVER['HTTP_HOST']) . "-embedQuiz-";
            $response = View::make('admin/quizes/create')->with(array(
				'quizSchema' => $quizSchema->getSchema(),
				'questionSchema' => $questionSchema->getSchema(),
				'choiceSchema' => $choiceSchema->getSchema(),
				'resultSchema' => $resultSchema->getSchema(),
				'quizData' => $quiz->id ? json_encode($quiz) : json_encode($populateQuizData),
				'quiz' => $quiz,
				'editingMode' => $quizId ? true : false,
				'creationMode' => $quizId ? false : true,
                'embedIframeElementIdPrefix' =>  $embedIframeElementIdPrefix
			));
            $response = apply_filters('admin_quizzes_create_page_response', $response, $quiz);
            return $response;
		}
	}

	public function delete(){

		$quizId = Input::get('quizId', null);
		if(!$quizId){
			return Response::error("Quiz not found");
		}
		try {
			$quiz = Quiz::findOrFail($quizId ? $quizId : $quizData['id']);
		} catch(ModelNotFoundException $e) {
			return Response::error("Error finding quiz with id " . $quizId);
		}
		if($quiz->delete()){
			return Response::json(array(
				'success' => true
			));
		} else {
			return Response::error("Some error occured while deleting quiz : '" . $quizId->topic . "'");
		}
	}
	
	public function embedCodes(){
		return View::make('admin/quizes/embedCodes');
	}
	
	public static function saveThumbnails($quiz) {
		function saveThumbnail($imagePath, $width, $height) {
			$imgContent = file_get_contents($imagePath);
			$image = imagecreatefromstring($imgContent);
			$origWidth = imagesx($image);
			$origHeight = imagesy($image);
			$pathParts = pathinfo($imagePath);
			$thumbPath = $pathParts['dirname'] . '/' . $pathParts['basename'] . '_thumb.jpg';
			$thumbImage = imagecreatetruecolor($width, $height);
			imagecopyresampled($thumbImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
			imagejpeg($thumbImage, $thumbPath, 90);
		}
		
		$ogImages = (array) $quiz->ogImages;
        if(!empty($ogImages['main'])) {
            if(strpos($ogImages['main'], 'http://') === 0 || strpos($ogImages['main'], 'https://') === 0) {
                throw new InvalidArgumentException("You shouldn't use remote images for Og images. Please upload them!");
            }
            if(file_exists(content_path($ogImages['main']))) {
                saveThumbnail(content_path($ogImages['main']), 400, 210);
            }
        }
		/*foreach($ogImages as $ogImage) {
			if(!empty($ogImage) && file_exists('.' . $ogImage)) {
				saveThumbnail('.' . $ogImage, 210, 400);
			}
		}*/
		
	}
	
	public static function getThumbnail($quiz) {
		
	}

	public static function addShareRates(&$quizes){
		foreach($quizes as $quiz){
			$quizStats = $quiz->stats;
			$quizShareRate = $quizLikeRate = number_format(0, 2);
			if($quizStats['attempts']) {
				$quizShareRate =  number_format(($quizStats['shares'] / $quizStats['attempts']) * 100, 2);
				$quizLikeRate =  number_format(($quizStats['likes'] / $quizStats['attempts']) * 100, 2);
			}
			$quiz->shareRate = $quizShareRate;
			$quiz->likeRate = $quizLikeRate;
			if($quiz->shareRate > 40)
				$quiz->shareRateRange = "high";
			else if($quiz->shareRate > 20)
				$quiz->shareRateRange = "fair";
			else if($quiz->shareRate > 10)
				$quiz->shareRateRange = "medium";
			else
				$quiz->shareRateRange = "low";
		}
	}

    /**
     * @param $quizData
     * @param $quiz
     */
    public static function addQuizData($quizData, $quiz)
    {
        $excludeKeys = ['created_at', 'updated_at', 'id', 'views', 'status', 'created_by', 'active', 'attempts', 'completions'];
        foreach ($quizData as $key => $val) {
            if (in_array($key, $excludeKeys))
                continue;
            $quiz->$key = is_array($quizData[$key]) ? json_encode($quizData[$key]) : $quizData[$key];
        }
    }
}