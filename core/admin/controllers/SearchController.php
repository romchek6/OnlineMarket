<?php

namespace core\admin\controllers;

use core\base\settings\Settings;

class SearchController extends BaseAdmin
{

    protected function inputData(){

        if(!$this->userId) $this->execBase();

        $this->search = true;

        $text = $this->clearStr($_GET['search']);

        $table = $_GET['search_table'];

        $this->data =$this->model->search($text , $table);

        $this->template = ADMIN_TEMPLATE . 'show';

        return $this->expansion();

    }



}