<?php
View::composer('*', function($view) {
    View::share([
        'sharingNetworkIcons'  =>  [
            'facebook'      =>  'facebook',
            'twitter'       =>  'twitter',
            'googleplus'    =>  'google-plus',
            'pinterest'     =>  'pinterest',
            'tumblr'        =>  'tumblr',
            'reddit'        =>  'reddit',
            'vk'            =>  'vk',
            'ok'            =>  'odnoklassniki'
        ]
    ]);
});