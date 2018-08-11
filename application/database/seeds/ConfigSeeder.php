<?php
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 12/12/15
 * Time: 2:33 PM
 */

class ConfigSeeder extends Seeder{

    public function run()
    {
        Eloquent::unguard();
        $configFilesPattern = __DIR__ . "/config/*";
        $configSeeds = \File::glob($configFilesPattern);
        foreach ($configSeeds as $configSeedFile) {
            $this->seedConfigFromFile($configSeedFile);
        }

    }

    public function seedConfigFromFile($file)
    {
        $configName = str_replace('.json', '', basename($file));
        SiteConfig::create([
            'name'  =>  $configName,
            'value' =>  $this->getConfigSeedContent($file)
        ]);
    }

    public function getConfigSeedContent($file)
    {
        return \File::get($file);
    }

}