<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 14/05/2018
 * Time: 11:26 PM
 */

//简书 刷阅读量
include __DIR__."/../autoloader.php";
use spider\core\requests;
use spider\core\selector;
use spider\core\queue;
use spider\common\common;
$redisKey = 'iPool';
try {
    queue::set_connect('default', common::REDIS_CONFIG);
} catch (Exception $e) {
    echo $e;
    exit;
}
$redisLength = queue::lsize($redisKey);
$url = "https://www.jianshu.com/u/6b1fa1764b51";
$referer = "https://www.jianshu.com";
$urll = "https://www.jianshu.com/p/26c2eca28dc5";
$userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36";
$cookie = "_ga=GA1.2.1446998420.1512720610; read_mode=day; default_font=font2; locale=zh-CN; web-note-ad-fixed=1524537468387; Hm_lvt_0c0e9d9b1e7d617b3e6842e85b9fb068=1526200190,1526220098,1526282339,1526312631; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22160352cb5d7300-071265b68a5aa1-17396d57-1296000-160352cb5d8d37%22%2C%22%24device_id%22%3A%22160352cb5d7300-071265b68a5aa1-17396d57-1296000-160352cb5d8d37%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E8%87%AA%E7%84%B6%E6%90%9C%E7%B4%A2%E6%B5%81%E9%87%8F%22%2C%22%24latest_referrer%22%3A%22https%3A%2F%2Fwww.google.co.jp%2F%22%2C%22%24latest_referrer_host%22%3A%22www.google.co.jp%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC%22%2C%22%24latest_utm_source%22%3A%22desktop%22%2C%22%24latest_utm_medium%22%3A%22not-signed-in-like-button%22%2C%22%24latest_utm_campaign%22%3A%22maleskine%22%2C%22%24latest_utm_content%22%3A%22note%22%7D%2C%22first_id%22%3A%22%22%7D; _m7e_session=9cfeba4267a7645d2058d2a971670be3; signin_redirect=https%3A%2F%2Fwww.jianshu.com%2Fp%2F26c2eca28dc5; Hm_lpvt_0c0e9d9b1e7d617b3e6842e85b9fb068=1526391092";
requests::set_referer($referer);

requests::set_cookie($referer,$cookie);

requests::set_useragent($userAgent);
//fork num
$num = 4;
$runTimes = 540;
//拿列表页会被封，需要代理下
$file = "temp.html";
//临时解决方案
if(!file_exists($file)) {
    $index = 0;
    resend:
    $ips = getPoxyIp($redisKey,$index);
    echo $ips.PHP_EOL;
    requests::set_proxy($ips);
    requests::set_timeout(10);
    $html = requests::get($url);
    if (!empty(requests::$error) || !$html) {
        echo "Error " . date('Y-m-d H:i:s') . ": " . PHP_EOL;
        $err = !$html ? "ip $ips abandon" : requests::$error;
        echo $err.PHP_EOL;
        requests::$error = '';
        $index++;
        goto resend;
    }
    file_put_contents($file,$html);
    echo 'Create Success!';
}else {
    $html = file_get_contents($file);
}
$result = selector::select($html,'//div[@class="content"]/a/@href');

$readList = selector::select($html,"//div[@class='meta']/a/text()");//拿阅读量 todo
$reads = [];
for($i = 1;$i<count($readList);$i +=4){
    if(isset($readList[$i])) {
        $reads[] = $readList[$i];
    }
}
$reads = array_reverse($reads);

$result = array_map(function ($e)use($referer,&$reads){
    $word = explode('/',$e);
    $word = $word[2];
    $ref = $referer.$e;
    return ['word'=>$word,'referer'=>$ref,'reads'=>array_pop($reads)];
},(array)$result);
//玩下fork
if(count($result) < $num){
    $num = count($result);
}

for ($i = 0;$i<$num;$i++){

    $pid = pcntl_fork();
    if(0 < $pid){
        //father
        //准备回收
        pcntl_signal(SIGCHLD,function() use( $pid ) {
            echo "收到子进程退出".PHP_EOL;
            pcntl_waitpid( $pid, $status, WNOHANG );
        });
//        pcntl_alarm(1);
        pcntl_signal_dispatch();

    }elseif(0 == $pid){

        $ips = getPoxyIp($redisKey,0);
        $time = time() + $runTimes;
        echo $ips.PHP_EOL;
        $indexs = $redisLength;
        while(time() < $time){

            $flag = toSee($i,$result,$num,$ips);
            if(!$flag){
                $indexs--;
                $ips = getPoxyIp($redisKey,$indexs);
                echo 'New Ip'.$ips.PHP_EOL;
            }else{
                file_put_contents('debug.json','Ip: '.date('Y-m-d H:i:s').$ips.' Success!'.PHP_EOL,FILE_APPEND);
            }
        }
        echo $pid."*$i run finished ".date('Y-m-d H:i:s').PHP_EOL;
        exit;
    }else {
        echo 'fork error';
    }
}
function toSee(int $i,array $result,int $num,string $ip = ""){

    $referer = "https://www.jianshu.com/u/6b1fa1764b51";
    $url = [];
    do{
        $url[] = $result[$i];
        $i = $i+$num;
    }while(isset($result[$i]));
    requests::set_proxy($ip);
    requests::set_timeout(10);
    foreach ($url as $key) {
        $ulas = "https://www.jianshu.com/notes/%s/mark_viewed.json";

        $res = requests::get($key['referer']);
        if(!$res){
            echo $key['referer'].PHP_EOL;
            echo "拿不到文章详细".PHP_EOL;
            return false;
        }
        $path = "/html/body/script[1]/text()";

        $ress = selector::select($res,$path);
        $ress = json_decode($ress);
        if(!$ress){
            return false;
        }
        $data = [
            "referer" => $referer,
            'uuid' => $ress->note_show->uuid
        ];
        $ulas = sprintf($ulas,$key['word']);
        echo $ulas;
        requests::$error = '';
        requests::set_referer($key['referer']);//必须将referer调成当前页面
        requests::post($ulas,json_encode($data));
        if(!empty(requests::$error)){
            echo "Post Error ".date('Y-m-d H:i:s').":".PHP_EOL;
            echo requests::$error.PHP_EOL;
            requests::$error = '';
            return false;
        }else{
            echo 'Success Post '.date('Y-m-d H:i:s').':'.PHP_EOL;
        }
    }

    return true;
}
function getPoxyIp($redisKey,$index = 0){

//    $iPool = new iPool();
//    reTry:
//    $ip = $iPool->get66Ip(1);
//    if(!$ip){
//
//        $ip = $iPool->getjxlIp();
//        if(!$ip){
//            goto reTry;
//        }
//    }
//    return $ip[0];
    $ip = queue::lget($redisKey,$index);
    if($ip == 'nil'){
        $ip =  queue::lget($redisKey,$index-1);
    }
    return $ip;
}
