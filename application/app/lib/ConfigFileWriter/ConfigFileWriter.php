<?php
namespace ConfigFileWriter;
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 14/04/17
 * Time: 4:27 PM
 */
class ConfigFileWriter
{
    private $filePath;
    private $config;

    /**
     * ConfigFileWriter constructor.
     * @param $filePath string
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->loadConfigFromFile();
    }

    public function loadConfigFromFile()
    {
        $this->config = require($this->filePath);
    }

    public function write($config) {
        $configFileContent = "<?php \n return " . var_export($config, true) . ";";
        file_put_contents($this->filePath, $configFileContent);
    }

    public function update($configToChange = array())
    {
        $config = array_merge($this->config, $configToChange);
        $this->write($config);
    }
}