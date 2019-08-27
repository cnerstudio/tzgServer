<?php

// 日志类
namespace app\admin\controller;

use app\api\controller\Logapi;

class Echarts extends Base {
	public function line(){ // 显示折线图
		$wlog=array();
		if(isset($_GET['cur'])&&$_GET['cur']!=1){
			$cur=$_GET['cur'];
			if($cur==2){
				$days=90;
			}elseif($cur==3){
				$days=180;
			}elseif($cur==4){
				$days=365;
			}elseif($cur==5){}
		}else{
			$cur=1;
			$days=30;
		}
		if(isset($_GET['name'])&&$_GET['name']!='所有用户'){
			$name=$_GET['name'];
			$wlog['Username']=array(
					'exp',
					"='$name' or Username like '$name:%' "
			);
		}else{
			$name='所有用户';
		}
		$cs=array(
				'1' => '按月',
				'2' => '按季度',
				'3' => '半年',
				'4' => '一年',
				'5' => '全部'
		); // 分类
		$wu=array(
				'' => array(
						'exp',
						'instr(Username,":")=0'
				)
		);
		if($cur!=5){
			$wlog['']=array(
					'exp',
					"time>  DATE_SUB(CURDATE(), INTERVAL $days DAY)"
			);
		}
		$group="date_format(time,'%Y-%m-%d')";
		$data=db('logAction')->field('count(*) as num,date_format(time,"%Y-%m-%d") as d')->where($wlog)->group($group)->order('d asc')->select();
		$rdata=$this->arrayColumn($data, 'num', 'd', 1);
		if($cur==5){
			if($data){
				$days=floor((time()-(strtotime($data[0]['d'])))/86400);
			}
		}

		$time=strtotime(date('Y-m-d', strtotime(" -$days day"))); // 上月0点
		$nu='';
		$day='';
		for($i=0; $i<$days; $i++){
			$md=date('Y-m-d', $time);
			$day.=date("'m-d'", $time).',';
			if(isset($rdata[$md])){
				$nu.=$rdata[$md].',';
			}else{
				$nu.='0,';
			}
			$time+=86400;
		}
		$day=substr($day, 0, -1);

		$user=db('logAction')->field('Username')->where($wu)->group('Username')->select();
		$user=$this->arrayColumn($user, 'Username');
		array_unshift($user, '所有用户');
		$this->assign('name', $name);
		$this->assign('user', $user);
		$this->assign('cur', $cur);
		$this->assign('cs', $cs);
		$this->assign('day', $day);
		$this->assign('nu', $nu);
		return $this->fetch();
	}

	/**
	 * 折线图日志时间数据填充
	 * @param array $data   存在的数据
	 * @param int $times    最早的时间
	 * @param int $totime    到期的时间
	 * @return array
	 */
	protected function fill($data, $times, $totime){
		$result='';
		$value=0;
		for($i=$times; $i<$totime;){
			$x=date('Y-m-d', $i);
			if(isset($data[$x])){
				$result.=$data[$x].',';
				$value=$data[$x];
			}else{
				$result.=$value.',';
			}
			$i+=86400;
		}
		$result=trim($result, ',');
		return $result;
	}

	/**
	 * 分布图时间数据填充
	 * @param array $data   存在的数据
	 * @param int $times    开始时间
	 * @param int $totime    结束时间
	 * @param string $range  填充区间
	 * @param int $cnt   	填充数据
	 * @return array
	 */
	protected function fillchart($data, $times, $totime, $range, $cnt){
		$newdata=array();
		for($i=$times; $i<$totime; $i+=86400){
			$xtime=date('Y-m-d', $i);
			$newdata[]=array(
					$xtime,
					$cnt,
					$range
			);
		}
		return array_merge($data, $newdata);
	}
}