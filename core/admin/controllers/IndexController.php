<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController
{

    protected function inputData(){

        $db = Model::instance();

        $table = 'articles';

        $db->add($table, [
            'fields' =>['name'=>'dsadsadas','content'=>'ewqewqesad','price'=>7444]
        ]);

        exit();

    }

}