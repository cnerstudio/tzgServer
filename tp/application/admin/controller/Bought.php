<?php

// 已买/库存/运费
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Bought extends Base {
	// 列表
	public function listbought(){
		if($_SESSION['s_name']=="信儿众创"||$_SESSION['s_name']="myanderson"){
			$sq="select * from (select * from tzg_ListBought order by ctime desc) as a limit 100";
		}else{
			$sq="select * from (select * from tzg_ListBought where Username = '".$_SESSION['s_name']."' order by ctime desc) as a limit 100";
		}

		$data=Db::query($sq);

		$this->assign("data", $data);
		$this->assign("i", 1);
		$this->assign("c", count($data));

		return $this->fetch();
	}

	// 库存列表 专属 inventory
	public function inventory(){
		if($_SESSION['s_name']=="信儿众创"||$_SESSION['s_name']="myanderson"){
			$sq="select * from (select * from tzg_Inventory order by inter asc) as a limit 100";
		}else{
			$sq="select * from (select * from tzg_Inventory where Username = '".$_SESSION['s_name']."' order by ID desc) as a limit 100";
		}

		$data=Db::query($sq);

		$this->assign("data", $data);
		$this->assign("i", 1);
		$this->assign("c", count($data));

		return $this->fetch();
	}

	// 运费模板列表
	public function carriagetemplate(){
		$p=15;
		$w=array();
		if(isset($_GET['user'])&&$_GET['user']!=='所有用户'){ // 获得筛选得用户
			$user=$_GET['user'];
			$userTrue=$this->getUser($user);
			$w['Username']=array(
					'exp',
					"='$userTrue' OR Username like '$userTrue:%'"
			);
		}else{
			$user='所有用户';
		}
		$data=db('carriagetemplate')->field(true)->where($w)->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		{ // 下载使用
			$dataDown=array(
					'table' => 'carriagetemplate',
					'field' => 'J_ID,J_Title,J_LastModified,J_From,J_Check,J_CalcRule,Username,col_express,col_area,col_starting,col_postage,col_plus,col_postageplus',
					'head' => array(
							'J_ID',
							'运费模板名称',
							'更新时间',
							'模板来源',
							'校验',
							'计价方式',
							'用户名',
							'运送方式',
							'运送到',
							'首件(个)',
							'运费(元)',
							'续件(个)',
							'运费(元)'
					),
					'where' => $w
			);
			$this->setDownSession($dataDown);
		}
		$userData=db('carriagetemplate')->field('Username')->group('Username')->select(); // 查询所有用户
		$userData=$this->arrayColumn($userData, 'Username');
		$userData['-1']='所有用户';
		ksort($userData);
		$this->assign('userData', $userData);
		$this->assign("data", $data);
		$this->assign('user', $user);
		return $this->fetch();
	}
	/* 获取包含指定名字的模板名字 */
	public function getTempName(){
		if(!isset($_POST['name'])){
			echo json_encode(array(
					'result' => 0
			));
		}
		$name=$_POST['name'];
		$name=$this->getUser($name);
		$w=array(
				'Username' => $name
		);
		$wOR=array(
				'Username' => array(
						'like',
						"$name:%"
				)
		);
		$data=db('carriagetemplate')->field('distinct J_Title,J_ID')->where($w)->whereOr($wOR)->select();
		if($data){
			echo json_encode(array(
					'result' => 1,
					'data' => $data
			), JSON_UNESCAPED_UNICODE);
		}else{ 
			echo json_encode(['result' => 2]);
		}
		exit();
	}
}