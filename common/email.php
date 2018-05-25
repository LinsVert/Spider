<?php
/**
 * Created by PhpStorm.
 * User: lins
 * Date: 25/05/2018
 * Time: 10:27 AM
 */

namespace spider\common;


class email
{
    public function send(){
    $to = "992943312@qq.com";
    $subject = "Test mail";
    $message = "Hello! This is a simple email message.";
    $from = "sb@163.com";
    $headers = "From: $from";
   var_dump(mail($to,$subject,$message,$headers));
    echo "Mail Sent.";
    }

}