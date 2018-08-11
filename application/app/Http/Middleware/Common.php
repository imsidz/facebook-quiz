<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 12/11/15
 * Time: 1:57 AM
 */

namespace App\Http\Middleware;
use App;
use Closure;
use Illuminate\Support\Collection;
use SiteConfig;
use Config;
use View;
use Auth;
use CustomEmailSettings;
use Post;
use Helpers;
use Languages;

class Common {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->detectAdmin();
        //dd($request->getPathInfo() == route(Config::get('app-installer::routeName'), [], false));

        //If is install route, skip config check
        if($request->route()->getName() == Config::get('app-installer::routeName')) {
            $isInstalling = true;
        } else {
            $isInstalling = false;
        }

        if((!\DB::connection('mysql')->getPdo() || !\Schema::hasTable('config'))&& !$isInstalling) {
            die(\Response::configurationError("Seems like you are starting up! Go on. Install it! Refer to documentation for instructions.", "Installation not completed"));
        }

        self::loadCommonData();

        /*//Load PostManager
        App::register('PostManager\PostManagerServiceProvider');*/
        $response = $next($request);

        //After filters
        return $response;
    }

    public static function loadCommonData()
    {
        //Loading config
        App::make('\MyConfigManager\ConfigManager')->loadConfig();

        $config = \MyConfig::all();
        //If config is empty, skip the rest
        if(!$config)
            return $next($request);

        Config::set('siteConfig', $config);

        $language = \Languages::initialize($config['languages']['languages'], 'en');

        //Activate Language
        if($languageFromDomain = Helpers::getLanguageFromDomain()) {
            if($language->isLanguageAvailable($languageFromDomain)) {
                $activeLanguage = $languageFromDomain;
            } else {
                //Is invalid language - redirect to homepage on main domain
                return redirect(route('home'));
            }
        }
        else if(!empty($config['languages']['activeLanguage'])) {
            $activeLanguage = $config['languages']['activeLanguage'];
        } else {
            $activeLanguage = $language->getDefaultLanguage();
        }

        /*//If the active language is not the default language and it is not the one in the domain, redirect to the domain
        if($activeLanguage != $language->getDefaultLanguage() && Helpers::getLanguageFromDomain() != $activeLanguage) {
            return redirect(Helpers::getLanguageHomeUrl($activeLanguage));
        }*/

        $language->activateLanguage($activeLanguage);

        //If current language is not the user's native language
        if(!Languages::isActiveLanguageMyNative())
            View::share('isNotMyNativeLanguage', true);

        View::share('activeLanguage', $language->getLanguage($activeLanguage));

        View::share('config', $config);
        View::share('languages', \MyConfig::get('languages.languages'));
        $safeToExposeMainConfig = $config['main'];
        unset($safeToExposeMainConfig['social']['facebook']['secret']);
        View::share('mainConfigJSON', json_encode($safeToExposeMainConfig));
        View::share('quizConfigJSON', json_encode($config['quiz']));

        //Loading colors from config for theming
        if(!empty($config['main']['navbarColor'])) {
            View::share('navbarColor', $config['main']['navbarColor']);
        }
        if(!empty($config['main']['mainBtnColor'])) {
            View::share('mainBtnColor', $config['main']['mainBtnColor']);
        }
        if(!empty($config['main']['linkColor'])) {
            View::share('linkColor', $config['main']['linkColor']);
        }

        self::loadWidgets();

        $sharingNetworks = @Config::get('siteConfig')['socialSharing']['sharingNetworks'];
        $sharingNetworks = !$sharingNetworks ? [] : $sharingNetworks;
        View::share('sharingNetworks', $sharingNetworks);

        if(Auth::check()) {
            $user = Auth::user();
            $userData = json_encode($user->toArrayWithPoints());
            View::share('userData', $userData);
        }

        $mediaManagerRoots = \Config::get('laravel-media-manager.roots');
        $mediaManagerRoots[0]['URL'] = ('/media');
        \Config::set('laravel-media-manager.roots', $mediaManagerRoots);

        //Getting Categories
        \BaseController::loadCategories();
    }
    public static function loadWidgets() {
        //Loading widgets
        $widgets = [];
        $widgetPlacements = \MyConfig::get('widgets.widgets');
        if(!empty($widgetPlacements)) {
            foreach($widgetPlacements as $widgetPlacement) {
                $widgetItems = !empty($widgetPlacement['widgets']) ? $widgetPlacement['widgets'] : array();
                //Remove disabled widgets
                $widgetItems = array_filter($widgetItems, function($widgetItem){
                    $pass = true;
                    if(isset($widgetItem['disabled']) && ($widgetItem['disabled'] === true || $widgetItem['disabled'] == "true"))
                        $pass = false;
                    $pass = apply_filters('widget_filter', $pass, $widgetItem);
                    return $pass;
                });
                $widgets[$widgetPlacement['id']] = $widgetItems;
            }
        }
        View::share('widgets', $widgets);
    }


    public function detectAdmin() {
        $admin = \Session::get('admin');
        \View::share('loggedInAdmin', $admin);

        App::singleton('loggedInAdmin', function() use($admin) {
            return $admin;
        });
    }

    public function terminate($request, $response) {
        $time = microtime();
        $execTime = ($time - LARAVEL_START);
        //dd($execTime);
    }
}