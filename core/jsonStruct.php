<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 2018/2/1
 * Time: 下午2:01
 */

namespace phpspider\core;

//json数据 建表类库
class jsonStruct extends db
{
    private $config = array();
    private static $file = '';
    public static $error = '';
    private static $table = 'undefine';
    public function __construct()
    {

    }
    public function setJsonFile($file = ''){
        self::$file = $file;
    }
    public function setConnect($link_name,$config = array()){
        try{
        self::set_connect($link_name,$config);
        }catch (\Exception $e){
            self::$error = $e;
        }
    }
    public function setTable($table) {
        self::$table = $table;
    }
    public function jsonFiller($recursion = false)
    {
        if(!$recursion){
            if (is_file(self::$file)) {
                $json = file_get_contents(self::$file);
            }else {
                self::$error = self::$file." is not file";
                return 0;
            }
            if(is_string($json)) {
                $json = is_array(json_decode($json,true)) ? json_decode($json,true) :(array)json_decode($json,true);
            }
        }else {

            $json = func_get_arg(func_num_args()-1);

        }


        $table_key = [];
        $values = [];
        $used = false;
        $t = 1;
       // var_dump($json[0]);
        if($json)foreach ($json as $key => $value) {
//            if($t==5)var_dump($value[0]);
//            $t ++;
            //echo 'in';
            if(!is_numeric($key) && is_array($value) && count($value) >=5){
               // echo 'in2';
                //$arr[$key] = $json[$key];
                $this->jsonFiller($key,$value);
               // echo 'out2';

            }else {
                //echo 'in3';
               // echo $key;
                if($key === 0  && !$used){
                   // echo 'in';
                    foreach ($value as $kk =>$vv){
                        $table_key[] = ['table_key' => $kk, 'type' => $this->types($vv)];
                    }
                    $used = true;
                }else if(!is_numeric($key)){
                    $table_key[] = ['table_key' => $key, 'type' => $this->types($value)];
                }
            }
            $values[] = is_array($value) ? json_encode($value) : $value;
        }
       return $this->createTable($recursion,$table_key,$values);
    }

    private function createTable($recursion,$table_key,$values){
        if(self::$table == 'undefine') return 0;
       //echo 'in4';
        if(is_array($table_key) && is_array($values)) {
            $sql = 'CREATE TABLE %s (';
            $sql = sprintf($sql,$recursion?$recursion:self::$table);
            $sql .= '`id` int(11) unsigned NOT NULL AUTO_INCREMENT ';
            for ($i = 0;$i<count($table_key);$i++){

             $sql .= ',`'.$table_key[$i]['table_key'].'` '.$table_key[$i]['type'];

            }
           if($sql) $sql .= ', PRIMARY KEY (`id`))ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        }
        if($res = self::query($sql)) {
//            if(file_put_contents(__DIR__.'/../data/cache/create_'.microtime(1).'.sql',$sql)) {
            var_dump($res);
            return 1;
//            if(1) {
//            return $this->insertValue($recursion,$values);
        }else return 0;

    }
    private function insertValue($recursion = false,$values) {
        //暂时不用
        $sql = 'insert into %s values';
        $sql = sprintf($sql,$recursion?$recursion:self::$table);
        $sql .='(%s)';
        $len = count($values);
        $num = 0;
        foreach ($values as $key =>$value){
            $str = '';
            if(is_array($value)){
              $str = implode(',',$value);
            }else $str = $value;

            if($str){
                if($len != $num){
                    $sql = sprintf($sql,$str);
                    $sql .= ',(%s)';
                }
            }
            $num++;
        }

//        echo ' start********* ';echo $sql;echo ' *********end ';

//        return 1;
        return file_put_contents(__DIR__.'/../data/cache/insert_'.microtime(1).'.sql',$sql);
       // return self::query($sql);
    }




    private function types($value){
        $type = 'unkonw';
        if(is_string($value) && strlen($value)<=20){
            $type = 'varchar(20)';
        }else if(is_string($value) && strlen($value)<=50){
            $type = 'varchar(50)';
        }else if(is_string($value) && strlen($value)<=255){
            $type = 'varchar(255)';
        }else if(is_string($value)){
            $type = 'varchar(500)';
        }else if(is_array($value)){
            $type = 'varchar(500)';
        }else if(is_numeric($value)){
            $type = 'int(10)';
        }else if(is_bool($value)){
            $type = 'varchar(10)';
        }else if(is_double($value)){
            $type = 'varchar(10)';
        }else if(is_object($value)){
            $type = 'varchar(500)';
        }
        return $type;
    }
}