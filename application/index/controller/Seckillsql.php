<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
//
//不使用缓存，直接使用数据库
//
class Seckillsql extends Controller{
    public function build_order_no()
	{
		return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}
	public function writeLog($status = 1,$msg)
	{
		$data['count'] = 1;
		$data['status'] = $status;
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['msg'] = $msg;
		return Db::table('web2020_user_log')->insertGetId($data);
	}
	public function sqlMs()
	{
	    if (!session('user_id')) {
            $this->error('请登陆', 'login/index');
        }
        $user_id = session('user_id');
		$id = input('gid');	//商品编号

		$ordersn = $this->build_order_no();	//生成订单
		
		$uid = session('user_id');	//随机生成用户id
		$status = 1;
		// 再进行数据库操作
		$data = Db::table('web2020_goods')->where('id',$id)->find();	//查找商品
        
		// 查询还剩多少库存
		$rs = $data["stock"];
		if ($rs <= 0) {
			
			$this->error("该商品无库存");
		}else{

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
			// 自动减少一个库存
			$res = Db::table('web2020_goods')->where('ID',$id)->update(['stock' => $rs-1]);

			if ($res) {
				$this->writeLog(1,'秒杀成功');
				$this->success("秒杀成功",'index/index');
			}else{
				$this->writeLog(0,'秒杀失败');
				$this->error("秒杀失败");
			}
		}
	}
}