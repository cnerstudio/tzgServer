<?php

namespace app\admin\controller;

use think\Controller;
use think\Loader;

// 基础控制器
class Base extends Controller {
	protected $beforeActionList=array(
			"init",
			'sessions'
	);

	/**
	 * 前置方法，取消运行时间限制
	 */
	protected function init(){
		set_time_limit(0);
	}

	/**
	 * 验证访问权限
	 */
	protected function sessions(){
		if(!session_id()){
			session_start();
		}
		if(!isset($_SESSION['s_id'])){
			// echo "无权访问";
			header("location:../../admin/user/login");
			exit();
		}
		session_write_close();
	}

	/**
	 * 二维数组转一维数组 
	 * @param array $data   需要转换的一维数组
	 * @param string $value   一维数组的值，二维数组的键
	 * @param string $key    一维数组的键，二维数组的键
	 * @param string $type   key是字符串还是变量 0 字符串，1 值
	 * @return array
	 */
	protected function arrayColumn($data, $value, $key=null, $type=0){
		$redata=array();
		if(is_array($data)&&!empty($data)){
			if($key==null){
				array_walk($data, function ($v, $k) use (&$redata, $value){
					$redata[]=$v[$value];
				});
			}else{
				array_walk($data, function ($v, $k) use (&$redata, $key, $value, $type){
					if($type==0){
						$redata[$key]=$v[$value];
					}elseif($type==1){
						$redata[$v[$key]]=$v[$value];
					}
				});
			}
		}
		return $redata;
	}

	/**
	 * 处理返回主账号用户
	 * @param string $user用户名字            
	 * @return string
	 */
	protected function getUser($user){
		$s=strpos($user, ':');
		if($s){ // 用户带：
			$user=substr($user, 0, $s);
		}
		return $user;
	}

	/**
	 * 下载csv文件
	 */
	public function csvDown(){
		header("Content-Type:application/csv");
		$name=date("Y-m-d-h-His").'.csv';
		header("Content-Disposition:attachment;filename=$name");
		$output=fopen('php://output', 'w') or die("Can't down");
		fwrite($output, chr(0xEF).chr(0xBB).chr(0xBF));
		flush();
		ob_flush();
		if(!isset($_SESSION['down'])){
			exit();
		}
		$data=$_SESSION['down'];
		session_write_close();
		if(isset($data['head'])){ // 首行
			fputcsv($output, $data['head']);
		}
		flush();
		ob_flush();
		$i=0;
		$p=1000;
		$result=$this->sqData($data, $i*$p, $p);
		do{
			foreach($result as $vs){
				foreach($vs as $key => $vv){
					if(is_numeric($vs[$key])&&strlen($vs[$key])>11){ // 纯数转
						$vs[$key]="'".$vv;
					}
					if(strpos($vv, '"')||strpos($vv, ',')){
						$vv=str_replace('"', '""', $vv);
						$vv=str_replace(',', '",', $vv);
						$vs[$key]='"'.$vv.'"';
					}
				}
				fputcsv($output, $vs);
			}
			flush();
			ob_flush();
			$i++;
			$result=$this->sqData($data, $i*$p, $p);
		}while($result);
	}

	/**
	 * 获取下载的数据
	 * @param array $data  sq参数
	 * @param int $n   偏移量
	 * @param int $p   单次条数
	 * @return array
	 */
	protected function sqData($data, $n, $p){
		set_time_limit(0);
		if(!isset($data['table'])){
			return array();
		}
		$result=db($data['table'])->alias(isset($data['alias'])?$data['alias']:'')->field(isset($data['field'])?$data['field']:true)->where(isset($data['where'])?$data['where']:array())->join(isset($data['join'])?$data['join']:array())->group(isset($data['group'])?$data['group']:'')->order(isset($data['order'])?$data['order']:'')->limit($n.','.$p)->select();
		return $result;
	}

	/**
	 * 计算满足记录的sql记录数
	 * @param array $data  sq语句数组
	 * @return int
	 */
	protected function sqCount($data){
		set_time_limit(0);
		if(!isset($data['table'])){
			return 0;
		}
		$result=db($data['table'])->alias(isset($data['alias'])?$data['alias']:'')->field("count(*) as total")->where(isset($data['where'])?$data['where']:array())->join(isset($data['join'])?$data['join']:array())->group(isset($data['group'])?$data['group']:'')->order(isset($data['order'])?$data['order']:'')->find();
		return $result['total'];
	}

	/**
	 * 使用phpExcel下载数据 数据量小于1000条
	 */
	public function xlsxDown(){
		if(!isset($_SESSION['down'])){
			exit();
		}
		$data=$_SESSION['down'];
		session_write_close();
		$num=$this->sqCount($data);
		if($num>1000){
			echo '<script>alert("记录大于1千条，请用csv下载")</script>';
			exit();
		}
		Loader::import('Excel.my.PHPExcel');
		$excel=new \PHPExcel();
		$ex07=new \PHPExcel_Writer_Excel2007($excel);
		$fielname=date('Y-m-d-H-i').'.xlsx';
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition:attachment;filename='.$fielname.'');
		header("Content-Transfer-Encoding:binary");
		$sheet=$excel->getActiveSheet(0);
		$he=1;
		if(isset($data['head'])){ // 首行
			$clu='A';
			foreach($data['head'] as $v){
				$sheet->setCellValue($clu.$he, $v);
				$clu++;
			}
			$he++;
		}
		$i=0;
		$p=1000;
		$result=$this->sqData($data, $i*$p, $p);
		do{
			foreach($result as $vs){
				$clu='A';
				foreach($vs as $key => $value){
					if(is_numeric($value)&&strlen($value)>11){ // 纯数转
						$sheet->getStyle($clu.$he)->getNumberFormat()->setFormatCode('@');
					}
					$sheet->setCellValue($clu.$he, $value);
					$clu++;
				}
				$he++;
			}
			$i++;
			$result=$this->sqData($data, $i*$p, $p);
		}while($result);
		$ex07->save('php://output');
	}

	/**
	 * 设置下载的参数
	 * @param array $downData    下载的参数
	 */
	protected function setDownSession($downData){
		if(session_id()){
			session_start();
		}
		$_SESSION['down']=$downData;
		session_write_close();
	}
}