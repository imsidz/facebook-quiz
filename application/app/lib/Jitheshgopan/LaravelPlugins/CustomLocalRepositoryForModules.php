<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 01/04/17
 * Time: 1:06 PM
 */

namespace Jitheshgopan\LaravelPlugins;


use Caffeinated\Modules\Repositories\LocalRepository;

/*
 * Custom repository to override the default manifest file name (module.json)
 */

class CustomLocalRepositoryForModules extends LocalRepository
{
    protected function getManifestPath($slug)
    {
        return $this->getModulePath($slug).'plugin.json';
    }

}