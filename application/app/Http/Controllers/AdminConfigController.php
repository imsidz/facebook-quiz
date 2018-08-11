<?php

class AdminConfigController extends BaseController{
	
	public function index(){
		$configMainSchema = new \Schemas\ConfigMainSchema();
		$mainConfigRow = SiteConfig::where('name', 'main')->first();
		if(!$mainConfigRow) {
			$mainConfigRow = new SiteConfig();
			$mainConfigRow->name = 'main';
		}
		if(Request::ajax() && Request::isMethod('post')) {
			$configData = Input::get('config', array());
			$mainConfigData = $configData;
			$mainConfigRow->value = json_encode($mainConfigData);
			$mainConfigRow->save();
			return Response::json(array(
				'success' => 1,
				'config' => $configData
			));
		} else{
			return View::make('admin/config/index')->with(array(
				'configMainSchema' => $configMainSchema->getSchema(),
				'mainConfigData' => $mainConfigRow->value ? $mainConfigRow->value : array()
			));
		}
	}
	
	public function widgets(){
		$widgetsSchema = new \Schemas\WidgetsSchema();
		$widgetsDataRow = SiteConfig::where('name', 'widgets')->first();
		if(!$widgetsDataRow) {
			$widgetsDataRow = new SiteConfig();
			$widgetsDataRow->name = 'widgets';
		}

        $this->_addMissingWidgetData($widgetsDataRow);

		if(Request::ajax() && Request::isMethod('post')) {
			$widgetsData = Input::get('widgets', array());
			
			$widgetsDataRow->value = json_encode($widgetsData);
			$widgetsDataRow->save();
			return Response::json(array(
				'success' => 1,
				'widgets' => $widgetsData
			));
		} else{
			return View::make('admin/config/widgets')->with(array(
				'widgetsSchema' => $widgetsSchema->getSchema(),
				'widgetsData' => $widgetsDataRow->value ? $widgetsDataRow->value : array('widgets'=> array())
			));
		}
	}

	public function languages() {
		$languagesSchema = new \Schemas\LanguagesSchema();
		$languagesDataRow = SiteConfig::where('name', 'languages')->first();
		if(Request::ajax() && Request::isMethod('post')) {
			$languagesData = Input::get('languages', array());

			$languagesDataRow->value = json_encode($languagesData);
			$languagesDataRow->save();
			return Response::json(array(
				'success' => 1,
				'languages' => $languagesData
			));
		} else {
			return View::make('admin/config/languages')->with(array(
				'languagesSchema' => $languagesSchema->getSchema(),
				'languagesData' => $languagesDataRow->value ? $languagesDataRow->value : array()
			));
		}
	}

	public function quizConfig(){
		$quizConfigSchema = new \Schemas\QuizConfigSchema();
		$quizConfigDataRow = SiteConfig::where('name', 'quiz')->first();
		if(!$quizConfigDataRow) {
			$quizConfigDataRow = new SiteConfig();
			$quizConfigDataRow->name = 'quiz';
		}
		
		if(Request::ajax() && Request::isMethod('post')) {
			$quizConfigData = Input::get('quizConfig', array());
			
			$quizConfigDataRow->value = json_encode($quizConfigData);
			$quizConfigDataRow->save();
			return Response::json(array(
				'success' => 1,
				'quizConfig' => $quizConfigData
			));
		} else{
			return View::make('admin/config/quiz')->with(array(
				'quizConfigSchema' => $quizConfigSchema->getSchema(),
				'quizConfigData' => $quizConfigDataRow->value ? $quizConfigDataRow->value : '{quizConfig:[]}'
			));
		}
	}

    public function leaderboardConfig(){
        $leaderboardConfigFromFile = Config::get('leaderboard');
        $leaderboardEvents = $leaderboardConfigFromFile['events'];
        $LeaderboardConfigSchema = new \Schemas\LeaderboardConfigSchema();
        $schema = json_decode($LeaderboardConfigSchema->getSchema(), true);
        foreach($leaderboardEvents as $event) {
            $schema['scores']['properties'][$event['id']] = [
                'type'      =>  'number',
                'title'      =>  $event['name'],
                'required'  =>  true
            ];
        }

        $leaderboardConfigDataRow = SiteConfig::where('name', 'leaderboard')->first();
        if(!$leaderboardConfigDataRow) {
            $leaderboardConfigDataRow = new SiteConfig();
            $leaderboardConfigDataRow->name = 'leaderboard';
        }

        if(Request::ajax() && Request::isMethod('post')) {
            $leaderboardConfigData = Input::get('leaderboardConfig', array());

            $leaderboardConfigDataRow->value = json_encode($leaderboardConfigData);
            $leaderboardConfigDataRow->save();
            if(!empty($leaderboardConfigData['scores']['userSignUp'])) {
                //Add signup score for all users whose leaderboard hasn't been created yet
                $createMissingLeaderboardsQuery = 'insert into leaderboard(boardable_type, points, created_at, updated_at, boardable_id) select "User", ' . intval($leaderboardConfigData['scores']['userSignUp']) . ', NOW(), NOW(), id from users';
                try{
                    $topUsers = User::getTopNUsers();
                    if(!count($topUsers)) {
                        DB::unprepared($createMissingLeaderboardsQuery);
                    }
                } catch (\PDOException $e) {
                    //die("Update failed: Error running query : \"" . substr($createMissingLeaderboardsQuery, 0, 120) . '...' . "\". " . $e->getMessage());
                }
            }
            return Response::json(array(
                'success' => 1,
                'leaderboardConfig' => $leaderboardConfigData
            ));
        } else{
            return View::make('admin.config.leaderboard')->with(array(
                'leaderboardConfigSchema' => json_encode($schema),
                'leaderboardConfigData' => $leaderboardConfigDataRow->value ? $leaderboardConfigDataRow->value : '{}'
            ));
        }
    }

    public function socialSharingConfig() {
        $socialSharingConfigDataRow = SiteConfig::where('name', 'socialSharing')->first();
        if(!$socialSharingConfigDataRow) {
            $socialSharingConfigDataRow = new SiteConfig();
            $socialSharingConfigDataRow->name = 'socialSharing';
        }

        $sharingNetworkNames = Config::get('sharingNetworks');

        $sharingNetworks = array_keys($sharingNetworkNames);

        $socialSharingConfigData = json_decode($socialSharingConfigDataRow->value, true);
        $activeNetworks = @$socialSharingConfigData['sharingNetworks'];
        if(!$activeNetworks)
            $activeNetworks = [];
        $inactiveNetworks = array_diff($sharingNetworks, $activeNetworks);

        if(Request::ajax() && Request::isMethod('post')) {
            $socialSharingConfigData = Input::get('socialSharingConfig', array());

            $socialSharingConfigDataRow->value = json_encode($socialSharingConfigData);
            $socialSharingConfigDataRow->save();
            return Response::json(array(
                'success' => 1,
                'socialSharingConfig' => $socialSharingConfigData
            ));
        } else{
            return View::make('admin/config/socialSharing')->with(array(
                'socialSharingConfigData' => $socialSharingConfigDataRow->value ? $socialSharingConfigDataRow->value : '{sharingNetworks:[]}',
                'sharingNetworkNames'  =>  $sharingNetworkNames,
                'sharingNetworks'  =>  $sharingNetworks,
                'activeNetworks'   =>  $activeNetworks,
                'inactiveNetworks'   =>  $inactiveNetworks
            ));
        }
    }

    public function _addMissingWidgetData(&$widgetsDataRow) {
        $widgetsData = json_decode($widgetsDataRow->value);
        $widgetsDataSchemaObj = new \Schemas\WidgetsDataSchema();
        $widgetsDataSchema = json_decode($widgetsDataSchemaObj->getSchema());

        function hasWidgetSection($sectionId, $widgetsData) {
            $hasSection = false;
            array_map(function($widgetSection) use(&$hasSection, $sectionId){
                if($widgetSection->id == $sectionId) {
                    $hasSection = true;
                }
            }, $widgetsData->widgets);
            return $hasSection;
        }

        foreach ($widgetsDataSchema->widgets as $widgetSection) {
            if(!hasWidgetSection($widgetSection->id, $widgetsData)) {
                $widgetsData->widgets[] = $widgetSection;
            }
        }

        //Save the changes back
        $widgetsDataRow->value = json_encode($widgetsData);
    }
}