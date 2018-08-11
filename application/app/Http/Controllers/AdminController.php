<?php

use ConfigFileWriter\ConfigFileWriter;

class AdminController extends BaseController {

    const INSTALL_FILE = 'install.php';
    public function __construct() {
        try{
            $adminData = self::getAdminCredentials();
            define('ADMIN_USERNAME', $adminData['username']);
            define('ADMIN_PASSWORD', $adminData['password']);

            if(!App::isLocal() && file_exists($this->getInstallFilePath())) {
                View::share('installFileExistsError', "You must remove the file '" . self::INSTALL_FILE . "' on the script's base folder for security reasons.");
            }
        } catch(Exception $e){
            die(Response::error(
                "<h4>Original error message:</h4>" . $e->getMessage(),
                "Error loading admin config. Check Admin config file: /protected/app/config/admin.php"
            ));
        }
    }

    public static function getAdminCredentials($key = null){
        $adminCredentials = [
            'username' => env('ADMIN_USERNAME'),
            'password' => env('ADMIN_PASSWORD')
        ];
        if($key && isset($adminCredentials[$key])) {
            return $adminCredentials[$key];
        }
        return $adminCredentials;
    }

    public function index()
    {
        $totalQuizzesCount = Quiz::count();
        $totalUsersCount = User::count();
        $todayQuizzesCount = Quiz::whereRaw('DATE(created_at) = DATE(NOW())')->count();
        $todayUsersCount = User::whereRaw('DATE(created_at) = DATE(NOW())')->count();

        $overallActivities = QuizUserActivity::groupBy('type')->havingRaw("type in ('attempt', 'share')")->select('type', DB::raw('count(*) as count'))->get()->toArray();
        $overallStats = array();
        foreach($overallActivities as $activity) {
            $overallStats[$activity['type']] = $activity['count'];
        }
        $overallStats['quizzes'] = $totalQuizzesCount;
        $overallStats['users'] = $totalUsersCount;

        $todayActivities = QuizUserActivity::whereRaw('DATE(created_at) = DATE(\'' . date('Y-m-d H:i:s') . '\')')->groupBy('type')->havingRaw("type in ('attempt', 'share')")->select('type', DB::raw('count(*) as count'))->get()->toArray();
        $todayStats = array();
        foreach($todayActivities as $activity) {
            $todayStats[$activity['type']] = $activity['count'];
        }
        $todayStats['quizzes'] = $todayQuizzesCount;
        $todayStats['users'] = $todayUsersCount;

        //Filling stats vars that are not yet set
        self::fillNullStats($todayStats);
        self::fillNullStats($overallStats);

        View::share(array(
            'overallStats' => $overallStats,
            'todayStats' => $todayStats
        ));

        $last30DaysAttempts = self::getLastNDaysActivity(30, 'attempt');
        $activityTypes = ['completion', 'like', 'share', 'comment'];
        $last30DaysActivities = $last30DaysAttempts;
        foreach($activityTypes as $activityType) {
            $activities = self::getLastNDaysActivity(30, $activityType);
            foreach($activities as $key => $activity) {
                $last30DaysActivities[$key][$activityType] = $activity[$activityType];
            }
        }

        $last30DaysUserRegistrations = self::getLastNDaysUserRegistrations(30);
        View::share(array(
            'last30DaysAttempts' => json_encode($last30DaysAttempts),
            'last30DaysActivities' => json_encode($last30DaysActivities),
            'last30DaysUserRegistrations' => json_encode($last30DaysUserRegistrations
            )));

        $newUsers = User::orderBy('created_at', 'desc')->take(10)->get();
        View::share('newUsers', $newUsers);

        $latestActivity = QuizUserActivity::orderBy('created_at', 'desc')->with('quiz')->has('quiz')->with('user')->take(10)->get();
        View::share('latestActivity', $latestActivity);
        return View::make('admin/index');
    }

    public function login(){
        View::share(array('redirect' => Input::get('redirect')));
        if(Request::isMethod('get')) {
            return View::make('admin/login');
        } else {
            $username = Input::get('username');
            $password = Input::get('password');
            if($username == ADMIN_USERNAME && $password == ADMIN_PASSWORD) {
                //Login success
                Session::set('admin', 'admin');
                $this->_onLogin();
                if(Input::get('redirect')) {
                    return Redirect::to('/' . urldecode(Input::get('redirect')));
                } else {
                    return Redirect::route('admin');
                }
            } else {
                return View::make('admin/login')->with(array('error' => 'Incorrect username or password!'));
            }
        }
    }
    public function logout(){
        Session::forget('admin');
        return 'Logged out successfully';
    }

    public static function lastNDays($n){
        $timestamp = time();
        $days = array();
        for ($i = 0 ; $i < $n ; $i++) {
            $days[] = date('Y-m-d', $timestamp);
            $timestamp -= 24 * 3600;
        }
        return $days;
    }

    public static function getLastNDaysActivity($n, $activityType, $quiz = null){

        $activityHistory = QuizUserActivity::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as activityCount'))->where('type', '=', $activityType)->where('created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL '. $n .' DAY)'));
        if($quiz){
            $activityHistory = $activityHistory->where('quiz_id', '=', $quiz->id);
        }
        $activityHistory = $activityHistory->groupBy('date')->get()->toArray();

        $lastNDays = self::lastNDays($n);
        $lastNDaysActivity = array();
        foreach($lastNDays as $key => $day) {
            $lastNDaysActivity[$key] = array('date' => $day, $activityType => 0);
            foreach($activityHistory as $activity){
                if ($activity['date'] === $day) {
                    $lastNDaysActivity[$key][$activityType] = $activity['activityCount'];
                }
            }
        }
        return ($lastNDaysActivity);
    }

    public static function getLastNDaysUserRegistrations($n){

        $activityHistory = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as registrations'))->where('created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL '. $n .' DAY)'))->groupBy('date')->get()->toArray();

        $lastNDays = self::lastNDays($n);
        $lastNDaysActivity = array();
        foreach($lastNDays as $key => $day) {
            $lastNDaysActivity[$key] = array('date' => $day, 'registrations' => 0);
            foreach($activityHistory as $activity){
                if ($activity['date'] === $day) {
                    $lastNDaysActivity[$key]['registrations'] = $activity['registrations'];
                }
            }
        }
        return ($lastNDaysActivity);
    }

    /*
     * Fill the stats vars that are not yet set in the stats array
     * Set them to null
     * @param $stats The stats array
     */
    public static function fillNullStats(&$stats){
        $statsVars = array('quizzes', 'users', 'attempt', 'share');
        foreach ($statsVars as $var) {
            if(!isset($stats[$var]))
                $stats[$var] = 0;
        }

    }

    public function changePassword(){
        $currentUsername = AdminController::getAdminCredentials('username');
        $configFilePath = dirname(base_path()) . DIRECTORY_SEPARATOR . 'config.php';
        $errors = [];
        $formSuccess = null;
        if(Request::isMethod('post')){
            //Form sumbitted
            $username = Input::get('username');
            $password = Input::get('password');
            $repeatPassword = Input::get('repeatPassword');
            if(!$username || !$password || !$repeatPassword) {
                $errors[] = "Username or password empty! Please fill in all fields";
            } else if($password != $repeatPassword){
                $errors[] = "The passwords doesn't match. Repeat the same password to make sure it is correct.";
            }
            if(!$errors){
                //No error. Save new credentials
                $adminCredentialsConfig = [
                    'ADMIN_USERNAME' => $username,
                    'ADMIN_PASSWORD' => $password
                ];

                try {
                    $configFileWriter = new ConfigFileWriter($configFilePath);
                    $configFileWriter->update($adminCredentialsConfig);
                    $formSuccess = true;
                } catch(Exception $e) {
                    $errors[] = "Failed storing new admin credentials to config file. Make sure '" . $configFilePath . "' is writable.'";
                }
            }
        }

        return View::make('admin/config/changePassword')->with(array(
            'currentUsername' => $currentUsername,
            'formErrors' => $errors,
            'formSuccess' => $formSuccess
        ));
    }
    public function getInstallFilePath() {
        if(CMS_PACKED_MODE)
            base_path('../' . static::INSTALL_FILE);
        return base_path(static::INSTALL_FILE);
    }
    public function removeInstallFile() {
        if(file_exists($this->getInstallFilePath())) {
            try {
                unlink($this->getInstallFilePath());
            } catch(Exception $e) {
                //May be a permission problem. Discard - warning message will be shown in admin panel to inform user to remove it manually
            }
        }
    }

    public function _onLogin()
    {
        //Remove install file
        if(!\App::isLocal()){
            $this->removeInstallFile();
        }
    }

}
