<?php
if(!defined('BASE_DOMAIN')) {
    define('BASE_DOMAIN', Helpers::getBaseDomain());
}
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));

Route::get('quizzes', array('as' => 'quizes', 'uses' => 'QuizController@index'));
Route::get('quizzes/popular', array('as' => 'popularQuizes', 'uses' => 'QuizController@popular'));
Route::get('quizzes-iframe', array('as' => 'quizesIframe', 'uses' => 'QuizController@iframeList'));
Route::get('quizzes/{nameString}/{quizId}', array('as' => 'viewQuiz', 'uses' => 'QuizController@viewQuiz'));
Route::get('quizzes/{nameString}/{quizId}/r/{resultId}', array('as' => 'viewQuizResultLandingPage', 'uses' => 'QuizController@viewQuiz'));
Route::post('quizzes/{nameString}/{quizId}/activity/{activityType}', array('as' => 'viewQuizRecordActivity', 'uses' => 'QuizController@activity'));
Route::post('quizzes/{nameString}/{quizId}/user-results/', array('as' => 'viewQuizSaveUserResult', 'uses' => 'QuizController@saveUserResult'));
Route::post('quizzes/{nameString}/{quizId}/user-answers/', array('as' => 'viewQuizSaveUserAnswer', 'uses' => 'QuizController@saveUserAnswer'));
Route::get('pages/{nameString}.html', array('as' => 'viewPage', 'uses' => 'PageController@viewPage'));
Route::get('category/{slug}', array('as' => 'category', 'uses' => 'QuizController@category'));
Route::get('me/get-my-points', array('as' => 'getMyPoints', 'uses' => 'UserController@getMyPoints'));
Route::get('leaderboard/widget', array('as' => 'leaderboardWidget', 'uses' => 'LeaderboardController@widget'));
Route::get('leaderboard', array('as' => 'leaderboard', 'uses' => 'LeaderboardController@index'));
Route::get('search', array('as' => 'search', 'uses' => 'QuizController@search'));


Route::match(array('get', 'post'), 'admin/login', array('as' => 'adminLogin', 'uses' => 'AdminController@login'));
Route::get('admin/logout', array('as' => 'adminLogout', 'uses' => 'AdminController@logout'));
Route::group(array('middleware' => 'App\Http\Middleware\AdminAuth', 'prefix' => 'admin'), function() {
	Route::get('/', array('as' => 'admin', 'uses' => 'AdminController@index'));
	Route::get('quizes/view', array('as' => 'adminViewQuizes', 'uses' => 'AdminQuizesController@listQuizes'));
	Route::match(array('GET', 'POST'), 'quizes/create', array('as' => 'adminCreateQuiz', 'uses' => 'AdminQuizesController@createEdit'));
	Route::match(array('POST'), 'quizes/delete', array('as' => 'adminDeleteQuiz', 'uses' => 'AdminQuizesController@delete'));
	Route::get('pages/view', array('as' => 'adminViewPages', 'uses' => 'AdminPagesController@listPages'));
	Route::match(array('GET', 'POST'), 'pages/create', array('as' => 'adminCreatePage', 'uses' => 'AdminPagesController@createEdit'));
	Route::match(array('GET', 'POST'), 'pages/delete', array('as' => 'adminDeletePage', 'uses' => 'AdminPagesController@delete'));
	Route::match(array('GET', 'POST'), 'config', array('as' => 'adminConfig', 'uses' => 'AdminConfigController@index'));
	Route::match(array('GET', 'POST'), 'config/widgets', array('as' => 'adminConfigWidgets', 'uses' => 'AdminConfigController@widgets'));
	Route::match(array('GET', 'POST'), 'config/languages', array('as' => 'adminConfigLanuages', 'uses' => 'AdminConfigController@languages'));
	Route::match(array('GET', 'POST'), 'config/quiz', array('as' => 'adminConfigQuiz', 'uses' => 'AdminConfigController@quizConfig'));
    Route::match(array('GET', 'POST'), 'config/social-sharing', array('as' => 'adminConfigSocialSharing', 'uses' => 'AdminConfigController@socialSharingConfig'));
    Route::match(array('GET', 'POST'), 'config/leaderboard', array('as' => 'adminConfigLeaderboard', 'uses' => 'AdminConfigController@leaderboardConfig'));

    Route::match(array('GET', 'POST'), 'change-password', array('as' => 'adminChangePassword', 'uses' => 'AdminController@changePassword'));
	Route::match(array('GET', 'POST'), 'users/', array('as' => 'adminUsersHome', 'uses' => 'AdminUsersController@index'));
	Route::match(array('GET', 'POST'), 'users/quiz-users', array('as' => 'adminQuizUsers', 'uses' => 'AdminUsersController@quizUsers'));
	
	Route::match(array('GET', 'POST'), 'quizes/embed-codes', array('as' => 'adminQuizesEmbedCodes', 'uses' => 'AdminQuizesController@embedCodes'));
    Route::match(array('GET', 'POST'), 'categories', array('as' => 'adminCategories', 'uses' => 'AdminCategoriesController@view'));
    Route::match(array('GET', 'POST', 'PATCH', 'DELETE'), 'categories/addEdit', array('as' => 'adminCategoriesAddEdit', 'uses' => 'AdminCategoriesController@addEdit'));

    Route::get('update', array('as' => 'update', 'uses' => 'UpdateController@index'));
    Route::get('update/get-details', array('as' => 'getUpdateDetails', 'uses' => 'UpdateController@getUpdateDetails'));
    Route::get('update/do', array('as' => 'doUpdate', 'uses' => 'UpdateController@doUpdate'));

    Route::get('shortcodes', array('as' => 'adminShortCodes', 'uses' => 'AdminShortCodesController@index'));
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');


    Route::get('plugins', array('uses' => 'AdminPluginsController@getIndex'));
    Route::get('plugins/configure/{slug}', array('uses' => 'AdminPluginsController@getConfigure'));
    Route::post('plugins/configure/{slug}', array('uses' => 'AdminPluginsController@postConfigure'));
    Route::get('plugins/translate/{slug}', array('uses' => 'AdminPluginsController@getTranslate'));
    Route::post('plugins/translate/{slug}', array('uses' => 'AdminPluginsController@postTranslate'));
    Route::post('plugins/activate/{slug}', array('uses' => 'AdminPluginsController@postActivate'));
    Route::post('plugins/install/{slug}', array('uses' => 'AdminPluginsController@postInstall'));
    Route::post('plugins/uninstall/{slug}', array('uses' => 'AdminPluginsController@postUninstall'));

    //Media manager
    Route::group(array(), function()
    {
        \Route::get('media', 'W3G\MediaManager\MediaManagerController@showStandalone');
        \Route::any('media/connector', array('as' => 'mediaConnector', 'uses' => 'W3G\MediaManager\MediaManagerController@connector'));
    });
});

Route::get('/login', array('as' => 'login', 'uses' => 'UserController@login'));
Route::get('login/fb', array('as' => 'loginWithFb', 'uses' => 'UserController@loginWithFb'));

Route::get('logout', array('as' => 'logout', 'uses' => 'UserController@logout'));

//404 macro
Response::macro('notFound', function($value = null)
{
    QuizController::_loadQuizes();
    return Response::view('errors.404', array('errorMsg' => strtoupper($value)), 404);
});