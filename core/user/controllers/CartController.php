<?php

namespace core\user\controllers;

class CartController extends BaseUser
{

    protected $delivery;

    protected $payments;

    protected function inputData(){

        parent::inputData();

        $this->delivery = $this->model->get('delivery');
        $this->payments = $this->model->get('payments');

        if(!empty($this->parameters['alias']) && $this->parameters['alias']==='remove'){

            if(!empty($this->parameters['id'])){

                $this->deleteCartData($this->parameters['id']);

            }else{

                $this->clearCart();

            }

            $this->redirect($this->alias('cart'));

        }


    }

}