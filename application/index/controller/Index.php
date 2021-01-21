<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Index extends Common
{
    public function index()
    {
        
        $data=Db::table('web2020_goods')->select();
        $this->assign('lists',$data);
        return $this->fetch();
    }
}