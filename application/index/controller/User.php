<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class User extends Common
{
    /**
     * 登入
     */
    public function index()
    {
        $info = db('user')->select();
        //var_dump($info);
        $this->assign('lists',$info);
        return $this->fetch('index');
    }
    public function useredit()
    {
        $id=input('get.id');
        $data=db('user')->where('ID',$id)->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function update()
    {
        $data=input('post.');
        $id = input('post.id');
        //数据库中更新该数据
        //$ret=$users->allowField(true)->isUpdate(true)->save($data,['id'=>$id]);
        $ret=db('user')
                ->where('id', $id)
                ->update(['password'  => md5($data['password']),
                'username'  => $data['username'],
                'email'  => $data['email'],
                'status'=> $data['status'],
                'update_time'=>date("Y-m-d H:i:s")
                ]);
        if(!$ret)
        {
            $this->error("修改用户信息失败");
        }
        else
        {
            $this->success("修改用户信息成功",'Admin/index');
        }
    }
     
    public function public_edit_info()
    {
        $this->assign('name',session('user_name'));
        return $this->fetch('public_edit_info');
    }
    public function infoedit()
    {
        $data=input('post.');
        $id=session('user_id');
        //检查输入是否正确
        if($data['password']!=$data['repassword'])
        {
            $this->error("密码错误");
        }
        if(strlen($data['password'])<6||!preg_match('/[a-zA-Z]/',$data['password'])||!preg_match('/[0-9]/',$data['password']))
        {
            $this->error("密码过于简单");
        }
        $ret=db('admin')->where('id',$id)->update(['username'  => $data['username'],
                'password' => $data['password'],
                'email' => $data['email'],
                'mobile' => $data['phone'],
                'realname' => $data['realname']
                ]);
       if(!$ret)
        {
            $this->error("更新失败");
        }
        else
        {
            $this->success("更新成功");
        }
    }
    public function public_info()
    {
        $id=session('user_x_id');
        $info = db('user')->where('id',$id)->find();
        $this->assign('vo',$info);
        return $this->fetch('public_info');
    }
}