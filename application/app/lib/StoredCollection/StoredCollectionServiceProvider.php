<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 22/12/15
 * Time: 11:20 PM
 */

namespace StoredCollection;


use Illuminate\Support\ServiceProvider;

class StoredCollectionServiceProvider extends ServiceProvider{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('StoredCollection\Contracts\StoredCollectionStore', 'StoredCollection\Store\CommonConfigStore');
    }

}