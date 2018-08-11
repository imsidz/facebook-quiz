<?php

return [
    "route" => '/install-it',
    "routeName" => 'AppInstaller',
    'dbImport' => [
        'migration' => true,
        'seed'  =>  true,
        //'sqlFiles' => [ base_path('dbBasicStructure.sql'), base_path('dbBasicData.sql') ]
    ],
    'afterInstallRedirectUrl' => function() {
        return '/';
    },
    'processDBConfig' => function($config) {
        //Process the database config here. Called after validating the DB credentials.
        //Save the config in a file for use by the app
        return true;
    }
];