<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\admin\models\Model;

class IndexController extends BaseController
{

    protected function inputData(){

        $db = Model::instance();

        $table = 'articles';

        $color = ['red','blue','black'];

        $res = $db->get($table,[
            'fields' =>['id','name',],
            'where' =>['name'=>'Roma,ivan' ,'id'=>'Roma', 'price'=>'457','car'=>'porshe', 'color'=> $color],
            'operand'=>['IN','%LIKE%','<>','=','NOT IN'],
            'condition'=>['AND','OR','AND'],
            'order'=>['name','price'],
            'order_direction'=>['DESC'],
            'Limit'=>'1'
        ]);

        exit();

    }

}