<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\cache\driver\Redis;
//include_once('Publisher.php');
//include_once('Consumer.php');
//
//使用缓存
//
class Seckillre extends Common
{

	private $expire = 43200;	//redis缓存过期时间  12h
	//private $redis = null;
	private $cachekey = null;	//缓存变量名
	private $basket = [];		//私有数组，存放商品信息

	private $store = 50;

	/**
	 * 购物车初始化，传入用户id
	/**
	 * 秒杀入口
	 */
	public function index()
	{
	    $date1=date("Y-m-d H:i:s");
		$id = input('gid');	//商品编号
		$key='goods_stock'.$id;
		$st='goods_time'.$id;
		if (empty($id)) {
			// 记录失败日志
			return $this->writeLog(0,'商品编号不存在');
		}
        $redis = Common::Redis();//接入redis
        $count = $redis->get($key);//减少库存，返回剩余库存
        $date2 = $redis->get($st);//减少库存，返回剩余库存
        if(strtotime($date1)<strtotime($date2))
        {
            $this->error("时间未到");
        }
        
		// 先判断库存是否为0,为0秒杀失败,不为0,则进行先移除一个元素,再进行数据库操作
		if ($count == 0) {	//库存为0

			$this->writeLog(0,'库存为0');
			echo "库存为0";
			exit;

		}else{
			// 有库存
			//先移除一个列表元素
			$redis->decr($key);
			echo "订单生成中……";
			return redirect('build_order?gid='.$id);
            /*$publisher = new Publisher();
            $publisher->sendMessage($id);
            $publisher->closeConnetct();
            */
            
		}	
	}
    public function build_order()
    {
        $id=input('gid');

        $ordersn = $this->build_order_no();	//生成订单
		$uid = session('user_x_id');	
		$status = 1;
		// 再进行数据库操作
        $data = Db::table('web2020_goods')->where('id',$id)->find();	//查找商品
        
		$res = Db::table('web2020_goods')->where('ID',$id)->update(['stock' => $data['stock']-1]);
        
		if (!$res) {
			return $this->writeLog(0,'该商品不存在');
		}

		$insert_data = [
			'order_no' => $ordersn,
			'uid' => $uid,
			'gid' => $id,
			'pay_fee'	=> $data['price'],
			'status' => $status,
			'create_time' => date('Y-m-d H:i:s')
		];

		// 订单入库
			$result = Db::table('web2020_order')->insert($insert_data);
			if ($result) {
				echo "秒杀成功";
				$this->writeLog(1,'秒杀成功');
				return redirect('order?id='.$ordersn.'&gid='.$id);
			}else{
				echo "秒杀失败";
				$this->writeLog(0,'秒杀失败');
			}
    }
	/**
	 * 生成订单号
	 */
	public function order()
	{
	   /* $consumer = new Consumer();
        //$consumer->dealMq(false);
        $consumer->dealMq(true);
        */
	     $id=input('id');
	     $gid=input('gid');
	     $info = Db::table('web2020_order')->where('order_no',$id)->where('gid',$gid)->find();
	     $good = Db::table('web2020_goods')->where('id',$gid)->value('name');
	     $info['name']=$good;
	     if($info['status']==1)
	        $info['status']='待支付';
        else if($info['status']==2)
	        $info['stadtus']='已完成';	        
	     $this->assign('vo',$info);
         return $this->fetch();
	     
	}
	public function build_order_no()
	{
		return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}

	/**
	 * 生成日志  1成功 0失败
	 */
	public function writeLog($status = 1,$msg)
	{
		$data['count'] = 1;
		$data['status'] = $status;
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['msg'] = $msg;
		return Db::table('web2020_user_log')->insertGetId($data);
	}

}