<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController
{

    protected function inputData(){

        $db = Model::instance();

        $table = 'articles';

        $res = $db->get($table,[
            'fields' =>['id'],
            'where' =>['name'=>"Roma"],
            'limit' =>1
        ])[0];

        exit('id = ' .  $res['id']);

    }

}