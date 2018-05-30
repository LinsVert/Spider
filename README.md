# Spider
phpSpider
使用phpSpider框架爬点东西
https://github.com/owner888/phpspider

IP池 可以查看 common下的[iPool.php](https://github.com/LinsVert/Spider/blob/master/common/iPool.php)

demo文件夹内容:

[jianshu_read.php](https://github.com/LinsVert/Spider/blob/master/demo/jianshu_read.php)
简书阅读爬虫

[ipSpider.php](https://github.com/LinsVert/Spider/blob/master/demo/ipSpider.php)
ip爬取脚本

[checIp.php](https://github.com/LinsVert/Spider/blob/master/demo/checIp.php)
ip新鲜度脚本

流程是：
先爬ip 入redis 然后刷阅读 easy!


```
crontab 命令 每10分钟跑一次 jianshu_read 每5分钟抓IP 每10分钟检测redis池中ip的新鲜度
*/10 * * * * php /home/wwwroot/spider/demo/jianshu_read.php >> /home/wwwlogs/jianshu.log

*/5 * * * * php /home/wwwroot/spider/demo/ipSpider.php >> /home/wwwlogs/ipSpider.log
*/10 * * * * php /home/wwwroot/spider/demo/checIp.php >> /home/wwwlogs/checkIp.log
```