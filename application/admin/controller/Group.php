<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;


class Group extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $data=Db::table('web2020_admin_group')->select();
        $this->assign('data',$data);
        return $this->fetch('index');
    }
    public function group_id()
    {
        $gid=input('get.id');
        $gname=input('get.gname');
        $data=db('admin_group_access')->where('group_id',$gid)->select();
        $n=sizeof($data);
        for($x=0;$x<$n;$x++)
        {
            $dt=db('admin')->where('id',$data[$x]['uid'])->select();
            $this->assign('data',$dt);
            $this->assign('gname',$gname);
        }
        return $this->fetch();
    }
}