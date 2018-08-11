<?php namespace App\Http\Middleware;

use Closure;

class AdminAuth {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        $admin = \Session::get('admin');

        /*if (!$admin && !Input::get('logmein'))
        {
            return Response::notFound();
        } else */

        if(!$admin) {
            if(\Request::ajax()) {
                return \Response::make('You have been logged out or your session has expired. Please login on another tab and try again.<br><br><a target="_blank" href="'. route('adminLogin') .'" class="btn btn-success">Login again</a></a>', 400);
            } else{
                return \Redirect::route('adminLogin', ['redirect' => urlencode(\Request::path())]);
            }
        }
		return $next($request);
	}

}
