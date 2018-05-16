<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 16/05/2018
 * Time: 1:02 PM
 * For: 抓详情页的uuid
 */

include __DIR__."/../autoloader.php";
use spider\core\requests;
use spider\core\selector;


$html = file_get_contents('1526399922.html');

$path = "/html/body/script[1]/text()";

$res = selector::select($html,$path);

//var_dump($res);

$res = json_decode($res);



print_r($res->note_show->uuid);


