<?php
include_once('Parenter.php');
class Consumer extends Parenter
{
    public function __construct()
    {
        parent::__construct('exchange', 'queue', 'routeKey');
    }
    public function doProcess($msg)
    {
       echo $msg."\n";
       //在此处处理$msg内容
    }
}
$consumer = new Consumer();
//$consumer->dealMq(false);
$consumer->dealMq(true);