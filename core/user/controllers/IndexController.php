<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;
use core\base\models\BaseModel;
use core\base\models\Crypt;

class IndexController extends BaseController{
    protected $name;

    protected function inputData(){

        $str = '1234567890';

        $jopa = Crypt::instance() ->encrypt($str);

        $dec_str = Crypt::instance()->decrypt($jopa);

        exit();
    }



}