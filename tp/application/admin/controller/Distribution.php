<?php

// 分销列表
namespace app\admin\controller;

use think\Db;
use think\Request;

class Distribution extends Base {
	// 分销商品 distribution
	public function distribution(){
		$p=15;
		$searchgoods=isset($_GET['searchgoods'])?$_GET['searchgoods']:''; // 搜索的商品
		$uname=isset($_GET['uname'])?$_GET['uname']:'所有用户'; // 用户名
		$status=isset($_GET['status'])?$_GET['status']:'所有状态'; // 状态
		$where=array();
		if($searchgoods!=''){
			$where[]=array(
					'exp',
					"goodsname like '%$searchgoods%'"
			);
		}

		if(isset($_GET['supname'])&&($_GET['supname']!='选择供应商'&&$_GET['supname']!='')){
			if($uname!='所有用户'){
				$where['suppliername']=$_GET['supname'];
				$supname=$_GET['supname'];
			}
		}else{
			$supname='';
		}
		if($status!='所有状态'){
			$where['state']=$status;
		}else{
			$where['state']=array(
					'neq',
					'已删除'
			);
		}
		$gy=array();
		if($uname!='所有用户'){
			$where['username']=$uname;
			$w0=array(
					'Username' => $uname,
					'state' => $where['state']
			);
			$gy=db('distribution')->field('DISTINCT suppliername')->where($w0)->order("supplierID desc")->select();
		}
		$join=array(
				array(
						'tzg_mysupplier b',
						'b.id=a.supplierID',
						'left'
				)
		);
		$data=db('distribution')->alias('a')->field("username,name,a.ID,supplierID,pprice,goodsID,price,goodsname,profit,stock,a.rtime,state")->where($where)->join($join)->order('rtime desc')->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		{ // 下载
			$dataDown=array(
					'table' => 'distribution',
					'alias' => 'a', // cateid
					'field' => "username,name,goodsname,pprice,price,profit,stock,a.rtime,state",
					'head' => array(
							'用户名',
							'供应商名',
							'商品名字',
							'采购价',
							'售价',
							'利润',
							'库存',
							'更新时间',
							'状态'
					),
					'where' => $where,
					'join' => $join,
					'order' => "rtime desc"
			);
			$this->setDownSession($dataDown);
		}
		if(isset($where['username'])){
			unset($where['username']);
		}
		$name=db('distribution')->field("username")->order('supplierID desc')->where($where)->group('username')->select();
		if($name){
			$uarr[]=array(
					'username' => '所有用户'
			);
			$user=array_merge($uarr, $name);
		}else{
			$user[]=array(
					'username' => $uname
			);
		}
		$state=array(
				'所有状态',
				'未上架',
				'已上架',
				'未关联'
		);
		if($gy){
			$op="<option value='?uname=$uname&supname=选择供应商&status=$status&searchgoods=$searchgoods'>选择供应商</option>";
			foreach($gy as $vc){
				$s='';
				$nc=$vc['suppliername'];
				if(isset($_GET['supname'])){
					if($nc==$_GET['supname']){
						$s='selected';
					}
				}
				$op.="<option value='?uname=$uname&supname=$nc&status=$status&searchgoods=$searchgoods' $s>$nc</option>";
			}
			$select="<select onchange='window.location.href=this.value'>$op</select>";
		}else{
			$select="<select onchange='window.location.href=this.value'><option>选择供应商</option></select>";
		}

		$this->assign('select', $select);
		$this->assign('supname', $supname);
		$this->assign('data', $data); // 主数据
		$this->assign('unamearr', $user); // 筛选用户数组
		$this->assign('state', $state); // 状态筛选
		$this->assign('searchgoods', $searchgoods); // 搜索的商品
		$this->assign('uname', $uname); // 当前用户
		$this->assign('status', $status); // 当前状态
		return $this->fetch();
	}

	// 分销商户 dissupplier
	public function disupplier(){
		if($_SESSION['s_name']=="信儿众创"||$_SESSION['s_name']=="myanderson"){
			$sq1="select * from (select * from tzg_Disupplier order by aprofit desc) as a limit 100";
			$sq2="SELECT sum(csales) as a FROM tzg_Disupplier";
			$sq3="SELECT sum(salesv) as a FROM tzg_Disupplier";
			$data1=Db::query($sq1);
			$data2=Db::query($sq2);
			$data3=Db::query($sq3);
		}else{
			$name=preg_replace('/[\d]/', '', $_SESSION['s_name']);
			$name=mb_substr($name, 1, strlen($name)-1, "utf-8");
			$sql="SELECT location FROM tzg_Uinfo where Username = '%s'"; // 替换使用的sq
			$encode=mb_detect_encoding($sql, array(
					"ASCII",
					'UTF-8',
					"GB2312",
					"GBK",
					'BIG5'
			)); // 检测编码
			$sql=mb_convert_encoding($sql, 'UTF-8', $encode); // 转换编码
			$sql=sprintf($sql, $name); // 函数把格式化的字符串写入变量中 %s 字符串型
			$cd=Db::query($sql);
			$t=$cd[0]['location'];
			$sq1="select * from (select * from tzg_Disupplier where suppliID LIKE '".$t."%".$name."' order by aprofit desc) as a limit 100";
			$sq2="SELECT sum(csales) as a FROM tzg_Disupplier  where suppliID LIKE '".$t."%".$name."' ";
			$sq3="SELECT sum(salesv) as a FROM tzg_Disupplier where suppliID LIKE '".$t."%".$name."' ";
			$data1=Db::query($sq1);
			$data2=Db::query($sq2);
			$data3=Db::query($sq3);
		}
		$sql="SELECT * FROM tzg_Disupplier"; // 总记录
		$c=Db::query($sq1);
		foreach($data1 as $k => $v){
			$data1[$k]['suppliname']=str_replace("专营店", "", $v['suppliname']);
			$data1[$k]['suppliname']=str_replace("供应商", "", $v['suppliname']);
			$data1[$k]['suppliname']=str_replace("旗舰店", "", $v['suppliname']);
			$data1[$k]['suppliname']=str_replace("概念店", "", $v['suppliname']);
			$data1[$k]['suppliname']=str_replace("品牌商", "", $v['suppliname']);
			$data1[$k]['suppliname']=str_replace("专卖店", "", $v['suppliname']);
		}

		$this->assign("k", array(
				'默认',
				'无响应',
				'交易违约',
				'风险供应'
		)); // 上架方式
		$this->assign("data", $data1); //
		$this->assign("x", isset($data3[0]['a'])?$data3[0]['a']:0); // 累计销售量
		$this->assign("e", isset($data2[0]['a'])?$data2[0]['a']:0); // 累计销售额
		$this->assign("n", count($data1)); // 当前显示记录数
		$this->assign("z", count($c)); // 总记录数
		$this->assign("i", 1); // 序号
		return $this->fetch();
	}

	// 分销采购dispurchase
	public function dispurchase(){
		if($_SESSION['s_name']=="信儿众创"||$_SESSION['s_name']="myanderson"){
			$sq="select * from (select * from tzg_Dispurchase order by mktime desc) as a limit 100";
		}else{
			$sq="select * from (select * from tzg_Dispurchase where Username = '".$_SESSION['s_name']."' order by mktime desc) as a limit 100";
		}

		$data=Db::query($sq);

		$this->assign("c", count($data));
		$this->assign("data", $data);
		$this->assign("i", 1);

		return $this->fetch();
	}
}