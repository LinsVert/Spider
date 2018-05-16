<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 19/04/2018
 * Time: 10:57 AM
 */

include __DIR__."/../autoloader.php";
use spider\common\db;
use spider\common\Config;

$db = new db(Config::db);
$res = file_get_contents(__DIR__."/../demo/reqJson.php");
$res = json_decode($res);
$aa = $res->content->positionResult->result;
//$db->insertJob($aa);
//var_dump($aa);
$i = 0;
while($i<3){
$time1 = microtime(true);
$rand = rand(1,4);
sleep($rand);
$i++;
$time2 = microtime(true);
echo $time2-$time1;
}
