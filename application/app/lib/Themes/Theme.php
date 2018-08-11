<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:15 AM
 */

namespace Themes;

use \Artisan;
use Illuminate\Support\Arr;

class Theme {

    protected $slug;
    protected $properties;
    protected $themes;
    function __construct($slug, $properties)
    {
        $this->slug = $slug;
        $this->properties = $properties;
        $this->themes = app('themes');
    }

    public function uninstall()
    {
        //Artisan::call('module:migrate:rollback', [$this->slug]);
        $this->themes->requireModuleFile($this->properties, 'uninstall.php');
    }

    public function getProperty($key, $default = null)
    {
        return Arr::get($this->properties->all(), $key, $default);
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }


    public function getImage()
    {
        if(!empty($this->properties['image'])) {
            return $this->properties['image'];
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

}