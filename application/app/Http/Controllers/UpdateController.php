<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 29/03/15
 * Time: 1:31 AM
 */
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use \VisualAppeal\AutoUpdate;

class UpdateController extends BaseController{

    const UPDATES_DIR = 'updates';
    private $logPath = '';
    private $simulate = false;
    public function __construct() {
        $this->logPath = storage_path('update.log');
    }

    /**
     * @param boolean $simulate
     */
    public function setSimulate($simulate)
    {
        $this->simulate = $simulate;
    }

    public function getUpdatesDirPath() {
        return public_path(self::UPDATES_DIR);
    }
    /*public function index() {
        $updatePath = Input::get('update-path');
        if(!$updatePath) {
            $updateDirs = File::glob($this->getUpdatesDirPath() . '/update-*', GLOB_ONLYDIR);
            return (View::make('admin.updates', [
                'updateDirs'    =>  $updateDirs
            ]));
        } else {
            $updateMethod = require($updatePath . '/update.php');
            call_user_func($updateMethod);
        }
    }*/

    /*
     * Check for updates. Shows Action button to update
     */
    public function index()
    {
        $update = $this->_getUpdateHandler();
        $updateAvailable = false;
        //Check for a new update
        if ($update->checkUpdate() === false) {
            return (View::make('admin.update.landing', [
                'log'    =>  $this->_getLogs(),
                'error' =>  true,
                'message' => 'Unable to check for updates!'
            ]));
        }
        $latestVersion = $update->getLatestVersion();
        if ($update->newVersionAvailable()) {
            $message = "Update Available";
            $updateAvailable = true;
        } else {
            $message = "You are already on the latest version";
            $updateAvailable = false;
        }
        return (View::make('admin.update.landing', [
            'log'    =>  $this->_getLogs(),
            'updateVersion' =>  $latestVersion,
            'message'   =>  $message,
            'updateAvailable' => $updateAvailable
        ]));
    }


    /*
     * Run the update
     */
    public function doUpdate()
    {
        //Updates to the latest version
        //$version = Input::get('version');
        $update = $this->_getUpdateHandler();

        //Check for a new update
        if ($update->checkUpdate() === false) {
            return (View::make('admin.update.result', [
                'log'    =>  $this->_getLogs(),
                'error' =>  true,
                'message' => 'Unable to check for updates!'
            ]));
        }
        $latestVersion = $update->getLatestVersion();
        if ($update->newVersionAvailable()) {
            $versionsToUpdate = array_map(function($version) {
                return (string) $version;
            }, $update->getVersionsToUpdate());
            $update->onEachUpdateFinish(array($this, '_onUpdateCallback'));
            // Set the first argument (simulate) to "false" to install the update
            // i.e. $update->update(false);
            $result = $update->update($this->simulate);
            if ($result === true) {
                $message = 'Update successful!';
                if($this->simulate)
                    $message .= ' (SIMULATION ONLY - Not actually updated)';
            } else {
                $message = 'Update failed: ' . $result . '!';
                if($this->simulate)
                    $message .= ' (SIMULATION ONLY)';
                if ($result = AutoUpdate::ERROR_SIMULATE) {
                    return (View::make('admin.update.result', [
                        'log'    =>  $this->_getLogs(),
                        'error' =>  true,
                        'message' => $message
                    ]));
                }
            }
        } else {
            $message = 'Current Version is already up to date';
        }
        return (View::make('admin.update.result', [
            'log'    =>  $this->_getLogs(),
            'updateVersion' =>  $latestVersion,
            'message'   =>  $message
        ]));
    }
    /*
     * Ajax route to get update details
     */
    public function getUpdateDetails()
    {
        $updateVersion = Input::get('version');
        $updateDetailsUrl = Config::get('appMeta.updateDetailsBaseUrl');
        $updateDetailsUrl .= $updateVersion . '.html';
        return file_get_contents($updateDetailsUrl);
    }

    public function _getLogs()
    {
        return nl2br(file_get_contents($this->logPath));
    }

    public function _getUpdateHandler()
    {
        $updateBasePath = CMS_PACKED_MODE ? base_path('../') : base_path();
        $update = new AutoUpdate(storage_path('updates-temp'), $updateBasePath, 60);
        $update->setCurrentVersion(Config::get('appMeta.version'));
        $update->setUpdateUrl(Config::get('appMeta.updateCheckUrl')); //Replace with your server update directory
        File::delete($this->logPath, '');
        $logHandler = new Monolog\Handler\StreamHandler($this->logPath);
        $update->addLogHandler($logHandler);
        return $update;
    }

    public function _onUpdateCallback($updateVersion)
    {
        \Artisan::call('migrate', array('--force' => true));
        $updateScriptPath = app_path('update.php');
        if(\File::exists($updateScriptPath)) {
            require_once($updateScriptPath);
        }
    }
}