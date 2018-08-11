<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		QuizController::_loadQuizes(['limit' => QuizController::getPerPageLimit()]);

		$pageTitle = Config::get('siteConfig')['main']['siteTitle'];
        $pageDescription = Config::get('siteConfig')['main']['siteDescription'];

        $ogTitle = $pageTitle;
        $ogDescription = $pageDescription;
        $ogImage = '';
        try {
            $ogTitle = Config::get('siteConfig')['main']['ogData']['siteOgTitle'];
            $ogDescription = Config::get('siteConfig')['main']['ogData']['siteOgDescription'];
            $ogImage = content_url(Config::get('siteConfig')['main']['ogData']['siteOgImage']);
            if($ogTitle == 'siteOgTitle') {
                $ogTitle = $pageTitle;
            }
            if($ogDescription == 'siteOgDescription') {
                $ogDescription = $pageDescription;
            }
        } catch(Exception $e) {

        }

        $pageTitle = apply_filters('home_meta_title', $pageTitle);
        $pageDescription = apply_filters('home_meta_description', $pageDescription);
        $ogTitle = apply_filters('home_og_title', $ogTitle);
        $ogDescription = apply_filters('home_og_description', $ogDescription);

		$response = View::make('home')->with(array(
			'title' => $pageTitle,
			'ogTitle' => $ogTitle,
			'description' => $pageDescription,
			'ogDescription' => $ogDescription,
            'ogImage'  =>  $ogImage
		));
        $response = apply_filters('home_page_response', $response);
        return $response;
	}

}
