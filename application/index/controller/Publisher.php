<?php
include_once('Parenter.php');
class Publisher extends Parenter
{
    public function __construct()
    {
        parent::__construct('exchange', '', 'routeKey');
    }
    public function doProcess($msg)
    {
 
    }
 
}