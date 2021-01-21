<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;

class Ordering extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $data=Db::table('web2020_order')->select();
        $this->assign('lists',$data);
        return $this->fetch('index');
    }
}