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
        return route('adminLogin');
    },
    'processDBConfig' => function($dbConfig) {
        //Process the database config here. Called after validating the DB credentials.
        //Save the config in a file for use by the app
        $configPath = install_path('config.php');
        if(!file_exists($configPath))
            throw new Exception("Config file doesn't exist: {$configPath} .");
        $config = require($configPath);
        $config['DB_HOST'] = $dbConfig['host'];
        $config['DB_DATABASE'] = $dbConfig['database'];
        $config['DB_USERNAME'] = $dbConfig['username'];
        $config['DB_PASSWORD'] = isset($dbConfig['password']) ? $dbConfig['password'] : '';
        $configFileContent = "<?php \n return " . var_export($config, true) . ";";
        if(file_put_contents($configPath, $configFileContent))
            return true;
        else
            throw new Exception("Unable to write config file: {$configPath} . Possibly a permission issue");
    }
];