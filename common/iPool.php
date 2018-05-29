<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 26/05/2018
 * Time: 1:06 PM
 */

namespace spider\common;

//ip池
use spider\core\requests;
use spider\core\selector;

class iPool
{
    const ips = [
        'www.66ip.cn' => "http://www.66ip.cn/nmtq.php?getnum=%s&isp=0&anonymoustype=0&start=&ports=&export=&ipaddress=&area=1&proxytype=2&api=66ip",
        "ip.jiangxianli.com" => "http://ip.jiangxianli.com/api/proxy_ip",
        "www.data5u.com" => "http://www.data5u.com/free/index.shtml",
        "www.xicidaili.com"=>"http://www.xicidaili.com/",
        "ip.kxdaili.com/" => "http://ip.kxdaili.com/",
    ];
    const userAgent = [
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

    public function get66Ip($num = 10){
        $url = sprintf(self::ips['www.66ip.cn'],$num);
        $num = 0;
        do{
            $num++;
            requests::set_timeout(10);
            $html = requests::get($url);
            $ips = $this->preIp($html);
        }while(!$ips && $num<=5);
        return $ips;
    }
    public function getjxlIp(){
        $url = self::ips['ip.jiangxianli.com'];
        $num = 0;
        do{
            $num ++;
            requests::$error = '';
            requests::set_timeout(20);
            $poxy = requests::get($url);
            if (!$poxy) {
                $poxy = '{"code":1}';
            }
            $poxy = json_decode($poxy);
            sleep(1);
        }while(!empty(requests::$error) && $num<=5);
        if ($poxy->code !=0){
            return false;
        }else {
            if(isset($poxy->data->ip) && isset($poxy->data->port))
                $ips = $poxy->data->ip . ":" . $poxy->data->port;
            else return false;
            return [$ips];
        }
    }
    public function getjxlIpH(){
        $ips = [];
        $url = "http://ip.jiangxianli.com/?page=%s";
        $referer = "http://ip.jiangxianli.com";
        requests::set_referer($referer);
        $pages = 1;
        do{
            requests::set_useragent(self::userAgent);
            $urls = sprintf($url,$pages);
            $pages ++;
            $html = requests::get($urls);
            $page = (array)selector::select($html,"//ul[@class='pagination']/li/a");
            $total = (int)$page[count($page)-2];
            $ip = selector::select($html,"//tbody/tr/td[2]");
            $port = selector::select($html,"//tbody/tr/td[3]");
            if($ip && $port){
                $res = array_map(function ($v1,$v2){
                    return $v1.":".$v2;
                },(array)$ip,(array)$port);
                $ips = array_merge($ips,$res);

            }
        }while($pages <= $total);
        return $ips;
    }
    public function getkxIp(){
        $ips = [];
        $url = "http://ip.kxdaili.com/ipList/%s.html#ip";
        $referer = "http://ip.kxdaili.com";
        requests::set_referer($referer);
        $pages = 1;
        do{
            requests::set_useragent(self::userAgent);
            $urls = sprintf($url,$pages);
            echo $urls;
            $pages ++;
            $html = requests::get($urls);
            $page = (array)selector::select($html,"//div[@class='page']/a");
            $total = (int)$page[count($page)-1];
            echo 'Total '.$total.PHP_EOL;
            $ip = selector::select($html,"//tbody/tr/td[1]");
            $port = selector::select($html,"//tbody/tr/td[2]");
            if($ip && $port){
                $res = array_map(function ($v1,$v2){
                    return $v1.":".$v2;
                },(array)$ip,(array)$port);
                $ips = array_merge($ips,$res);
            }
        }while($pages <= $total);
        return $ips;
    }
    public function getxcIp(){
        $ips = [];
        $url = ['http://www.xicidaili.com/nn/','http://www.xicidaili.com/nt/','http://www.xicidaili.com/wn/','http://www.xicidaili.com/wt/'];
        $referer = "http://ip.kxdaili.com";
        requests::set_referer($referer);
        $pages = 0;
        do{
            requests::set_useragent(self::userAgent);
            $urls = $url[$pages];
            echo $urls;
            $pages ++;
            $html = requests::get($urls);
            $ip = selector::select($html,"//tr[@class='odd']/td[2]");
            $port = selector::select($html,"//tr[@class='odd']/td[3]");
            if($ip && $port){
                $res = array_map(function ($v1,$v2){
                    return $v1.":".$v2;
                },(array)$ip,(array)$port);
                $ips = array_merge($ips,$res);

            }

        }while(isset($url[$pages]));
        return $ips;
    }
    public function getData5uIp(){
        $ips = [];
        $url = ['http://www.data5u.com/free/gngn/index.shtml','http://www.data5u.com/free/gnpt/index.shtml','http://www.data5u.com/free/gwgn/index.shtml','http://www.data5u.com/free/gwpt/index.shtml'];
        $referer = "http://www.data5u.com/free/gwgn/index.shtml";
        requests::set_referer($referer);
        $pages = 0;
        do{
            requests::set_useragent(self::userAgent);
            $urls = $url[$pages];
            echo $urls.PHP_EOL;
            $pages ++;
            $html = requests::get($urls);
            $ip = selector::select($html,"//ul[@class='l2']/span[1]/li");
            $port = selector::select($html,"//ul[@class='l2']/span[2]/li");
            if($ip && $port){
                $res = array_map(function ($v1,$v2){
                    return $v1.":".$v2;
                },(array)$ip,(array)$port);
                $ips = array_merge($ips,$res);

            }

        }while(isset($url[$pages]));
    }

    protected function preIp($html){
        $pre = "/\d+\.\d+\.\d+\.\d+\:\d+/";//ip 简易匹配

        preg_match_all($pre,$html,$ips);
        if(!$ips){
            return false;
        }else {
            return $ips[0];
        }
    }


}