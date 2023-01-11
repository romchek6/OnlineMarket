<?php

namespace core\base\settings;

use core\base\controllers\Singleton;

class Settings
{

    use Singleton;

    private $routes = [
        'admin' => [
            'alias'=>'admin',
            'path'=> 'core/admin/controllers/',
            'hrUrl'=> false,
            'routes'=>[

            ]
        ],
        'settings' => [
            'path'=>'core/base/settings/'
        ],
        'plugins'=>[
            'path' => 'core/plugins/',
            'hrUrl'=> false,
            'dir'=> false
        ],
        'user'=>[
            'path'=>'core/user/controllers/',
            'hrUrl'=> true,
            'routes'=>[

            ]
        ],
        'default'=>[
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod'=> 'outputData'
        ],

    ];

    private $expansion = 'core/admin/expansion/';

    private $messages = 'core/base/messages/';

    private $defaultTable = 'goods';

    private $formTemplates = PATH . 'core/admin/views/include/form_templates/';

    private $projectTables = [
        'articles'=>[],
        'filters'=>['name'=>'Фильтры'],
        'goods' =>['name'=>'Товары','img'=>'pages.png'],
        'pages' =>[]

    ];

    private $templateArr = [
        'text' =>['name'],
        'textarea' =>['content','keywords','description'],
        'radio'=>['visible'],
        'checkboxlist'=>['filters'],
        'select'=>['menu_position' ,'parent_id'],
        'img' =>['img', 'main_img'],
        'gallery_img' =>['gallery_img']
    ];

    private $fileTemplates = ['img' , 'gallery_img'];

    private $translate = [
        'name'=>['Название', 'Не более 100 символов'],
        'content'=>['Описание', 'Не более 200 символов'],
        'gallery_img'=>['Галерея изображений', 'Не более 20 изображений'],
        'img'=>['Главное изображение','image/*,image/jpeg,image/png,image/gif'],
        'menu_position'=>['Позиция в меню'],
        'parent_id'=>['Категории'],
        'visible' =>['Видимость','Отоброжение на сайте'],
        'keywords' =>['Ключевые слова','Не более 70 символов']
    ];

    private $radio = [
        'visible'=>['Нет','Да','default'=>'Да']
    ];

    private $rootItems = [
        'name'=>'Корневая',
        'tables'=>['goods' , 'filters']
    ];

    private $manyToMany =[
        'goods_filters' =>['goods' , 'filters' ] // 'type' => 'child' || 'root'
    ];

    private $blockNeedle = [
        'vg-rows'=>[],
        'vg-img'=>['main_img','img','gallery_img'],
        'vg-content'=>['content']
    ];

    private $validation = [
        'name' => ['empty'=>true, 'trim' =>true],
        'price' =>['int'=>true],
        'login' =>['empty' => true, 'trim'=>true],
        'password' => ['crypt' => true],
        'content' => ['count'=>200,'trim'=>true],
        'keywords' =>['count'=>70,'trim'=>true],
        'description' => ['count' =>160 , 'trim'=> true],
    ];

    static public function get($property){
        return self::instance()->$property;
    }

    public function clueProperties($class){

        $baseProperties = [];

        foreach ($this as $name => $item){

            $property = $class::get($name);

            if(is_array($property) && is_array($item)){

               $baseProperties[$name] =  $this ->arrayMergeRecursive($this->$name,$property);
               continue;
            }

            if(!$property) $baseProperties[$name] = $this->$name;
        }

        return $baseProperties;

    }

    public function arrayMergeRecursive(){

        $arrays = func_get_args();

        $base = array_shift($arrays);

        foreach ($arrays as $array){
            foreach ($array as $key => $value){
                if(is_array($value) && is_array($base[$key])){
                    $base[$key] = $this->arrayMergeRecursive($base[$key],$value);

                }else{
                    if(is_int($key)){
                        if (!in_array($value, $base)) $base[] = $value;
                        continue;
                    }
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

}