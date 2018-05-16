<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Target: Spider For Lagou
 * Date: 18/04/2018
 * Time: 10:32 AM
 */

include __DIR__."/../autoloader.php";

use spider\core\requests;
use spider\common\Config;
use spider\common\db;

requests::set_referer(Config::referer);//referer
requests::set_cookies(Config::cookie,Config::domain);
requests::set_useragent(Config::useragent);
$index = 0;
$pageSize = 15;
$db = new db(Config::db);
$errorLog = __DIR__."/../demo/error.php";
//kd层
while(isset(Config::kd[$index])){
    $page = 1;
    $first = $page == 1 ? true : false;
    $total = 0;

    do{
        $time1 = microtime(true);
        $filed = [
            'kd'=>Config::kd[$index],
            'pn'=>$page,
            'first'=>$first,
        ];
        if($page >1){
            $filed['first'] = false;
        }
        $urlEncode = [
            'city'=>Config::city[0],
            'px'=>Config::px[0],
            'needAddtionalResult'=>false,
        ];
        $reqUrl = http_build_query($urlEncode);
        $reqUrl = Config::reqUrl.$reqUrl;
        $res = requests::post($reqUrl, $filed);
        //$res = file_get_contents(__DIR__."/../demo/reqJson.php");
        $res = json_decode($res);
        if($res->success === false){
            $error['page'] = $page;
            $error['kd'] = Config::kd[$index];
            file_put_contents($errorLog,json_encode($error,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
            continue;
        }
        $positionResult = $res->content->positionResult;
        if($page == 1){
            $total = $positionResult->totalCount;
        }
        $result = $positionResult->result;
        unset($positionResult,$res);
        $db->insertJob($result);


        $time2 = microtime(true);
        $used = $time2-$time1;
        echo "***********".PHP_EOL;
        echo " KD = ".Config::kd[$index]." ****** PAGE = ".$page.PHP_EOL;
        echo "useTime = ".$used.PHP_EOL;

        $page++;
        $rand = rand(1,3);//sleep seed
        sleep($rand);

    }while($page * $pageSize <= $total);
    $index++;
}