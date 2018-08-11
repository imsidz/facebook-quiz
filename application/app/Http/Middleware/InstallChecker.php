<?php namespace App\Http\Middleware;

use Closure;

class InstallChecker {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    $route = $request->route();
        $isInstalling = false;
        $config = app('siteConfig');
        //If is install route, skip config check
        if($route->getName() == Config::get('app-installer::routeName')) {
            $isInstalling = true;
        }

        if(!Config::get('database') && !$isInstalling) {
            die(Response::configurationError("Seems like you are starting up! Go on. Install it! Refer to documentation for instructions.", "Installation not completed"));
        }
        //If config is empty and if the user is not running the installer, Show error
        if(!$config && !$isInstalling) {
            if(!DB::connection()) {
                return Response::configurationError("Oops! The database cant be read!", "Database Connection error");
            } else {
                return Response::configurationError("Oops! The database is not configured properly!", "Database Configuration error");
            }
        }
		return $next($request);
	}

}
