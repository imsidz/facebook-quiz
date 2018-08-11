<?php
    class Theme {
        private $theme;
        public function __construct($themeName = "blue"){
            $this->theme = $themeName;
        }
        public function setTheme($themeName){
            $this->theme = $themeName;
        }
        public function getTheme(){
            return $this->theme;
        }
        public function getPrimaryColor(){
            switch($this->getTheme()){
                case "blue":
                    $color = '#1F91E2';
                    break;
                case "red":
                    $color = '#FF4545';
                    break;
                case "yellow":
                    $color = '#FFB632';
                    break;
                case "orange":
                    $color = '#FF6600';
                    break;
                case "green":
                    $color = '#0CC563';
                    break;
                case "purple":
                    $color = '#9A50A6';
                    break;
                default:
                    $color = '#222';
            }
            return $color;
        }
    }