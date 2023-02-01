<?php

namespace core\user\controllers;

use core\user\models\Model;

abstract class BaseUser extends \core\base\controllers\BaseController
{
    protected $model;

    protected $table;

    protected $set;

    protected $menu;

    protected $breadcrumbs;

    /*Проектные свойства*/

    protected $socials;

    /*Проектные свойства*/

    protected function inputData(){

        $this->init();

        !$this->model && $this->model = Model::instance();

        $this->set = $this->model->get('settings' , [
            'where' =>['menu_position' => 1]
        ]);

        $this->set && $this->set = $this->set[0];

        $this->menu['catalog'] = $this->model->get('catalog',[
            'where' =>['visible' => 1 , 'parent_id' => null],
            'order' => ['menu_position']
        ]);

        $this->menu['information'] = $this->model->get('information' , [
           'where' => ['visible' => 1, 'show_top_menu' => 1],
           'order' => ['menu_position']
        ]);

        $this->socials = $this->model->get('socials' ,[
            'where' => ['visible' => 1],
            'order' => ['menu_position']
        ]);


    }

    protected function outputData(){

        $args = func_get_arg(0);
        $vars = $args ? $args : [];

        $this->breadcrumbs = $this->render(TEMPLATE . 'include/breadcrumbs');

        if(!$this->content){

//            if(!$this->template) $this->template = ADMIN_TEMPLATE . 'show';

            $this->content = $this->render($this->template , $vars);
        }

        $this->header = $this->render(TEMPLATE . 'include/header', $vars);
        $this->footer = $this->render(TEMPLATE. 'include/footer', $vars);

        return $this->render(TEMPLATE . 'layout/default');

    }

    protected function img($img = '' , $tag = false){

        if(!$img && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMAGE_DIRECTORY)){

            $dir = scandir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMAGE_DIRECTORY);

            $imgArr = preg_grep('/'.$this->getController().'\./i' , $dir)?:preg_grep('/default\./i' , $dir);

            $imgArr && $img = DEFAULT_IMAGE_DIRECTORY . '/' . array_shift($imgArr);

        }

        if($img){

            $path = PATH . UPLOAD_DIR . $img;

            if(!$tag){

                return $path;

            }

            echo '<img src="'. $path .'" alt="image" title="image">';

        }

        return '';

    }

    protected function alias($alias = '', $queryString = ''){

        $str = '';

        if($queryString){

            if(is_array($queryString)){

                foreach ($queryString as $key => $item){

                    $str .= (!$str ? '?' :'&');

                    if(is_array($item)){

                        $key .= '[]';

                        foreach ($item as $k => $v){

                            $str .= $key . '=' . $v . (!empty($item[$k + 1]) ? '&' : '');

                        }

                    }else{

                        $str .= $key . '=' . $item;

                    }

                }

            }else{

                if(strpos($queryString , '?') === false) $str = '?' . $str;

                $str .= $queryString;

            }

        }

        if(is_array($alias)){

            $aliasStr = '';

            foreach ($alias as $key => $item){

                if(!is_numeric($key) && $item){

                    $aliasStr .=$key . '/' . $item . '/';

                }elseif($item){

                    $aliasStr .= $item . '/';

                }

            }

            $alias = trim($aliasStr , '/');

        }

        if(!$alias || $alias === '/') return PATH . $str;

        if(preg_match('/^\s*https?:\/\//i' , $alias)) return $alias . $str;

        return preg_replace('/\/{2,}/' , '/' , PATH . $alias . END_SLASH . $str);

    }

    protected function wordsForCounter($counter , $arrElement = 'years'){

        $arr = [
            'years' =>[
                'лет',
                'год',
                'года'
            ]
        ];

        if(is_array($arrElement)){

            $arr = $arrElement;

        }else{

            $arr = $arr[$arrElement] ?? array_shift($arr);

        }

        if(!$arr) return null;

        $char= (int)substr($counter , -1);

        $counter = (int)substr($counter, - 2);

        if(($counter >= 10 && $counter <= 20 ) || ($char >= 5 && $char <= 9) || !$char) return $arr[0] ?? null;
        elseif ($char ===1) return $arr[1] ?? null;
        else return $arr[2] ?? null;

    }

    protected function showGoods($data , $parameters , $template = 'goodsItem'){

        if(!empty($data)){

            echo $this->render(TEMPLATE . 'include/' . $template , compact('data' , 'parameters'));

        }

    }

    protected function dateFormat($date){

        if(!$date){

            return;

        }

        $daysArr = [
            'Sunday'=>'Воскресенье',
            'Monday'=>'Понедельник',
            'Tuesday'=>'Вторник',
            'Wednesday'=>'Среда',
            'Thursday'=>'Четверг',
            'Friday'=>'Пятница',
            'Saturday'=>'Суббота'
        ];

        $monthsArr = [
            1=>'Январь',
            2=>'Февраль',
            3=>'Март',
            4=>'Апрель',
            5=>'Май',
            6=>'Июнь',
            7=>'Июль',
            8=>'Август',
            9=>'Сентябрь',
            10=>'Октябрь',
            11=>'Ноябрь',
            12=>'Декабрь',
        ];

        $dateArr = [];

        $dateData = new \DateTime($date);

        $dateArr['year'] = $dateData->format('Y');

        $dateArr['month'] = $monthsArr[$this->clearNum($dateData->format('m'))];

        $dateArr['monthFormat'] = preg_match('/т$/u', $dateArr['month']) ?$dateArr['month'] . 'а' :
            preg_replace('/[ьй]/u' , 'я' , $dateArr['month']);

        $dateArr['weekDay'] = $daysArr[$dateData->format('l')];

        $dateArr['day'] = $dateData->format('d');

        $dateArr['time'] = $dateData->format('H:i:s');

        $dateArr['format'] = mb_strtolower($dateArr['day']) . ' ' .
            $dateArr['monthFormat'] . ' ' . $dateArr['year'];

        return $dateArr;

    }

    protected function pagination($pages){

        $str = $_SERVER['REQUEST_URI'];

        if(preg_match('/page=\d+/i' , $str)){

            $str = preg_replace('/page=\d+/i' , '' , $str);

        }

        if(preg_match('/(\?&)|(\?amp;)/i' , $str)){

            $str = preg_replace('/(\?&)|(\?amp;)/i' , '?' , $str);

        }

        $basePageStr = $str;

        if(preg_match('/\?(.)?/i' , $str , $matches)){

            if(!preg_match('/&$/' , $str) && !empty($matches[1])){

                $str .= '&';

            }else{

                $basePageStr = preg_replace('/(\?$)|(&$)/' , '' , $str);

            }

        }else{

            $str .= '?';

        }

        $str .='page=';

        $firstPageStr = !empty($pages['first']) ? ($pages['first'] === 1 ? $basePageStr : $str . $pages['first']) : '';

        $backPageStr = !empty($pages['back']) ? ($pages['back'] === 1 ? $basePageStr : $str . $pages['back']) : '';

        if(!empty($pages['first'])){

            echo <<<HEREDOC
                            <a href="$firstPageStr" class="catalog-section-pagination__item">
                                <<
                            </a>
HEREDOC;

        }

        if(!empty($pages['back'])){

            echo <<<HEREDOC
                            <a href="$backPageStr" class="catalog-section-pagination__item">
                                <
                            </a>
HEREDOC;

        }

        if(!empty($pages['previous'])){

            foreach ($pages['previous'] as $item){

                $href = $item === 1 ? $basePageStr : $str . $item;

                echo <<<HEREDOC
                            <a href="$href" class="catalog-section-pagination__item">
                                $item
                            </a>
HEREDOC;

            }

        }

        if(!empty($pages['current'])){

            echo <<<HEREDOC
                            <a href="" class="catalog-section-pagination__item pagination-current">
                                ${pages['current']}
                            </a>
HEREDOC;

        }

        if(!empty($pages['next'])){

            foreach ($pages['next'] as $item){

                $href = $str . $item;

                echo <<<HEREDOC
                            <a href="$href" class="catalog-section-pagination__item">
                                $item
                            </a>
HEREDOC;

            }

        }

        if(!empty($pages['forward'])){

            $href = $str . $pages['forward'];

            echo <<<HEREDOC
                            <a href="$href" class="catalog-section-pagination__item">
                                >
                            </a>
HEREDOC;

        }

        if(!empty($pages['last'])){

            $href = $str . $pages['last'];

            echo <<<HEREDOC
                            <a href="$href" class="catalog-section-pagination__item">
                                >>
                            </a>
HEREDOC;

        }

    }

}