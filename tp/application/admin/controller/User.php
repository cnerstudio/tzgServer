<?php

namespace app\admin\controller;

use think\Db;
use think\Request;

class User extends Base {
	protected $beforeActionList=array(
			"sessions" => [
					'except' => "login,jub"
			]
	);

	// 用户信息
	public function uinfo(){
		$p=15;
		$w=array();
		$join=array();
		$data=db('uinfo')->field(true)->join($join)->where($w)->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		$ur=trim(TKP, '.');

		{ // 下载使用
			$downData=array(
					'table' => 'uinfo',
					'field' => 'ID,case when picurl <> "" then  CONCAT("'.$_SERVER['SERVER_NAME'].TKP.'",picurl) ELSE "" END,Username,Category,bought,sold,freightTemp,location',
					'head' => array(
							'ID',
							'用户头像',
							'Username',
							'用户类别',
							'已买导入',
							'已卖导入',
							'主要运费模板',
							'所在地'
					)
			);
			$this->setDownSession($downData);
		}
		{ // 统计商品数量
			$arr=$data->toArray();
			$user=array();
			foreach($arr['data'] as $v1){
				$user[]=$v1['Username'];
			}
			if($user){
				$w1=array(
						'Username' => array(
								'in',
								$user
						),
						'state' => array(
								'in',
								'未上架,已上架,未关联'
						)
				);
				$reslt=db('distribution')->field('Username,state,count(*) as c')->where($w1)->group('Username,state')->select();
				$total=array();
				foreach($reslt as $v2){
					$total[$v2['Username']][$v2['state']]=$v2['c'];
				}
			}
		}
		$userCate=array(
				'-1' => '选择用户类别',
				1 => "信儿直营",
				2 => "加盟联营",
				3 => "管理员"
		);
		$locat=array(
				'-1' => '请选择省份',
				'110000' => '北京',
				'120000' => '天津',
				'130000' => '河北省',
				'140000' => '山西省',
				'150000' => '内蒙古自治区',
				'210000' => '辽宁省',
				'220000' => '吉林省',
				'230000' => '黑龙江省',
				'310000' => '上海',
				'320000' => '江苏省',
				'330000' => '浙江省',
				'340000' => '安徽省',
				'350000' => '福建省',
				'360000' => '江西省',
				'370000' => '山东省',
				'410000' => '河南省',
				'420000' => '湖北省',
				'430000' => '湖南省',
				'440000' => '广东省',
				'450000' => '广西壮族自治区',
				'460000' => '海南省',
				'500000' => '重庆',
				'510000' => '四川省',
				'520000' => '贵州省',
				'530000' => '云南省',
				'540000' => '西藏自治区',
				'610000' => '陕西省',
				'620000' => '甘肃省',
				'630000' => '青海省',
				'640000' => '宁夏回族自治区',
				'650000' => '新疆维吾尔自治区',
				'710000' => '台湾',
				'810000' => '香港特别行政区',
				'820000' => '澳门特别行政区'
		);
		$this->assign('userCate', $userCate);
		$this->assign('locat', $locat);
		$this->assign("data", $data);
		$this->assign('total', $total);

		return $this->fetch();
	}

	/* 修改指定用户的信息 */
	public function upUinfo(){
		if(!isset($_GET['id'])||!isset($_GET['key'])){
			header('Location:uinfo'); // 直接跳转
			exit();
		}
		$userCate=array(
				'-1' => '选择用户类别',
				1 => "信儿直营",
				2 => "加盟联营",
				3 => "管理员"
		);
		if($_GET['key']!=1&&$_GET['key']!=2&&$_GET['key']!=3){
			header('Location:uinfo'); // 直接跳转
			exit();
		}
		$w=array(
				'ID' => $_GET['id']
		);
		$update=array(
				'Category' => $userCate[$_GET['key']]
		);
		db('uinfo')->where($w)->update($update);
		header('Location:uinfo'); // 直接跳转
		exit();
	}

	// 登录页面
	public function login(){
		return $this->fetch();
	}

	// 判断用户用户名
	public function jub(){
		if(!isset($_POST)){
			exit();
		}
		if($_POST['n']==""||$_POST['p']==""){
			echo 1; // 用户名或者密码未输入
			exit();
		}
		// 判断验证
		if(!captcha_check($_POST['capt'])){
			echo 2; // 验证码错误
			exit();
		}
		// 连接数据库判断用户名密码
		$name=htmlspecialchars($_POST['n']);
		$pass=htmlspecialchars($_POST['p']);
		// 查询条件
		$where=array(
				"uname" => $name,
				"state" => 1
		);
		$data=db("user")->field("id,uname,upass,state,ctime")->where($where)->find();
		if(!$data){
			echo 3; // 用户名错误
			exit();
		}
		$md=md5($data['uname'].$pass.$data['ctime']);
		if($md!==$data['upass']){
			echo 4; // 密码错误
			exit();
		}
		if(!session_id()){
			session_start();
		}
		$_SESSION['s_name']=$data['uname'];
		$_SESSION['s_id']=$data['id'];
		session_write_close();
		echo 5; // 密码正确
	}

	/* 获得指定省份的城市数组 */
	public function getCity(){
		if(!isset($_POST['id'])){
			echo json_encode(array(
					'result' => 0
			));
			exit();
		}
		$w=array(
				'parent_id' => $_POST['id']
		);
		$date=db('taobaoArea')->field('id,name')->where($w)->select();
		echo json_encode(array(
				'result' => 1,
				'data' => $date
		), JSON_UNESCAPED_UNICODE);
	}

	/* 修改用户地址 */
	public function upUserLoact(){
		if(!isset($_POST['c'])||!isset($_POST['u'])){
			echo json_encode(array(
					'result' => 0
			));
			exit();
		}
		$lo=db('taobaoArea')->field('id,name,parent_id')->where('id', $_POST['c'])->find();
		if($lo){
			$data=array(
					'id' => $lo['id'],
					'name' => str_replace('市', '', $lo['name']),
					'parent_id' => $lo['parent_id']
			);
		}else{
			echo json_encode(array(
					'result' => 0
			));
			exit();
		}
		db('uinfo')->where('ID', $_POST['u'])->update(['location' , $city]);
		echo json_encode(array(
				"result" => 1,
				"message" => "$city"
		), JSON_UNESCAPED_UNICODE);
		exit();
	}

	/* 修改指定用户的模板id */
	public function upTemp(){
		if(!isset($_POST['data'])||!isset($_POST['name'])){
			echo json_encode(array(
					'result' => 0
			));
			exit();
		}
		$data=explode(':', $_POST['data']);
		$updat=array(
				'tempID' => $data[0],
				'freightTemp' => $data[1]
		);
		$w=array(
				'Username' => $_POST['name']
		);
		db('uinfo')->where($w)->update($updat);
		echo json_encode(array(
				'result' => 1,
				'message' => $data[1]
		));
		exit();
	}

	/* 下载用户头像 */
	public function downPic(){
		if(!isset($_POST['name'])){
			echo json_encode(array(
					'r' => 0,
					'e' => '没有选择用户'
			));
		}
		$user=$_POST['name'];
		$url="https://wwc.alicdn.com/avatar/getAvatar.do?userNick=$user&_input_charset=UTF-8&width=150&height=150&type=sns";
		$picname="./images/user/".substr(md5(time()), 0, 12).time().'.png';
		ob_clean();
		ob_start();
		readfile($url);
		$img=ob_get_contents();
		ob_end_clean();
		$fp=fopen($picname, 'w');
		if(fwrite($fp, $img)){
			$update=array(
					'picurl' => $picname
			);
			$w=array(
					'Username' => $user
			);
			db('uinfo')->where($w)->update($update);
			$picname=substr($picname, 1);
			echo json_encode(array(
					'r' => 1,
					'url' => $picname
			));
		}else{
			echo json_encode(array(
					'r' => 0,
					'e' => '图片下载失败'
			));
		}
	}
}