<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:37 AM
 */

namespace Jitheshgopan\AppInstaller\Steps;


use Jitheshgopan\AppInstaller\Exceptions\InstallerException;
use Jitheshgopan\AppInstaller\Installer;
use \DB;

class ImportDbStep extends AbstractStep{
    public function process($prevStep) {
        $options = $this->getOptions();
        //Processing imports in the order - Migration, seed, sql imports
        if($options['migration']) {
            \Artisan::call('migrate', array('--force' => true));
        }

        if($options['seed']) {
            \Artisan::call('db:seed', array('--force' => true));
        }

        if(!empty($options['sqlFiles'])) {
            if(is_string($options['sqlFiles']))
                $options['sqlFiles'] = array($options['sqlFiles']);
            foreach ($options['sqlFiles'] as $sqlFile) {
                if(!file_exists($sqlFile)) {
                    $this->errors[] = "Error importing Database. SqlFile : '{$sqlFile}' doesn't exist.";
                    break;
                }
                try{
                    $this->importDbFile($sqlFile);
                } catch(InstallerException $e) {
                    $this->errors[] = $e->getMessage();
                    break;
                }
            }
        }
        if($this->hasError()){
            $this->renderErrorView();
            return false;
        }
    }

    public function handler() {
        return !$this->hasError();
    }

    public function importDbFile($filePath) {
        // Temporary variable, used to store current query
        $templine = '';
        if(!file_exists($filePath)) {
            throw new InstallerException("Error importing DB from file : \"" . $filePath . "\". File doesn't exist");
        }
        // Read in entire file
        $lines = file($filePath);
        // Loop through each line
        foreach ($lines as $line)
        {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';')
            {
                // Perform the query
                try{
                    DB::unprepared($templine);
                } catch (\PDOException $e) {
                    throw new InstallerException("Error running query : \"" . substr($templine, 0, 120) . '...' . "\". " . $e->getMessage());
                }
                // Reset temp variable to empty
                $templine = '';
            }
        }
        return true;
    }
}