<?php

namespace core\base\models;

abstract  class BaseModelMethods
{

    protected $sqlFunc =['NOW()'];

    //    создание полей

    protected function createFields($set,$table =false){

        $set['fields'] = is_array($set['fields']) && !empty($set['fields'])? $set['fields']:['*'];

        $table = ($table && !$set['no_concat'] ) ? $table . '.' :'';

        $fields = '';

        foreach ($set['fields'] as $field){
            $fields .= $table . $field. ',';
        }

        return $fields;
    }

//    создание сортировки

    protected function createOrder($set,$table =false){

        $table = ($table && !$set['no_concat'] ) ? $table . '.' :'';

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

        $table = ($table && !$set['no_concat'] ) ? $table . '.' :'';

        $where = '';

        if(is_string($set['where'])){
            return $instruction . ' ' . trim($set['where']);
        }

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
        $tables ='';

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


                    }

                    if(!$item['type']) $join.= 'LEFT JOIN ';
                    else $join.= trim(strtoupper($item['type'])) . ' JOIN ';

                    $join .= $key . ' ON ';

                    if($item['on']['table']) $join .= $item['on']['table'];
                    else $join.= $join_table;

                    $join .='.' . $join_fields[0] . '=' . $key . '.' .$join_fields[1];

                    $join_table = $key;
                    $tables .= ', ' . trim($join_table);

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

        return compact('fields','join' ,'where','tables');

    }

    protected function createInsert($fields,$files,$except){

        $insert_arr =[];

        $insert_arr['fields'] = '(';

        $array_type = array_keys($fields)[0];

        if(is_int($array_type)){

            $check_fields = false;
            $count_fields = 0;

            foreach ($fields as $i => $item){

                $insert_arr['values'] .= '(';

                if(!$count_fields) $count_fields = count($fields[$i]);

                $j = 0;

                foreach ($item as $row =>$value){

                    if($except && in_array($row, $except)) continue;

                    if(!$check_fields) $insert_arr['fields'] .= $row  . ',';

                    if(in_array($value , $this->sqlFunc)){
                        $insert_arr['values'] .= $value . ',';
                    }elseif($value =='NULL' || $value === NULL){
                        $insert_arr['values'] .='NULL' . ',';
                    }else{
                        $insert_arr['values'] .= "'" . addslashes($value) . "',";
                    }


                    $j++;

                    if($j===$count_fields) break;
                }

                if($j < $count_fields){
                    for (;$j< $count_fields;$j++){
                        $insert_arr['values'] .= 'NULL,';
                    }
                }

                $insert_arr['values'] = rtrim($insert_arr['values'] , ',') . '),';

                if(!$check_fields) $check_fields = true;

            }

        }else{

            $insert_arr['values'] = '(';

            if($fields){

                foreach ($fields as $row =>$value){

                    if($except && in_array($row, $except)) continue;

                    $insert_arr['fields'] .= $row  . ',';

                    if(in_array($value , $this->sqlFunc)){
                        $insert_arr['values'] .= $value . ',';
                    }elseif($value =='NULL' || $value === NULL){
                        $insert_arr['values'] .='NULL' . ',';
                    }else{
                        $insert_arr['values'] .= "'" . addslashes($value) . "',";
                    }

                }

            }

            if($files){

                foreach ($files as $row =>$file){

                    $insert_arr['fields'] .= $row  . ',';

                    if(is_array($file)) $insert_arr['values'] .= "'" . addslashes(json_encode($file)) . "',";
                        else $insert_arr['values'] .= "'" . addslashes($file) . "',";
                }

            }

            $insert_arr['values'] = rtrim($insert_arr['values'] , ',') . ')';

        }

        $insert_arr['fields'] = rtrim($insert_arr['fields'] , ',') . ')';
        $insert_arr['values'] = rtrim($insert_arr['values'] , ',');



        return $insert_arr;

    }

    protected function createUpdate($fields, $files ,$except){

        $update = '';

        if($fields){
            foreach ($fields as $row =>$value) {

                if($except && in_array($row , $except)) continue;

               $update .= $row . '=';

               if(in_array($value,$this->sqlFunc)){
                   $update.= $value .',';
               }elseif($value === null){
                   $update .= "NULL" . ",";
               }
               else{
                   $update .= "'" . addslashes($value) . "',";
               }

            }
        }

        if($files){

            foreach ($files as $row => $file) {

                $update .= $row . "=";

                if(is_array($file))  $update .= "'" . json_encode($file) . "',";
                    else $update .= "'" . addslashes($file) . "',";

            }

        }

        return rtrim($update, ',');

    }

}