<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $date_now=date("Y-m-d H:i:s");
        $list=array('name'=>session('user_name'),'time'=>$date_now,'IP'=>$_SERVER['REMOTE_ADDR']);
        $this->assign('list',$list);
        return $this->fetch('index');
    }
}