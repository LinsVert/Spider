<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 18/04/2018
 * Time: 10:37 AM
 */
namespace spider\common;

class Config
{
    const domain = "www.lagou.com";
    const reqUrl = "https://www.lagou.com/jobs/positionAjax.json?";
    const useragent =  [
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0",
        "Mozilla/5.0 (iPad; CPU OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3",
        "Mozilla/5.0 (Linux; U; Android 2.2; en-gb; GT-P1000 Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1"
    ];
    const referer = "https://www.lagou.com/jobs/list_web%E5%89%8D%E7%AB%AF?px=default&city=%E6%9D%AD%E5%B7%9E";
    const kd = [
        'web前端','Java','C++','PHP','区块链','数据挖掘','搜索算法','Python','C','Android',
        'iOS','深度学习','C#','.NET','Hadoop','Delphi','VB','Perl','Ruby',
        'Node.js','Go','ASP','Shell','后端开发其它','JavaScript'
    ];
    const cookie = "user_trace_token=20180114155859-10b9e9a9-2cec-4eb8-8c0b-fca78fbc5ace; _ga=GA1.2.1123511548.1515916740; LGUID=20180114155900-c78ee559-f900-11e7-95a0-525400f775ce; JSESSIONID=ABAAABAABEEAAJA6C448512BF647F1453CA15B1E80D99D5; _gid=GA1.2.906077634.1523935189; TG-TRACK-CODE=index_navigation; LGSID=20180418105252-964a1059-42b3-11e8-89d5-525400f775ce; PRE_UTM=; PRE_HOST=www.google.co.jp; PRE_SITE=https%3A%2F%2Fwww.google.co.jp%2F; PRE_LAND=https%3A%2F%2Fwww.lagou.com%2F; Hm_lvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1524020087,1524020121,1524020272,1524020337; _gat=1; X_HTTP_TOKEN=a48dec493d76b346085adaf1c2f89e46; LG_LOGIN_USER_ID=196fb18238d1de7e8d4e78bec721e0573f188c4d6e69d7c8; _putrc=2D4DD9DB7BEC288A; login=true; unick=%E6%9E%97%E5%BE%B7%E6%A0%87; showExpriedIndex=1; showExpriedCompanyHome=1; showExpriedMyPublish=1; hasDeliver=37; gate_login_token=25946ee09360df5c4032daf1e4bf15001d53f06b29d663dd; index_location_city=%E6%9D%AD%E5%B7%9E; Hm_lpvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1524021137; LGRID=20180418111216-4c788cca-42b6-11e8-b8a5-5254005c3644; SEARCH_ID=27c17e9f4fa24635a407595ea6f9e52b";
    const city = [
        '杭州',
    ];
    const px = [
        'new','default',
    ];
    const needAddtionalResult = false;

    const db = [
        'host'=>'127.0.0.1',
        'port'=>3306,
        'username'=>'root',
        'passwd'=>'',
        'dbname'=>'lins'
    ];
}