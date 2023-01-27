<?php

namespace core\user\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\models\BaseModel;
use core\base\models\Crypt;

class IndexController extends BaseUser{

    protected $name;

    protected function inputData(){

        parent::inputData();

        $sales = $this->model->get('sales' , [
            'where'=>['visible' => 1],
            'order'=>['menu_position']
        ]);

        $arrHits = ['hit', 'sale' , 'new', 'hot'];

        $goods = [];

        foreach ($arrHits as $type){

            $goods[$type] = $this->model->getGoods([
                'where' => [$type => 1],
                'limit' => 6
            ]);

        }

        return compact('sales');

    }

}