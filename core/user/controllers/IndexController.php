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

        $advantages = $this->model->get('advantages' , [
            'where'=>['visible' => 1],
            'order'=>['menu_position']
        ]);

        $news = $this->model->get('news' , [
            'where' =>['visible' =>1],
            'order' =>['date'],
            'order_direction' =>['DESC'],
            'limit'=> 3
        ]);

        $arrHits = [
            'hit' =>[
                'name' =>'Хиты продаж',
                'icon' => '<svg>
                        <use xlink:href="' . PATH . TEMPLATE . 'assets/img/icons.svg#hit"></use>
                    </svg>'
            ],
            'hot'=>[
                'name' =>'Горячие предложения',
                'icon' => '<svg>
                        <use xlink:href="' . PATH . TEMPLATE . 'assets/img/icons.svg#hot"></use>
                    </svg>'
            ],
            'sale'=>[
                'name' =>'Акции',
                'icon' => '%'
            ],
            'new'=>[
                'name' =>'Новинки',
                'icon' => 'new'
            ]
        ];

        $goods = [];

        foreach ($arrHits as $type => $item){

            $goods[$type] = $this->model->getGoods([
                'where' => [$type => 1 , 'visible' => 1],
                'limit' => 6
            ]);

        }

        return compact('sales', 'arrHits' , 'goods' , 'advantages' , 'news');

    }

}