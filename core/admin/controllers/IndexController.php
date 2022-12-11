<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController
{

    protected function inputData(){

        $db = Model::instance();

        $table = 'articles';

        $res = $db->delete($table,[
           'where'=> ['id'=> 24],
            'join'=>[
                'students'=>[
                    'table'=>'students',
                    'on'=>['student_id','id']
                ]
            ]
        ]);

        exit();

    }

}