<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Menu extends Common
{
     public function cankao()
     {
         return $this->fetch();
     }
    /**
     * 登入
     */
    public function index()
    {
        $data=Db::table('web2020_menu')->select();
        for ($x=0; $x<sizeof($data); $x++) {
            if($data[$x]['display']==1)
            {
                $data[$x]['display']='显示';
            }
            else
                $data[$x]['display']='不显示';
            if($data[$x]['biaoshi']==1)
                $data[$x]['name']='│ ├─'.$data[$x]['name'];
            else if($data[$x]['biaoshi']==2)
                $data[$x]['name']='│ │ ├─'.$data[$x]['name'];
        }
        $this->assign('lists',$data);
        //return $this->fetch();
        return $this->fetch('index');
    }
    public function menuadd(){
        $pid=input('get.id');
        $level=input('get.biaoshi');
        $this->assign('pid',$pid);
        $this->assign('biaoshi',$level+1);
        return $this->fetch();
    }
    public function insert()
    {
        $data=input('post.');
        var_dump($data);
        $time=time();
        $data['updatetime']=$time;
        $ret=Db::table('web2020_menu')->insert($data);
        //var_dump($ret);
        if(!$ret)
        {
            $this->error("添加目录失败");
        }
        else
        {
            $this->success("添加目录成功",'Menu/index');
        }
    }
    //删除菜单
    public function menuremove(){
        $id = $_GET['vid'];
        $ret=db('menu')->where('ID',$id)->delete();
        if(!$ret)
        {
            $this->error("删除菜单失败");
        }
        else
        {
            $this->success("删除菜单成功",'Menu/index');
        }
    }
    //编辑菜单
    public function menuedit()
    {
        $id=input('get.id');
        $data=db('menu')->where('id',$id)->find();
        $this->assign('data',$data);
        $data2=db('menu')->where('id',$data['parentid'])->find();
        if($data2==NULL)
        {
            $pname=null;
        }
        else
            $pname=$data2['name'];
        $this->assign('pname',$pname);
        return $this->fetch();
    }
    public function update()
    {
        $data=input('post.');
        $id = input('post.id');
        var_dump($data);
        if($data["pname"]=="")
            $data["pid"]=0;
        else{
            $data2=db('menu')->where('name',$data["pname"])->find();
            if($data2==NULL)
                $this->error("填写错误");
            $data["pid"]=$data2["id"];
        }
        //数据库中更新该数据
        //$ret=$users->allowField(true)->isUpdate(true)->save($data,['id'=>$id]);
        $ret=db('menu')
                ->where('id', $id)
                ->update(['name'  => $data['name'],
                'parentid'  => $data['pid'],
                'biaoshi'  => $data2['biaoshi']+1,
                'icon'=> $data['icon'],
                'c' => $data['c'],
                'a' => $data['a'],
                'display' => $data['display'],
                'updatetime'=>time()
                ]);
        if(!$ret)
        {
            $this->error("修改菜单项失败");
        }
        else
        {
            $this->success("修改菜单项成功",'Menu/index');
        }
    }
}