<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 10/11/15
 * Time: 4:15 AM
 */

namespace Jitheshgopan\LaravelPlugins;


use Jitheshgopan\LaravelPlugins\Contracts\PluginInterface;
use \Artisan;
use Illuminate\Support\Arr;

class Plugin extends AbstractPlugin {

    protected $slug;
    protected $properties;
    protected $plugins;
    function __construct($slug, $properties, Plugins $plugins)
    {
        parent::__construct();
        $this->slug = $slug;
        $this->properties = $properties;
        $this->plugins = $plugins;
    }


    public function install()
    {
        //Temporarily enable it to load autoload files and register service providers
        $this->plugins->enable($this->slug, false);

        //loads autoload files and registers service providers for all enabled plugins
        $this->plugins->register();

        Artisan::call('module:migrate', ['slug' => $this->slug]);
        Artisan::call('module:seed', ['slug' => $this->slug]);
        $this->plugins->requirePluginFile($this->slug, 'install.php');
    }

    public function uninstall()
    {
        Artisan::call('module:migrate:rollback', ['slug' => $this->slug]);
        $this->plugins->requirePluginFile($this->slug, 'uninstall.php');
    }

    public function getProperty($key, $default = null)
    {
        return Arr::get($this->properties->all(), $key, $default);
    }

    public function get($key, $default = null)
    {
        return $this->getProperty($key, $default);
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

    public function isInstalled()
    {
        return $this->getProperty('installed', false);
    }
}