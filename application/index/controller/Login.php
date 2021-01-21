<?php
namespace app\index\controller;

use think\Controller;
use think\captcha\Captcha;
class Login extends Controller
{

    /**
     * 登入
     */
    public function index()
    {
        //$this->view->engine->layout(false);
        return $this->fetch();
    }


    /**
     * 处理登录请求
     */
    public function dologin()
    {
        $username = input('post.username');
        $password = input('post.password');
        //验证码验证码
        $captcha = new Captcha();
        if (!$captcha->check(input("post.captcha"))){
            return $this->error("验证码错误");
        }
        if (!$username) {
            $this->error('用户名不能为空');
        }
        if (!$password) {
            $this->error('密码不能为空');
        }
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('123456'); # 如果没有密码则不需要这行
        $num = $redis->get($username);
        if($num > 5){
            echo '登录次数超过5次,请5分钟后再尝试';
            exit();
        }
        $User = model('User');
        $info = $User->getInfoByUsername($username);
        if (!$info) {
            $this->error('用户名或密码错误');
        }
        $md5_salt = config('md5_salt');

        //if (md5(md5($password).$md5_salt) != $info['password']) {
        if ($password != $info['password']) {
            $redis->incr($username);
            $redis->expire($username,20);  //设置过期时间，单位秒
            $this->error('用户名或密码错误');
        } else {
            session('user_x_name', $info['username']);
            session('user_x_id', $info['id']);
            //记录登录信息
            $User->editInfo($info['id']);
            $this->success('登入成功', 'index/index');
        }
    }
    /**
     * 登出
     */
    public function logout()
    {
        session('user_name', null);
        session('user_id', null);
        $this->success('退出成功', 'login/index');
    }

}