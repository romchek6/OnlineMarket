<?php

namespace core\admin\models;

abstract  class BaseModelMethods
{

    //    создание полей

    protected function createFields($set,$table =false){

        $set['fields'] = is_array($set['fields']) && !empty($set['fields'])? $set['fields']:['*'];

        $table = $table ? $table . '.' :'';

        $fields = '';

        foreach ($set['fields'] as $field){
            $fields .= $table . $field. ',';
        }

        return $fields;
    }

//    создание сортировки

    protected function createOrder($set,$table =false){

        $table = $table ? $table . '.' :'';

        $order_by = '';

        if(is_array($set['order']) && !empty($set['order'])){

            $set['order_direction'] = is_array($set['order_direction']) && !empty($set['order_direction'])? $set['order_direction']:['ASC'];

            $order_by ='ORDER BY ';
            $direct_count = 0;

            foreach ($set['order'] as $order){
                if($set['order_direction'][$direct_count]){
                    $order_direction = strtoupper($set['order_direction'][$direct_count]);
                    $direct_count++;
                }else{
                    $order_direction = strtoupper($set['order_direction'][$direct_count-1]);
                }

                if(is_int($order)) $order_by .= $order . ' ' . $order_direction . ',';
                else $order_by .= $table . $order . ' ' . $order_direction . ',';

            }

            $order_by =  rtrim($order_by, ',');
        }

        return $order_by;
    }

//    создание условия

    protected function createWhere($set,$table =false, $instruction = 'WHERE'){

        $table = $table ? $table . '.' :'';

        $where = '';

        if(is_array($set['where']) && !empty($set['where'])){

            $set['operand'] = is_array($set['operand']) && !empty($set['operand'])?$set['operand']:['='];
            $set['condition'] = is_array($set['condition']) && !empty($set['condition'])?$set['condition']:['AND'];

            $where = $instruction;

            $o_count = 0;
            $c_count = 0;

            foreach ($set['where'] as $key=>$item){

                $where .= ' ';

                if($set['operand'][$o_count]){
                    $operand = $set['operand'][$o_count];
                    $o_count++;
                }else{
                    $operand = $set['operand'][$o_count-1];
                }

                if($set['condition'][$c_count]){
                    $condition = $set['condition'][$c_count];
                    $c_count++;
                }else{
                    $condition = $set['condition'][$c_count-1];
                }

                if($operand ==='IN' || $operand === 'NOT IN'){
                    if(is_string($item) && strpos($item , 'SELECT') === 0){
                        $in_str = $item;
                    }else{
                        if(is_array($item)) $temp_item = $item;
                        else $temp_item = explode(',',$item);

                        $in_str = '';

                        foreach ($temp_item as $v){
                            $in_str.= "'" . addslashes(trim($v)) . "',";
                        }

                    }
                    $where .= $table . $key .' ' . $operand . ' (' . rtrim($in_str,',') . ') ' . $condition;

                }elseif(strpos($operand, 'LIKE') !== false ){

                    $like_template = explode('%', $operand);

                    foreach ($like_template as $lt_key=> $lt){
                        if(!$lt){
                            if(!$lt_key){
                                $item = '%' . $item;
                            }else{
                                $item .= '%';
                            }
                        }


                    }

                    $where .= $table . $key . ' LIKE ' . "'" . addslashes($item) . "' $condition";

                }else{

                    if(strpos($item , 'SELECT')===0){
                        $where .= $table . $key . ' ' . $operand . ' (' . $item . ') ' . $condition;
                    }else{
                        $where .= $table . $key . ' ' . $operand . " '" . addslashes($item) . "' " . $condition;
                    }

                }

            }

            $where = substr($where , 0, strrpos($where, $condition));

        }

        return $where;

    }

//    создание Join

    protected function createJoin($set,$table,$new_where = false){

        $fields = '';
        $join = '';
        $where = '';

        if($set['join']){

            $join_table = $table;

            foreach ($set['join'] as $key => $item){

                if(is_int($key)){
                    if(!$item['table']) continue;
                    else $key = $item['table'];
                }

                if($join) $join .= ' ';

                if($item['on']){

                    $join_fields = [];
                    $a = $item['on']['fields']?count($item['on']['fields']):0;
                    switch (2){
                        case $a:
                            $join_fields = $item['on']['fields'];
                            break;

                        case count($item['on']):
                            $join_fields = $item['on'];
                            break;

                        default:
                            continue 2;
                            break;

                    }

                    if(!$item['type']) $join.= 'LEFT JOIN ';
                    else $join.= trim(strtoupper($item['type'])) . ' JOIN ';

                    $join .= $key . ' ON ';

                    if($item['on']['table']) $join .= $item['on']['table'];
                    else $join.= $join_table;

                    $join .='.' . $join_fields[0] . '=' . $key . '.' .$join_fields[1];

                    $join_table = $key;

                    if($new_where){
                        if($item['where']){
                            $new_where = false;
                        }

                        $group_condition = 'WHERE';
                    }else{
                        $group_condition = $item['group_condition'] ? strtoupper($item['group_condition']) : 'AND';
                    }

                    $fields .= $this->createFields($item, $key);
                    $where .= $this->createWhere($item, $key,$group_condition);

                }
            }
        }

        return compact('fields','join' ,'where');

    }

    protected function createInsert($fields,$files,$except){

        if(!$fields){
            $fields = $_POST;
        }

        $insert_arr =[];

        if($fields){

            $sql_func =['NOW()'];

            foreach ($fields as $row=>$value){

                if($except && in_array($row , $except)) continue;

                $insert_arr['fields'] .= $row . ',';

                if(in_array($value, $sql_func)){
                    $insert_arr['values'] .= $value . ',';
                }else{
                    $insert_arr['values'] .= "'" . addslashes($value) . "',";
                }
            }

        }

        if($files){

            foreach ($files as $row => $file) {



            }

        }
    }
}