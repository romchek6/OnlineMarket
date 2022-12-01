<?php

namespace core\base\controllers;

use core\base\settings\Settings ;
use core\base\settings\ShopSettings;

class RouteController
{

    static private $_instance;

    private function __clone()
    {
    }

    static public function getInstance(){
        if(self::$_instance instanceof  self){
            return self::$_instance;
        }

        return  self::$_instance = new self;
    }

    private function __construct()
    {
        $s1 = ShopSettings::get('dsadas');
        $s = Settings::get('routes');
        exit();

    }

}