<?php

define('VG_ACCESS' ,true);

header('Content-type:text/html;charset=utf-8');
session_start();

//error_reporting(0);

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';

use core\base\exceptions\RouteException;
use core\base\controllers\BaseRoute;
use core\base\exceptions\DbException;

try{
    BaseRoute::routeDirection();
}
catch (RouteException $e){
    exit($e->getMessage());
}
catch (DbException $e){
    exit($e->getMessage());
}