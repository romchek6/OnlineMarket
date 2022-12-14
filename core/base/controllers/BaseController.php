<?php

namespace core\base\controllers;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;

abstract class BaseController
{

    use \core\base\controllers\BaseMethods;

    protected $header;
    protected $content;
    protected $footer;

    protected $page;

    protected $errors;

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    protected $template;
    protected $styles;
    protected $scripts;

    public function route(){
        $controller = str_replace('/','\\',$this->controller);

        try{
            $object = new \ReflectionMethod($controller, 'request');

            $argc = [
                'parameters'=>$this->parameters,
                'inputMethod'=>$this->inputMethod,
                'outputMethod'=>$this->outputMethod
            ];

            $object->invoke(new $controller,$argc);
        }catch (\ReflectionException $e){
            throw new RouteException($e->getMessage());
        }

    }

    public function request($args){
        $this->parameters = $args['parameters'];

        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];

        $data = $this->$inputData();

        if(method_exists($this,$outputData)){
            $page = $this->$outputData($data);
            if($page) $this->page = $page;
        } elseif($data){
            $this->page = $data;
        }

        if($this->errors){
            $this->writeLog($this->errors);
        }

        $this->getPage();
    }

    protected function render($path='',$parameters =[]){

        @extract($parameters);

        if(!$path){

            $class = new \ReflectionClass($this);

            $space = str_replace('\\','/',$class->getNamespaceName() . '\\');
            $routes = Settings::get('routes');

            if($space===$routes['user']['path']) $template = TEMPLATE;
                else $template = ADMIN_TEMPLATE;


            $path = $template . explode('controller',strtolower($class->getShortName()))[0];
        }

        ob_start();

        if(!@include_once $path . '.php') throw new RouteException('Отсутствует шаблон - ' . $path);

        return ob_get_clean();

    }

    protected function getPage(){

        if(is_array($this->page)){
            foreach ($this->page as $block) echo $block;
        }else{

            echo $this->page;
        }
        exit();
    }

    protected function init($admin = false){

        if(!$admin){
            if (USER_CSS_JS['styles']){
                foreach (USER_CSS_JS['styles'] as $style) $this->styles[] = PATH .TEMPLATE.trim($style, '/');
            }
            if (USER_CSS_JS['scripts']){
                foreach (USER_CSS_JS['scripts'] as $script) $this->scripts[] = PATH .TEMPLATE.trim($script, '/');
            }
        }else{
            if (ADMIN_CSS_JS['styles']){
                foreach (ADMIN_CSS_JS['styles'] as $style) $this->styles[] = PATH . ADMIN_TEMPLATE .trim($style, '/');
            }
            if (ADMIN_CSS_JS['scripts']){
                foreach (ADMIN_CSS_JS['scripts'] as $script) $this->scripts[] = PATH . ADMIN_TEMPLATE .trim($script, '/');
            }
        }

    }

}