<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 23/05/2018
 * Time: 10:27 AM
 */

include __DIR__."/../autoloader.php";

use spider\common\email;

$email = new email();
$email->send();