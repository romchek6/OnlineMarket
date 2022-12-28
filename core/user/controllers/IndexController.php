<?php

namespace core\user\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\models\BaseModel;
use core\base\models\Crypt;

class IndexController extends BaseController{

    protected $name;

    protected function inputData(){

        $model = Model::instance();

        $res = $model->get('teacher',[
            'where' =>['id'=>'28,33,34'],
            'operand'=>['IN'],
            'join'=>[
                'stud_teach' =>['on' =>['id' ,'teacher']] ,
                'students'=>[
                    'fields'=>['name as student_name'],
                    'on'=>['students' , 'id']
                ]
            ],
//            'join_structure' =>true
        ]);

        exit();
    }



}