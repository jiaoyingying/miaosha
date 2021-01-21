<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;

class Log extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $data=Db::table('web2020_admin_log')->paginate(15);
        $page=$data->render();
        $this->assign('page', $page);
        $this->assign('lists',$data);
        return $this->fetch('index');
    }
}