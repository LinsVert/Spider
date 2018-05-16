<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 19/04/2018
 * Time: 9:36 AM
 */

namespace spider\common;


class db
{
    public static $db = '';
    public static $dbname = '';
    public static $table = '';
    public function __construct($config)
    {
        self::$dbname = $config['dbname'];
        self::$db = new \mysqli($config['host'],$config['username'],$config['passwd'],$config['dbname'],$config['port']) or die('error');
    }
    public function insertJob($insertObject = [],$table = 'jobs')
    {
        self::$table = $table;
        $fieldList = self::getTableElement($table);
        if(is_array($insertObject)){
            foreach ($insertObject as $insetOb){
                $this->insert($fieldList,(array)$insetOb);
            }
        }else{
            $this->insert($fieldList,(array)$insertObject);
        }


    }
    private function getTableElement($table = 'jobs')
    {
       $element =  "SHOW COLUMNS FROM ".self::$dbname.'.'.$table;

       $fieldList =  self::$db->query($element)->fetch_all(MYSQLI_ASSOC);
       $res = [];
       if ($fieldList) {
           foreach ($fieldList as $field => $fi) {
               $res[] = $fi['Field'];
           }
       }
       return $res;
    }
    public function insert($needle,$data){
        $value = [];
        foreach ($needle as $key => $k){
            if(isset($data[$k])){
                $value[] = $this->escape(is_array($data[$k])?json_encode($data[$k],JSON_UNESCAPED_UNICODE):$data[$k]);
            }else $value[] = 0;
        }
        unset($needle[0],$value[0]);
        $insertSql = "INSERT INTO ".self::$table." (".implode(', ', $needle).") VALUES (".implode(', ', $value).")";
        //var_dump($insertSql);
        self::$db->query($insertSql);
        //var_dump(self::$db->error);
        return self::$db->insert_id;
    }
    public function escape($str)
    {
        if (is_string($str))
        {
            $str = "'".$this->escape_str($str)."'";
        }
        elseif (is_bool($str))
        {
            $str = ($str === FALSE) ? 0 : 1;
        }
        elseif (is_null($str))
        {
            $str = 'NULL';
        }

        return $str;
    }
    public function escape_str($str, $like = FALSE)
    {
        if (is_array($str))
        {
            foreach ($str as $key => $val)
            {
                $str[$key] = $this->escape_str($val, $like);
            }

            return $str;
        }


        $str = mysqli_real_escape_string(self::$db, $str);

        // escape LIKE condition wildcards
        if ($like === TRUE)
        {
            $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
        }

        return $str;
    }


}