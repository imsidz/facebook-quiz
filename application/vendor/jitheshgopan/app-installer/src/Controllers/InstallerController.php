<?php
namespace Jitheshgopan\AppInstaller\Controllers;
use Illuminate\Support\Facades\Config;
use Jitheshgopan\AppInstaller\Installer;


class InstallerController extends \Illuminate\Routing\Controller {
    public function index() {
        //If install.php is not present of database config is already set, Abort - disable installation
        if(!function_exists('install_path')) {
            $installFilePath = public_path('install.php');
        } else {
            $installFilePath = install_path('install.php');
        }
        if(!file_exists($installFilePath)) {
            die("Sorry! Installation disabled!");
        }

        //$stages = Config::get('app-installer.stages');
        $installer = new Installer();

        //Requirements check stage
        //$this->_setupRequirementsStage($installer);

        //Directory permissions stage
        //$this->_directoryPermissionsStage($installer);

        //Database config stage
        $this->_setupDbConnectionStage($installer);

        //Import database files
        $this->_setupImportDbStep($installer);

        //Finish
        $this->_setupFinishStage($installer);

        try {
            return $installer->run();
        } catch (InstallerException $e){
            return Response::make($e->getMessage(), 400);
        }

    }

    /*public function _setupRequirementsStage($installer){
        $requirementsStage = $installer->addStage("System requirements", [
            'banner' => Installer::asset('images/system.png')
        ]);

        //Php version
        $requirementsStage->addPhpVersionCheckStep(\Config::get('appMeta.minimumPHPVersion'), '>=');

        //GD extension check
        $gdExtensionCheck = $requirementsStage->addStep("Checking GD Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $gdExtensionCheck->check('gd');

        //PDO extension
        $pdoCheck = $requirementsStage->addStep("Checking PDO Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $pdoCheck->check('pdo');

        //CURL extension
        $curlCheck = $requirementsStage->addStep("Checking CURL Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $curlCheck->check('curl');

        //allow_url_fopen settings
        $allowUrlFopenCheck = $requirementsStage->addStep("Checking allow_url_fopen settings", [
            'type' => 'IniGetCheck'
        ]);
        $allowUrlFopenCheck->check('allow_url_fopen', null, "'allow_url_fopen' must be enabled. You may comtact your hosting support or change it in php.ini(if you have access to it)");
    }*/

    public function _setupDbConnectionStage($installer){
        $dbConfigStage = $installer->addStage("Database connection", [
            'banner' => \Jitheshgopan\AppInstaller\Installer::asset('images/database.png')
        ]);
        $dbConfigStep = $dbConfigStage->addDbConfigStep('mysql', [
            'configFilePath' => config_path('config.php')
        ]);
    }

    public function _setupImportDbStep($installer){
        $dbImportStage = $installer->addStage("Loading database with necessary data", [
            'banner' => Installer::asset('images/importdb.png')
        ]);
        $dbImportStep = $dbImportStage->addStep('Importing', array_merge([
            'type' => 'ImportDb'
        ], \Config::get('app-installer.dbImport')));
    }

    /*public function _directoryPermissionsStage($installer) {
        $directoryWritableStage = $installer->addStage("Directory permissions", [
            'banner' => Installer::asset('images/permissions.png')
        ]);
        //Is directory writable check
        $configCheck = $directoryWritableStage->addStep("Checking if the 'config' directory is writable", [
            'type' => 'WritableCheck'
        ]);

        $langCheck = $directoryWritableStage->addStep("Checking if the 'lang' directory is writable", [
            'type' => 'WritableCheck'
        ]);

        $mediaCheck = $directoryWritableStage->addStep("Checking if the 'media' directory is writable", [
            'type' => 'WritableCheck'
        ]);

        $uploadsCheck = $directoryWritableStage->addStep("Checking if the 'media/uploads' directory is writable", [
            'type' => 'WritableCheck'
        ]);

        $configCheck->checkWritable(config_path());
        $langCheck->checkWritable(app('path.lang'));
        $mediaCheck->checkWritable(content_path('media'));
        $uploadsCheck->checkWritable(content_path('media/uploads'));
    }*/

    public function _setupFinishStage($installer){
        $afterInstallRedirectUrl = \Config::get('app-installer.afterInstallRedirectUrl');
        if(is_callable($afterInstallRedirectUrl)) {
            $afterInstallRedirectUrl = call_user_func($afterInstallRedirectUrl);
        }
        $finishStage = $installer->addFinishStage("Installation Complete", [
            'proceedUrl' => $afterInstallRedirectUrl,
            'proceedUrlText' => 'Go to admin panel'
        ]);
    }
}