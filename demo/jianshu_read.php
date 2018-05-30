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
$num = 1;
$runTimes = 300;
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
                echo 'New Ip : '.$ips.PHP_EOL;
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
    echo 'Ip in toSee function.'.$ip.PHP_EOL;
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
       $path = "//script[@type='application/json']/text()";

        $ress = selector::select($res,$path);
        $ress = json_decode($ress);

        if(!$ress){
            echo 'do not select detail!!!!'.PHP_EOL;
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
    re:
    $ip = queue::lpop($redisKey);
    if($ip == 'nil'){
        exit('none');
    }
    $ip = check_ip([$ip]);
    if(!$ip){
        echo 'check error'.PHP_EOL;
        goto re;
    }
    queue::rpush($redisKey,$ip[0]);
    return $ip[0];
}
function check_ip($ips){
    $requestUrl = "https://www.jianshu.com";
    $userAgent = [
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; AcooBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Acoo Browser; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)",
        "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.5; AOLBuild 4337.35; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
        "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN) AppleWebKit/523.15 (KHTML, like Gecko, Safari/419.3) Arora/0.3 (Change: 287 c9dfb30)",
        "Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.6",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2pre) Gecko/20070215 K-Ninja/2.1.1",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/20080705 Firefox/3.0 Kapiko/3.0",
        "Mozilla/5.0 (X11; Linux i686; U;) Gecko/20070322 Kazehakase/0.4.5",
        "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.8) Gecko Fedora/1.9.0.8-1.fc10 Kazehakase/0.5.6",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20",
        "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.11 TaoBrowser/2.0 Safari/536.11",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.71 Safari/537.1 LBBROWSER",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; LBBROWSER)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E; LBBROWSER)",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 LBBROWSER",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)",
        "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E; QQBrowser/7.0.3698.400)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SV1; QQDownload 732; .NET4.0C; .NET4.0E; 360SE)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)",
        "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1",
        "Mozilla/5.0 (iPad; U; CPU OS 4_2_1 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8C148 Safari/6533.18.5",
        "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b13pre) Gecko/20110307 Firefox/4.0b13pre",
        "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11",
        "Mozilla/5.0 (X11; U; Linux x86_64; zh-CN; rv:1.9.2.10) Gecko/20100922 Ubuntu/10.10 (maverick) Firefox/3.6.10"
    ];
    $successIp = [];
    requests::set_timeout(10);
    foreach ($ips as $key){
        requests::set_useragent($userAgent);
        requests::set_proxy($key);
        $html = requests::get($requestUrl);
        if (!requests::$error && $html) {
            $successIp[] = $key;
            echo 'Check Success!'.$key.PHP_EOL;
        }else {
            requests::$error = '';
        }
    }
    return $successIp;
}

