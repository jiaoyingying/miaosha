<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\cache\driver\Redis;
use think\Cache;
class Good extends Common
{
    /**
     * 登陆
     */
    public function index()
    {
        //$data=Db::table('web2020_order')->select();
        //$this->assign('lists',$data);
        return $this->fetch();
    }

    /**
     * 商品列表
     */
    public function goodlist()
    {
        $data=Db::table('web2020_goods')->select();
        $this->assign('lists',$data);
        return $this->fetch();
    }
    /**
     * 下架
     */
    public function goodremove()
    {
       $id = $_GET['vid'];
       $ret=Db::table('web2020_goods')->where('ID',$id)->delete();
       if(!$ret)
        {
            $this->error("删除商品失败");
        }
        else
        {
            $this->success("删除商品成功",'Good/goodlist');
        }
    }

    /**
     * 上架
     */
    public function insert()
    {
        $data=input('post.');
        if(!is_numeric($data['price'])||!is_numeric($data['stock']))
            $this->error("输入非法");
        $time=date("Y-m-d H:i:s");
        $data['create_time']=$time;
        $data['start_time']=$data['startdate1'].' '.$data['startdate2'];
        $data['end_time']=$data['enddate1'].' '.$data['enddate2'];
        unset($data['startdate1']);
        unset($data['startdate2']);
        unset($data['enddate1']);
        unset($data['enddate2']);
        var_dump($data);
        
        $ret=Db::table('web2020_goods')->insert($data);
        //var_dump($ret);
        if(!$ret)
        {
            $this->error("添加商品失败");
        }
        else
        {
            $this->success("添加商品成功",'Good/goodlist');
        }
    }
    public function goodadd()
    {
        return $this->fetch('goodadd');
    }
    /**
     * 秒杀启动
     * 初始化redis缓存
     */
    public function miaosha()
    {
        $id = $_GET['vid'];
        $good=db('goods')->where('ID',$id)->find();
        $redis = $this->Redis();
        $ret=$redis->set('goods_stock'.$good['ID'], $good['stock']);
        $ret2=$redis->set('goods_time'.$good['ID'], $good['start_time']);
        if(!$ret)
        {
            $this->error("启动秒杀失败");
        }
        else
        {
            $this->success("启动秒杀商品成功",'Good/goodlist');
        }
    }
    public function Redis()
     {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379', 5);
        $redis->auth('123456');
        return $redis;
     }

}