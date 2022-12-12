<?php

namespace core\admin\controllers;

class ShowController extends BaseAdmin
{

    protected function inputData(){

        $this->execBase();

        $this->createTableData();

        $this->createDate(['fields'=>'content']);

    }

    protected function outputData(){

    }

}