<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 28/03/15
 * Time: 4:28 AM
 */

class AdminShortCodesController extends BaseController{

    public function index() {
        $shortCodeEngine = \App::make('shortCodeEngine');
        $shortCodes = $shortCodeEngine->getShortCodes();
        return View::make('admin.shortCodes', [
            'shortCodes'    =>  $shortCodes
        ]);
    }

}