<?php
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class UserController extends BaseController {
	public function login() {
		return View::make('login');
	}
	public function loginWithFb() {
		$redirectUrl = Input::get('redirect_url');
		$facebookBaseConfig = Config::get('facebook');
		$config = app('siteConfig');
		$facebookConfig = $config['main']['social']['facebook'];
		$facebookConfig['appId'] = empty($facebookConfig['appId']) ? '' : $facebookConfig['appId'];
		$facebookConfig['secret'] = empty($facebookConfig['secret']) ? '' : $facebookConfig['secret'];
		FacebookSession::setDefaultApplication($facebookConfig['appId'], $facebookConfig['secret']);
		$helper = new FacebookJavaScriptLoginHelper();
		$session = null;

		function getUserDataFromFb($session){
            $request = new FacebookRequest($session, 'GET', '/me', array('fields' => 'id,name,email'));
			$response = $request->execute();
			$graphObject = $response->getGraphObject()->asArray();
			return $graphObject;
		}

		try {
			$session = $helper->getSession();
		} catch(FacebookRequestException $ex) {
			// When Facebook returns an error
            return Response::make($ex->getMessage(), 400);
        } catch(\Exception $ex) {
            // When validation fails or other local issues
            return Response::make($ex->getMessage(), 400);
		}
		if(Request::ajax()) {
			if ($session) {
			  // Logged in.
				$uid = $session->getUserId();
				$accessToken = $session->getToken();
				$profile = Profile::whereUid($uid)->first();
				if (empty($profile)) {
					$me = getUserDataFromFb($session);	
					$user = new User;
                    $user->name = $me['name'];
					$user->email = $me['email'];
					$user->photo = 'https://graph.facebook.com/'.$uid.'/picture?type=large';

					$user->save();

					$profile = new Profile();
					$profile->uid = $uid;
					//$profile->username = $me['username']; //Username not available in the new Facebook API
					$profile->access_token = $accessToken;
					$profile = $user->profiles()->save($profile);
					
				}
				else {
					$profile->access_token = $accessToken;
					$profile->save();
				}
				$user = $profile->user;
				Auth::login($user);
				return Response::json(array('user' => $user->toArrayWithPoints()));
			} else {
				return Response::make('Not loggedin', 400);
			}
		} else{
			if ($session) {
				if($redirectUrl) {
					return Redirect::to($redirectUrl);
				} else{
					return Redirect::route('home');
				}
			}
			return Redirect::route('login');
		}
	}

    public function getMyPoints() {
        $user = Auth::user();
        return Response::json(array(
           'points' =>  $user->points
        ));
    }
	
	public function logout(){
		Auth::logout();
		return Redirect::to('/');
	}
}