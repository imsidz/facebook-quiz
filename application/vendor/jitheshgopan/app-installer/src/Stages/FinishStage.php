<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/03/15
 * Time: 1:33 AM
 */

namespace Jitheshgopan\AppInstaller\Stages;


use Illuminate\Support\Facades\View;
use Jitheshgopan\AppInstaller\Installer;

class FinishStage extends AbstractStage{

    public function process(){
        $this->isComplete = true;
        $options = $this->getOptions();
        $proceedUrl = isset($options['proceedUrl']) ? $options['proceedUrl'] : null;
        $proceedUrlText = isset($options['proceedUrlText']) ? $options['proceedUrlText'] : null;
        $finishView = \View::make(Installer::view('finish'))->with([
            'proceedUrl' => $proceedUrl,
            'proceedUrlText' => $proceedUrlText,
        ]);
        return $finishView->render();
    }

    public function handler(){
        return true;
    }
}