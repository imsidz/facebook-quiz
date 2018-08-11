<?php

Route::group([
    'middleware' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ]
], function ($router) {
    Route::match(array('GET', "POST"), Config::get('app-installer.route'), array(
            'as' => Config::get('app-installer.routeName'),
            'uses' => 'Jitheshgopan\AppInstaller\Controllers\InstallerController@index')
    );
});
