<?php

namespace core\user\controllers;

use core\base\controllers\BaseAjax;

class AjaxController extends BaseUser
{

    public function ajax(){

        if(isset($this->ajaxData['ajax'])){

            $this->inputData();

            foreach ($this->ajaxData as $key => $item)
                $this->ajaxData[$key] = $this->clearStr($item);

            switch ($this->ajaxData['ajax']){

                case 'catalog_quantities':

                    $qty = $this->clearNum($this->ajaxData['qty'] ?? 0);

                    $qty && $_SESSION['quantities'] = $qty;

                    return 0 ;

                    break;

                case 'add_to_cart':

                    return $this->_addToCart();

                    break;

            }

        }

        return json_encode(['success' => '0' , 'message'=>'No ajax variable']);

    }

    protected function _addToCart(){

        return $this->addToCart($this->ajaxData['id'] ?? null, $this->ajaxData['qty'] ?? 1);

    }

}