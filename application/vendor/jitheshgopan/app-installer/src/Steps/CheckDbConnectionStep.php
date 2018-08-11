<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 8:41 PM
 */

namespace Jitheshgopan\AppInstaller\Steps;
use Config;
use DB;

class CheckDbConnectionStep extends AbstractStep{
    private $defaultDbConfig = [
        'host' => '',
        'database' => '',
        'username' => '',
        'password' => '',
    ];
    public function process($prevStep){

    }

    public function handler(){
        $sourceStep = $this->getSourceStep();
        $data = $sourceStep ? $sourceStep->getData() : $this->getData();
        $isConnected = $this->testDbConnection($data);
        if(!$isConnected)
            $this->errors[] = "DB connection unsuccessful! Please check your database details";
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
        return $isConnected;
    }

    public function testDbConnection($dbData){
        $customDbConfig = array_merge($this->defaultDbConfig, $dbData);

        $dbConfig = Config::get('database');
        $dbConfig['connections']['mysql'] = array_merge($dbConfig['connections']['mysql'], $customDbConfig);
        Config::set('database', $dbConfig);

        try {
            DB::reconnect('mysql');
            $dbConnection = DB::connection('mysql')->getPdo();
        } catch(\PDOException $e){
            return false;
        }
        return true;
    }
}