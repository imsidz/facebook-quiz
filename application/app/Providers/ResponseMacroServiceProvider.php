<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 11/11/15
 * Time: 6:35 PM
 */

namespace App\Providers;

use Response;
use Request;
use View;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider{

    public function boot()
    {
        Response::macro('error', function($message, $title = null, $errorCode =  500)
        {
            if(Request::ajax()){
                return Response::make($message, $errorCode);
            } else{
                return Response::view('errors.error', array('title' => $title, 'message' => $message), $errorCode);
            }
        });

        Response::macro('configurationError', function($message, $title = null, $errorCode =  500)
        {
            if(Request::ajax()){
                $response = $title .'<br>' . $message;
            } else{
                $response = View::make('errors.plainError')->with(array('title' => $title, 'message' => $message));
            }
            die($response);
        });

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}