<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 06/01/16
 * Time: 7:50 PM
 */

namespace LanguageEditor;


interface LanguageEditorInterface {
    public function initialize($title, $description, $configKey);
    public function render();

    public function setTitle($title);
    /*
     * @return string $title
     */
    public function getTitle();
    public function setDescription($description);

    /*
     * @return string $description
     */
    public function getDescription();
    public function setConfigKey($key);
    /*
     * @return array $config
     */
    public function getData();
    public function readData();
    public function saveData();
}