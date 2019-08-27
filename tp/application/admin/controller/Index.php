<?php

namespace app\admin\controller;

use think\Controller;

class index extends Base {
	// 主页
	public function show(){
		$name=$_SESSION['s_name'];
		session_write_close();
		$this->assign("uname", $name);
		return $this->fetch();
	}
	// 欢迎页
	public function welcome(){
		return $this->fetch();
	}
	// 注销登录
	public function td(){
		if(!session_id()){
			session_start();
		}
		unset($_SESSION['s_id']);
		unset($_SESSION['s_name']);
		session_write_close();
		echo 1;
	}
}