<?php

// 日志类
namespace app\admin\controller;

use app\api\controller\Logapi;

class Log extends Echarts {
	// 查询日志
	public function log(){
		$w=array();
		if(isset($_GET['user'])&&$_GET['user']!='所有用户'){
			$name=$this->getUser($_GET['user']);
			$w['Username']=array(
					'exp',
					"='$name' or Username like '$name:%'"
			);
			$uname=$_GET['user'];
		}else{
			$uname='所有用户';
		}
		if(isset($_GET['time'])&&$_GET['time']!=0){
			$time=$_GET['time'];
			$w['time']=array(
					'like',
					"%$time%"
			);
		}else{
			$time=0;
		}
		$data=db('logAction')->field(true)->where($w)->order('time desc')->limit(100)->select();
		{ // 下载使用
			$dataDown=array(
					'table' => 'logAction',
					'field' => 'actioncode,Username,content,from,duration,num,version,time',
					'head' => array(
							'操作代码',
							'用户',
							'操作内容',
							'操作主机',
							'周期(min)',
							'条数',
							'系统版本',
							'操作时间'
					),
					'where' => $w
			);
			$this->setDownSession($dataDown);
		}

		// 查询出所有用户
		$w1=array(
				'' => array(
						'exp',
						"INSTR(Username,':') = 0"
				)
		);
		$userData=db('logAction')->field('Username')->where($w1)->group('Username')->select();
		$userData=$this->arrayColumn($userData, 'Username');
		array_unshift($userData, '所有用户');
		$this->assign('userData', $userData);
		$this->assign("c", count($data));
		$this->assign("data", $data);
		$this->assign("i", 1);
		// 创建日历
		$t=time();
		$c=mktime(0, 0, 0, date("m", $t), 1, date("y", $t)); // 当前1月时间戳
		$c2=mktime(0, 0, 0, date("m", $c-1), 1, date("y", $c-1)); // 上一月1号时间戳
		$z=cal_days_in_month(CAL_GREGORIAN, date("m", $t), date("Y", $t)); // 当月天数
		$c3=mktime(23, 59, 59, date("m", $t), $z, date("y", $t)); // 当月最后一天23:59:59
		$t1="";
		$t2="";
		$op=array();
		for($i=$c2; $i<=$c3; $i+=86400){
			$t1=date("Y-m-d H:i:s", $i);
			$t2=date("Y-m-d H:i:s", $i+86399);
			$w=array(
					'' => array(
							'exp',
							"time >'$t1' and time < '$t2'"
					)
			);
			$sql=db('logAction')->field('group_concat(distinct concat(Username,content)) as c')->where($w)->find();
			$sql=$sql["c"];
			$result=""; // 设置的字段
			if(strstr($sql, "校③")&&strstr($sql, "校④")&&strstr($sql, "校⑤")) $result=$result."校.";
			if(strstr($sql, "创③")&&strstr($sql, "创④")&&strstr($sql, "创⑤")) $result=$result."信.";
			if(strstr($sql, "_2012③")&&strstr($sql, "_2012④")&&strstr($sql, "_2012⑤")) $result=$result."2012.";
			if(strstr($sql, "pcm③")&&strstr($sql, "pcm④")&&strstr($sql, "pcm⑤")) $result=$result."飞豹.";
			if(strstr($sql, "263m③")&&strstr($sql, "263m④")&&strstr($sql, "263m⑤")) $result=$result."哥俩.";
			if(strstr($sql, "rson③")&&strstr($sql, "rson④")&&strstr($sql, "rson⑤")) $result=$result."mya.";
			if(strstr($sql, "伟商贸③")&&strstr($sql, "伟商贸④")&&strstr($sql, "伟商贸⑤")){
				$result=$result."奕伟.";
			}
			if(strstr($sql, "数码店③")&&strstr($sql, "数码店④")&&strstr($sql, "数码店⑤")){
				$result=$result."霞.";
			}
			$op[date("Y", $i)][date("m", $i)][date("d", $i)]=$result;
		}
		$w=date("w", $c2); // 上月第一天星期几
		$this->assign("u", 1);
		$this->assign("w", $w);
		$this->assign("op", $op);
		$this->assign('curmonth', date('m'));
		$this->assign('curday', date('d'));
		$this->assign("uname", $uname);
		$this->assign('time', $time);
		return $this->fetch();
	}

	/**
	 * 商品折线图
	 */
	public function goodsLog(){
		$log=new Logapi();
		$log->setGoodsLog();
		$wlog=array();
		if(isset($_GET['cur'])&&$_GET['cur']!=1){
			$cur=$_GET['cur'];
			if($cur==2){
				$days=90;
			}elseif($cur==3){
				$days=180;
			}elseif($cur==4){
				$days=365;
			}
		}else{
			$cur=1;
			$days=30;
		}
		if (!isset($_GET['table'])||(!in_array($_GET['table'], array(0,1,2)))) {//选择表
			$tb=2; // 默认为Pdd
		}else{
			$tb=$_GET['table'];
		}
		$cs=array(
				'1' => '按月',
				'2' => '按季度',
				'3' => '半年',
				'4' => '一年',
				'5' => '全部'
		);
		$table=array(
				'1' => '分销',
				'0' => '淘宝',
				'2' => '拼多多'
		);
		// 查询出需要的日期时间
		$totime=time();
		$wlog=array();
		if(isset($days)){
			$times=$totime-$days*86400;
			$time=date('Y-m-d', $times);
			$wlog['time']=array(
					'>=',
					$time
			);
		}
		$wlog['category']=$tb;
		$data=db('logGoods')->field(true)->where($wlog)->order('time asc')->select();
		if($data){
			if($cur==5){
				$times=strtotime($data[0]['time']);
				$time=date('Y-m-d', $times);
			}
		}else{
			$times=time();
			$time=date('Y-m-d', $times);
		}
		$tdw=array();
		$tdj=array();
		$tdx=array();
		$tdd=array();
		$disw=array();
		$disj=array();
		$disx=array();
		$disd=array();
		$pddw=array();
		$pddj=array();
		$pddx=array();
		$pddd=array();
		$pddb=array();
		$x=''; // x轴
		for($i=$times; $i<$totime;){
			$x.='"'.date('m-d', $i).'",';
			$i+=86400;
		}
		$x=trim($x, ',');
		foreach($data as $vs){
			if($vs['category']==0){ // 淘宝状态
				$vs['state']=='未上架'?$tdw[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已上架'?$tdj[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='未关联'?$tdx[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已删除'?$tdd[$vs['time']]=$vs['goodcnt']:'';
			}elseif($vs['category']==1){ // 分销
				$vs['state']=='未上架'?$disw[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已上架'?$disj[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='未关联'?$disx[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已删除'?$disd[$vs['time']]=$vs['goodcnt']:'';
			}else{
				$vs['state']=='未上架'?$pddw[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已上架'?$pddj[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已售罄'?$pddx[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已删除'?$pddd[$vs['time']]=$vs['goodcnt']:'';
				$vs['state']=='已驳回'?$pddb[$vs['time']]=$vs['goodcnt']:'';
			}
		}
		if($tb==0){
			$tdj=$this->fill($tdj, $times, $totime);
			$tdw=$this->fill($tdw, $times, $totime);
			$tdx=$this->fill($tdx, $times, $totime);
			$tdd=$this->fill($tdd, $times, $totime);
		}elseif($tb==1){
			$disw=$this->fill($disw, $times, $totime);
			$disj=$this->fill($disj, $times, $totime);
			$disx=$this->fill($disx, $times, $totime);
			$disd=$this->fill($disd, $times, $totime);
		}else{
			$pddw=$this->fill($pddw, $times, $totime);
			$pddj=$this->fill($pddj, $times, $totime);
			$pddx=$this->fill($pddx, $times, $totime);
			$pddd=$this->fill($pddd, $times, $totime);
			$pddb=$this->fill($pddb, $times, $totime);
		}
		$this->assign('tdj', $tdj?$tdj:'');
		$this->assign('tdw', $tdw?$tdw:'');
		$this->assign('tdx', $tdx?$tdx:'');
		$this->assign('tdd', $tdd?$tdd:'');
		$this->assign('disw', $disw?$disw:'');
		$this->assign('disj', $disj?$disj:'');
		$this->assign('disx', $disx?$disx:'');
		$this->assign('disd', $disd?$disd:'');
		$this->assign('pddw', $pddw?$pddw:'');
		$this->assign('pddj', $pddj?$pddj:'');
		$this->assign('pddx', $pddx?$pddx:'');
		$this->assign('pddd', $pddd?$pddd:'');
		$this->assign('pddb', $pddb?$pddb:'');
		$this->assign('x', $x);
		$this->assign('cur', $cur);
		$this->assign('cs', $cs);
		$this->assign('table', $table);
		$this->assign('tb', $tb);
		return $this->fetch();
	}

	/**
	 * 供应商数量变化日志表 
	 *   
	 */
	public function supplierLog(){
		$log=new Logapi();
		$log->setSupplierLog();
		$w=array();
		$time=time();
		if(isset($_GET['cur'])&&in_array($_GET['cur'], array(
				2,
				3,
				4,
				5
		))){
			$cur=$_GET['cur'];
			if($cur==2){
				$w['time']=array(
						'exp',
						" > DATE_SUB(NOW(),INTERVAL 3 MONTH)"
				);
				$times=strtotime("-3 month");
			}elseif($cur==3){
				$w['time']=array(
						'exp',
						" > DATE_SUB(NOW(),INTERVAL 6 MONTH)"
				);
				$times=strtotime("-6 month");
			}elseif($cur==4){
				$w['time']=array(
						'exp',
						" > DATE_SUB(NOW(),INTERVAL 12 MONTH)"
				);
				$times=strtotime("-12 month");
			}elseif($cur==5){
				$ctime=db('logSupply')->field('time')->order('time asc')->find();
				if($ctime){
					$times=strtotime($ctime['time']);
				}else{
					$times=$time;
				}
			}
		}else{
			$cur=1;
			$w['time']=array(
					'exp',
					" > DATE_SUB(NOW(),INTERVAL 1 MONTH)"
			);
			$times=strtotime("-1 month");
		}
		$data=db('logSupply')->field('state,supplycnt,time')->where($w)->order('time asc')->select();
		$cs=array(
				'1' => '按月',
				'2' => '按季度',
				'3' => '半年',
				'4' => '一年',
				'5' => '全部'
		);
		$x="";
		for($i=$times; $i<$time; $i+=86400){
			$x.="'".date("m-d", $i)."',";
		}
		$close=array();
		$recruit=array();
		$del=array();
		$coop=array();
		foreach($data as $vd){
			$vd['state']=='已关闭'?$close[$vd['time']]=$vd['supplycnt']:'';
			$vd['state']=='招募中'?$recruit[$vd['time']]=$vd['supplycnt']:'';
			$vd['state']=='已删除'?$del[$vd['time']]=$vd['supplycnt']:'';
			$vd['state']=='合作中'?$coop[$vd['time']]=$vd['supplycnt']:'';
		}
		$close=$this->fill($close, $times, $time);
		$recruit=$this->fill($recruit, $times, $time);
		$del=$this->fill($del, $times, $time);
		$coop=$this->fill($coop, $times, $time);

		$x=trim($x, ',');
		$this->assign('cs', $cs);
		$this->assign('cur', $cur);
		$this->assign('x', $x);
		$this->assign('close', $close);
		$this->assign('recruit', $recruit);
		$this->assign('del', $del);
		$this->assign('coop', $coop);
		return $this->fetch();
	}
}