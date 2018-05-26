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

class iPool
{
    const ips = [
        'www.66ip.cn'=> "http://www.66ip.cn/nmtq.php?getnum=%s&isp=0&anonymoustype=0&start=&ports=&export=&ipaddress=&area=1&proxytype=2&api=66ip",
        "ip.jiangxianli.com"=>"http://ip.jiangxianli.com/api/proxy_ip"
    ];

    public function get66Ip($num = 10){
        $url = sprintf(self::ips['www.66ip.cn'],$num);
        $num = 0;
        do{
            $num++;
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

            requests::set_timeout(20);
            $poxy = requests::get($url);
            if (!$poxy) {
                $poxy = '{"code":1}';
            }
            $poxy = json_decode($poxy);
            sleep(1);
        }while($poxy->code != 0 && $num<=5);
        if ($poxy->code !=0){
            return false;
        }else {
            $ips = $poxy->data->ip . ":" . $poxy->data->port;
            return [$ips];
        }
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