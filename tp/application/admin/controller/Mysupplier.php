<?php

namespace app\admin\controller;

use think\Request;

class Mysupplier extends Base {

	// 供应商管理模块
	public function sinfo(){
		$p=15; // 分页数量
		$color="#FF1493"; // 重复类目颜色
		$where=array(
				'isdel' => 0
		);
		$join=array();
		$cid=0;
		if(isset($_GET['action'])){
			if($_GET['action']=="update"){ // 已更新列表
				$where['btype']=array(
						'<>',
						''
				);
			}elseif($_GET['action']=="price"){ // 供应商价格
				$where['pricedavg']=array(
						'<>',
						''
				);
			}elseif($_GET['action']=="cate"&&$_GET['cateid']!=0){ // 类目id查询
				$cid=$_GET['cateid'];
				$where['']=array(
						'exp',
						"find_in_set($cid,searchid)"
				);
			}
		}
		// 查询用户可申请
		$userid='0';
		if(isset($_GET['id'])&&$_GET['id']!=0){
			$where['c.userid']=$_GET['id'];
			$where['c.status']=1;
			$userid=$_GET['id'];
			$join=array(
					array(
							"tzg_myapply c",
							"supplierid=a.id",
							"left"
					),
					array(
							"tzg_myapplycate e",
							"FIND_IN_SET(e.cateid ,a.searchid)",
							"left"
					)
			);
			$where['e.isapply']=1;
			$where['e.userid']=$userid;
		}
		if(!isset($_GET['cgroup'])){ // 默认产品数量排序
			$_GET['cgroup']='desc';
		}
		$cgroup=$_GET['cgroup']; // 当前排序

		$date=db("mysupplier")->alias("a")->field("a.id,a.name,productcount,rstatus,brand,rtime,province,city,priceavg,pricedmax,priceamin,searchid")->join($join)->where($where)->group("a.id")->order("productcount $cgroup,rtime desc")->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		// {//下载
		// $dataDown=array(
		// 'table'=>'mysupplier',
		// 'field'=>"id,name,province,city,brand,cateid,rstatus,guarantee,mcate,brand,btype,priceamax,priceamin,priceaavg,productcount,case isdel when 1 then '已删除' else '未删除' end as isdel,searchid,ctime,rtime",
		// 'head'=>array('供应商id','供应商名字','省','市','品牌','类目','招募状态','保障','商品类别','商家类型','最高价','最低价','平均价','产品数量','是否删除','搜索类目','创建时间','更新时间'),
		// 'where'=>$where,
		// 'join'=>$join,
		// 'order'=>"productcount $cgroup,rtime desc"
		// );
		// $this->setDownSession($dataDown);
		// }
		$dataArray=$date->toArray();
		$catstr='';
		foreach($dataArray['data'] as $vs){
			$catstr.=$vs['searchid'].',';
		}
		$catstr=trim($catstr, ',');
		$catarr=explode(',', $catstr);
		$catarr=array_unique($catarr); // 去重

		$w1=array(
				'id' => array(
						'in',
						$catarr
				)
		);
		$cataData=db('taobaoCat')->field('id,catename')->where($w1)->select();
		$newCat=array();
		if($cataData){
			$newCat=$this->arrayColumn($cataData, 'catename', 'id', 1);
		}
		{ // 查询出所有重复的类目（>1）
			$wherecolor=array( // 查询类目种类
					'brand' => array(
							'<>',
							''
					)
			);
			$datacolor=db("mysupplier")->field("id,count(id) as con,brand")->where($wherecolor)->group("brand")->having("con>1")->select();
			$datacolor=$this->arrayColumn($datacolor, 'con', 'brand', 1);
		}
		{ // 查询统计结果
			$wherey=array(
					"isdel" => 0
			);
			$arr=db("mysupplier")->field("count(*) as sc")->where($wherey)->union("select count(*) as sc from tzg_mysupplier where btype <> '' and isdel=0", true)->union("select count(*) as sc from tzg_mysupplier where isdel=1", true)->union("select count(*) as sc from tzg_mysupplier where pricedavg <> 0", true)->union("select count(*) as sc from tzg_mysupplier where rstatus ='已关闭'", true)->select();
		}
		{ // 查询所有可筛选的类目
			$n=100000;
			$totals=$date->total();
			$catstr1='';
			for($i=0; $i*$n<$totals; $i++){
				$catb=db('mysupplier')->field('searchid')->where($where)->group('searchid')->limit($i*$n, $n)->select();
				foreach($catb as $vr){
					$catstr1.=$vr['searchid'].',';
				}
			}
			$catstr1=trim($catstr1, ',');
			$catstr1arr=explode(',', $catstr1);
			$catstr1arr=array_unique($catstr1arr); // 当前筛选所有的id
			$where2['isshow']=1;
			$where2['id']=array(
					'in',
					$catstr1arr
			);
			$catname=db('taobaoCat')->field('id,catename')->where($where2)->select();
			$catedataf=array(
					array(
							"id" => 0,
							"catename" => "所有类目"
					)
			);
			$catedata=array_merge($catedataf, $catname); // 类目赛选数组
		}

		$this->assign('catedata', $catedata);
		$this->assign("cid", $cid); // 筛选类目
		$this->assign('uid', $userid); // 赋值用户id
		$this->assign("date", $date); // 主数据
		$this->assign("cgroup", $cgroup); // 当前排序
		$this->assign('newCat', $newCat);
		$this->assign("n", $arr); // 显示计数数组
		$this->assign("datacolor", $datacolor); // 类目id重复值
		$this->assign("color", $color); // 颜色值
		$this->assign('sk', $date->total()); // 当前筛选的条数
		return $this->fetch();
	}

	// 详情页面
	public function datails(){
		if(!isset($_GET['id'])){
			exit(); // id不存在结束
		}
		$where=array(
				"a.id" => $_GET['id']
		);
		$join=array(
				array(
						"tzg_mysupplierdetail b",
						"a.id=b.id",
						"left"
				)
		);
		$data=db("mysupplier")->alias("a")->field("a.id,a.name,b.name as payname,btype,productcount,priceavg,province,city,brand,cateid,mcate,guarantee,isdel,contacts,phone,alipay,ctime,rtime,priceamax,priceamin,priceaavg,pricedmax,pricedmin,pricedavg,priceavg,searchid")->join($join)->where($where)->find();
		$idarr1=explode(";", $data['cateid']); // 类目数组
		$sidarr=explode(",", $data['searchid']); // 搜索数组
		$arr=array_merge($idarr1, $sidarr); // 合并数组
		$idarr=array();
		foreach($arr as $k => $v){
			// 循环处理数组,获得唯一的值
			if($v!=''){
				if(!in_array($v, $idarr)){
					$idarr[]=$v;
				}
			}
		}
		$carr=db("taobaoCat")->field("id,catename")->where("id", "in", $idarr)->select(); // 大类数组
		$pcate="";
		$searchidstr='';
		if(!empty($carr)){ // 类目列
			foreach($carr as $k => $v){
				if(in_array($v['id'], $idarr1)){
					$pcate.=$v['catename'].';';
				}
				if(in_array($v['id'], $sidarr)){
					$searchidstr.=$v['catename'].';';
				}
			}
			$pcate=substr($pcate, 0, -1);
			$searchidstr=substr($searchidstr, 0, -1);
		}

		$this->assign("pcate", $pcate); // 类目列表
		$this->assign("searchidstr", $searchidstr); // searchid列表
		$this->assign("data", $data);
		return $this->fetch();
	}

	// 类目管理
	public function cateinfo(){
		$p=15; // 分页数
		if(isset($_GET['is'])&&isset($_GET['topid'])){ // 置顶修改
			if($_GET['is']==1){
				$up=array(
						"isTop" => 1
				);
			}elseif($_GET['is']==0){
				$up=array(
						"isTop" => 0
				);
			}elseif($_GET['is']==-1){
				$up=array(
						"isTop" => -1
				);
			}
			$where=array(
					"id" => htmlspecialchars($_GET['topid']),
					"isshow" => 1
			);
			db("taobaoCat")->where($where)->update($up); // 数据库修改置顶字段
		}
		$group=""; // 排序字段
		$where=array(); // 条件
		$name="c";
		$ort="desc";
		$top=""; // 置顶字段
		if(isset($_GET['group'])){
			$top.="&group=$group";
			if($_GET['group']=='group1'){
				$name="catename";
				$ort="desc";
			}elseif($_GET['group']=='group2'){
				$name="catename";
				$ort="asc";
			}elseif($_GET['group']=='group3'){
				$name="c";
				$ort="desc";
			}elseif($_GET['group']=='group4'){
				$name="c";
				$ort="asc";
			}elseif($_GET['group']=='group5'){
				$name="s";
				$ort="desc";
			}elseif($_GET['group']=='group6'){
				$name="s";
				$ort="asc";
			}
		}else{
			$_GET['group']="group3";
		}
		$group=$_GET['group'];
		if(!isset($_GET['display'])){ // 是否显示
			$_GET['display']=3;
		}
		$display=$_GET['display'];

		if($_GET['display']==1){
			$where["isshow"]=1; // 显示
		}elseif($_GET['display']==2){
			$where["isshow"]=0;
		}
		$top.="&display=".$_GET['display'];
		$this->assign("group", $group); // 排序
		$join=array(
				array(
						"tzg_mysupplier b",
						"FIND_IN_SET(a.id,b.cateid)",
						"LEFT"
				)
		);
		{ // 查询当前供应商所有的类目
			$catstr1='';
			$catb=db('mysupplier')->field('cateid')->group('cateid')->select();
			foreach($catb as $vr){
				$catstr1.=$vr['cateid'].',';
			}
			$catstr1=trim($catstr1, ',');
			$catstr1arr=explode(',', $catstr1);
			$catstr1arr=array_unique($catstr1arr); // 当前使用的id
		}
		$w1=array(
				'id' => array(
						'in',
						$catstr1arr
				)
		);
		$sq=db('taobaoCat')->field(true)->where($w1)->buildSql();
		$where['a.id']=array(
				'in',
				$catstr1arr
		);
		$strid=implode($catstr1arr, ',');
		$data=db('taobaoCat')->alias('a')->field('a.id,catename,isshow,isTop,count(b.id) as c,sum(productcount) as s')->where($where)->join($join)->group("a.id")->order(" isTop desc,$name $ort")->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		{
			$dataDown=array(
					'table' => 'taobaoCat',
					'alias' => 'a',
					'field' => "a.id,catename,case isshow when 1 then '是' else '否' end as isshow,case isTop when 1 then '是' else '否' end as isTop,count(b.id) as c,sum(productcount) as s",
					'head' => array(
							'id',
							'类目名字',
							'是否显示',
							'是否置顶',
							'供应商数量',
							'商品数量'
					),
					'where' => $where,
					'join' => $join,
					'group' => 'a.id',
					'order' => " isTop desc,$name $ort"
			);
			$this->setDownSession($dataDown);
		}
		$this->assign("date", $data);
		$this->assign("display", $display);
		$this->assign("c", $data->total());
		$this->assign("top", $top);
		$ishow=array( // 是否显示数组
				'3' => '全部',
				'1' => '显示',
				'2' => '不显示'
		);
		$this->assign("ishow", $ishow);
		$toparr=array( // istop栏
				"0" => array(
						array(
								'key' => '1',
								'name' => '置&emsp;&emsp;顶',
								'class' => 'btn btn-success radius size-S'
						),
						array(
								'key' => '-1',
								'name' => '置&emsp;&emsp;底',
								'class' => 'btn btn-warning radius size-S'
						)
				),
				"1" => array(
						array(
								'key' => '0',
								'name' => '取消置顶',
								'class' => 'btn btn-primary radius size-S'
						),
						array(
								'key' => '-1',
								'name' => '置&emsp;&emsp;底',
								'class' => 'btn btn-warning radius size-S'
						)
				),
				"-1" => array(
						array(
								'key' => '0',
								'name' => '取消置底',
								'class' => 'btn btn-primary radius size-S'
						),
						array(
								'key' => '1',
								'name' => '置&emsp;&emsp;顶',
								'class' => 'btn btn-success radius size-S'
						)
				)
		);
		$page=isset($_GET['page'])?$_GET['page']:1; // 当前页码
		$this->assign('page', $page);
		$this->assign("toparr", $toparr);
		// 查询所有用户
		$where=array();
		$userdata=db('uinfo')->field("ID,Username")->where($where)->select();
		$fuser=array(
				array(
						'ID' => 0,
						'Username' => '所有用户'
				)
		);
		$userdata=array_merge($fuser, $userdata); // 合并首列
		$this->assign('userdata', $userdata);
		return $this->fetch();
	}

	// mysapply列表
	public function applylist(){
		$p=15; // 分页记录数
		$where=array();
		if(isset($_GET['group'])){ // 排序字段
			$group=$_GET['group'];
		}else{
			$group='productcount';
		}
		if(isset($_GET['dec'])){ // 倒序
			$dec=$_GET['dec'];
		}else{
			$dec='desc';
		}
		$sqgruop=$group.' '.$dec;
		$enablet=isset($_GET['enablet'])?$_GET['enablet']:-1; // 筛选交易状态
		$page=isset($_GET['page'])?$_GET['page']:1; // 当前页码
		if($enablet!=(-1)){
			if($enablet==0){
				$where['enable']=array(
						'in',
						'0,101'
				);
			}elseif($enablet==1){
				$where['enable']=array(
						'in',
						'1,2,3,111,112,113'
				);
			}elseif($enablet==6){
				$where['enable']=array(
						'in',
						'6,161'
				);
			}
		}
		if(!isset($_GET['cateid'])||$_GET['cateid']==0){
			$_GET['cateid']=0;
			$getcateid=$_GET['cateid']; // 筛选的类目
		}else{
			$getcateid=$_GET['cateid']; // 筛选的类目
			$where['']=array(
					"exp",
					"find_in_set($getcateid,cateid)"
			);
		}
		$wname=array(
				'status' => 3
		); // 查询合作的用户
		if(!isset($_GET['suserid'])||$_GET['suserid']==0){ // 查询指定用户
			$_GET['suserid']='';
		}else{
			$where['userid']=$_GET['suserid'];
			$wname['userid']=$_GET['suserid'];
		}
		$suserid=$_GET['suserid'];
		$join=array(
				array(
						"tzg_uinfo b",
						"b.ID=a.userid",
						"left"
				),
				array(
						"tzg_mysupplier c",
						"c.id=a.supplierid",
						"left"
				)
		);
		// 申请状态筛选
		$applyarr=array(
				'-1' => '所有状态',
				'0' => '未知',
				'1' => '可申请',
				'2' => '申请中',
				'3' => '合作中',
				'4' => '已拒绝',
				'5' => '已过期 '
		);
		$this->assign('applyarr', $applyarr);
		$apply=isset($_GET['apply'])?$_GET['apply']:'-1';
		if($apply>=0){
			$where['a.status']=$apply;
		}
		$this->assign('apply', $apply);
		$data=db("myapply")->alias("a")->field("userid,a.supplierid,b.Username,c.name,a.status,brand,updatetime,productcount,cateid,enable,
                        case when  brand='' then 0 ELSE (length(`brand`) - length(REPLACE(`brand`,".'"、"'.", ''))) div 3+1
                        END as bnum")->join($join)->where($where)->group("a.userid,a.supplierid")->order($sqgruop)->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		// {//下载
		// $dataDown=array(
		// 'table'=>'myapply',
		// 'alias'=>'a',
		// 'field'=>"b.Username,c.name,productcount,cateid,brand,case a.status when 0 then '未知/不可申请' when 1 then '可申请' when 2 then '申请中' when 3 then '合作中' when 4 then '已拒绝' when 5 then '已过期' end as status,case enable when 0 then '默认' when 1 then '风险供应' when 2 then '交易违约' when 3 then '超额供应' when 6 then '独享供应' when 101 then '品牌供应' when 111 then '代理供应' end as enable,
		// case when brand='' then 3 ELSE (length(`brand`) - length(REPLACE(`brand`,".'"、"'.", ''))) div 3+1
		// END as bnum ,updatetime",
		// 'head'=>array('用户名','供应商商名','产品数量','类目','品牌','状态','上架方式','品牌数量,更新时间'),
		// 'where'=>$where,
		// 'join'=>$join,
		// 'group'=>'b.Username,c.name',
		// 'order'=>" $sqgruop"
		// );
		// $this->setDownSession($dataDown);
		// }
		{ // 查询所有的供应商筛选类目
			{ // 查询当前供应商所有的类目
				$catstr1='';
				$catb=db('mysupplier')->field('searchid')->group('searchid')->select();
				foreach($catb as $vr){
					$catstr1.=$vr['searchid'].',';
				}
				$catstr1=trim($catstr1, ',');
				$catstr1arr=explode(',', $catstr1);
				$catstr1arr=array_unique($catstr1arr); // 当前使用的id
			}
			$w1=array(
					'id' => array(
							'in',
							$catstr1arr
					),
					'isshow' => 1
			);
			$cname=db('taobaoCat')->field('id,catename')->where($w1)->select();
			$ar1=array(
					array(
							'id' => 0,
							'catename' => '所有类目'
					)
			);
			$cname=array_merge($ar1, $cname);
		}
		{ // 查找当前页码的cateid
			$da=$data->toArray();
			$ids='';
			foreach($da['data'] as $vt){
				$ids.=$vt['cateid'].',';
			}
			$ids=trim($ids, ',');
			$ids=explode(',', $ids);
			$ids=array_unique($ids);
			$w2=array(
					'id' => array(
							'in',
							$ids
					)
			);
			$idcate=db('taobaoCat')->field('id,catename')->where($w2)->select();
			if($idcate){
				$idcate=$this->arrayColumn($idcate, 'catename', 'id', 1);
			}
		}
		// 查询合作的供应商
		$capp=db('myapply')->field('count(*) as c')->where($wname)->find();
		$this->assign('capp', $capp['c']);
		$dataarr=$data->toArray();
		$needdata=$dataarr['data']; // 数据数组
		$w=array(
				'enable' => '6'
		);
		$inde=db('myapply')->field('userid,supplierid')->where($w)->select();
		$indearr=$this->arrayColumn($inde, 'userid', 'supplierid', 1);
		if($needdata){
			$name=array(); // 用户数组
			$supplier=array(); // 供应商数组
			$strwhere='';
			foreach($needdata as $k => $v){ // 获得供应商和用户
				$n=$v['Username'];
				$i=$v['supplierid'];
				$strwhere.="(Username = '$n' AND supplierID='$i') OR ";
			}
			$str=substr($strwhere, 0, -4);
			$whererela=array( // 关联商品数量条件
					'' => array(
							'exp',
							$str
					),
					'state' => array(
							'in',
							'已上架,未上架'
					)
			);

			$relation=db('distribution')->field('Username,supplierID,count(ID) as relation')->where($whererela)->group('Username,supplierID')->select();

			foreach($needdata as $k => $v){
				foreach($relation as $v1){
					if($v1['Username']==$v['Username']&&$v1['supplierID']==$v['supplierid']){
						$needdata[$k]['relation']=$v1['relation'];
						break;
					}
				}
				if(!isset($needdata[$k]['relation'])){
					$needdata[$k]['relation']=0;
				}
			}
		}
		// 查询出用户字段
		$join2=array(
				array(
						"tzg_uinfo b",
						"b.ID=a.userid",
						"left"
				)
		);
		$resule=db("myapply")->alias("a")->field("distinct userid,Username")->join($join2)->select();
		$userf=array(
				array(
						'userid' => '0',
						"Username" => '所有用户'
				)
		); // 添加所有选项
		$wreall=array(
				'state' => array(
						'in',
						'未上架,已上架'
				)
		); // 查询已关联记录条件
		$uname=$this->arrayColumn($resule, 'Username', 'userid', 1);
		if($_GET['suserid']!=''&&$_GET['suserid']!=0){
			foreach($resule as $vs){
				if($vs['userid']==$_GET['suserid']){
					$wreall['Username']=$uname[$_GET['suserid']]; // 筛选用户
				}
			}
		}
		$total=db('distribution')->field('count(ID) as t')->where($wreall)->find();
		$enables=array( // 目录数组
				'-1' => '上架方式',
				'0' => '默认',
				'1' => '风险供应',
				'6' => '独享供应'
		);
		$enable=array( // 列表数组
				'0' => '默认',
				'1' => '风险供应',
				'2' => '交易违约',
				'3' => '超额供应',
				'6' => '独享供应',
				'101' => '品牌供应',
				'111' => '代理供应',
				'112' => '分店供应',
				'113' => '虚假发货',
				'161' => '品牌独享'
		);
		$red=db('myapply')->field('supplierid,count(supplierid) as countid')->where('status', '3')->group('supplierid')->having('countid > 1')->select(); // 查询出合作中的重复的
		$red=$this->arrayColumn($red, 'countid', 'supplierid', 1);
		$blue=db('myapply')->field('supplierid,count(supplierid) as countid')->where('status', 'in', '1,3')->group('supplierid')->having('countid > 1')->select(); // 查询出状态为可申请、合作中的重复的供应商
		$blue=$this->arrayColumn($blue, 'countid', 'supplierid', 1);
		$this->assign('enables', $enables); // 交易状态列表选项
		$this->assign('enable', $enable); // 列 交易 状态
		$this->assign('enablet', $enablet); // 当前筛选条件
		$this->assign('page', $page); // 当前页码
		$resule=array_merge($userf, $resule);
		$this->assign("userst", $resule); // 筛选用户
		$this->assign("sid", $suserid);
		$this->assign("getcateid", $getcateid);
		$this->assign("catedata", $cname); // 筛选的类目
		$this->assign('blue', $blue); // 可申请、合作中重复的供应商id
		$this->assign('red', $red); // 合作中的重复的
		$this->assign('indearr', $indearr); // 独立供应
		$this->assign('group', $group);
		$this->assign('dec', $dec);
		$this->assign('idcate', $idcate);
		$this->assign('total', $total); // 已关联商品条数
		$this->assign('needdata', $needdata); // 主数据
		$this->assign("data", $data);
		$this->assign("c", $data->total());
		return $this->fetch();
	}

	// 用户、类目关系
	public function usercate(){
		$p=15; // 分页数量
		$where=array();
		$page=isset($_GET['page'])?$_GET['page']:1; // 当前页码
		$currcate=isset($_GET['currcate'])?$_GET['currcate']:3; // 当前的状态
		if($currcate!=3){
			$where['isapply']=$_GET['currcate'];
		}
		$join=array(
				array(
						"tzg_uinfo b",
						"a.userid=b.ID",
						'left'
				)
		);
		// 筛选用户
		$userids=isset($_GET['userid'])?$_GET['userid']:0;
		if($userids!=0){
			// 查询指定用户
			$where['userid']=$userids;
		}
		$data=db("myapplycate")->alias("a")->field("Username,isapply,updatetime,userid,cateid")->join($join)->where($where)->order("Username asc,isapply desc")->paginate($p, false, [
				'query' => Request::instance()->param()
		]);
		$joini=$join;
		$joini[]=array(
				'tzg_taobao_cat c',
				'c.id=a.cateid',
				'left'
		);
		$dataDown=array(
				'table' => 'myapplycate',
				'alias' => 'a', // cateid
				'field' => "Username,catename,case isapply when 1 then '启用' else '未启用' end as isapply,updatetime",
				'head' => array(
						'用户名',
						'类目id',
						'是否启用',
						'修改时间'
				),
				'where' => $where,
				'join' => $joini,
				'order' => " Username asc,isapply desc"
		);
		$this->setDownSession($dataDown);

		$dataarr=$data->toArray()['data'];
		$idarr=array();
		$catids='';
		foreach($dataarr as $v){
			if(isset($idarr[$v['userid']])){
				$idarr[$v['userid']]+=1;
			}else{
				$idarr[$v['userid']]=1;
			}
			$catids.=$v['cateid'].',';
		}
		$catids=trim($catids, ',');
		$catids=explode(',', $catids);
		$catids=array_unique($catids);
		$w1=array(
				'id' => array(
						'in',
						$catids
				)
		);
		$catename=db('taobaoCat')->field('id,catename')->where($w1)->select();
		$cat=$this->arrayColumn($catename, 'catename', 'id', 1);
		$joinuser=array( // 查询所有用户列表
				array(
						"tzg_uinfo b",
						"a.userid=b.ID",
						'left'
				)
		);
		$whereuser=array();
		$userdata=db("myapplycate")->alias("a")->field("ID,Username")->join($joinuser)->where($whereuser)->group("userid")->select();
		$userf=array(
				array(
						'ID' => 0,
						'Username' => '选择用户'
				)
		);
		$cataarr=array( //
				'3' => '全部状态',
				'1' => '启用',
				'0' => '未启用'
		);
		$recate=db('myapplycate')->field('cateid')->group('cateid')->having('count(cateid)>1')->select();
		$redata=array();
		foreach($recate as $v){
			$redata[$v['cateid']]='#FF4500';
		}
		$userdata=array_merge($userf, $userdata);
		$this->assign("userdata", $userdata); // 用户数组
		$this->assign('uids', $userids); // 选择的用户
		$this->assign("page", $page); // 分页
		$this->assign('cataarr', $cataarr); // 状态数组
		$this->assign('currcate', $currcate); // 当前数组
		$this->assign('redata', $redata); // 重复的类目数组
		$this->assign('urow', $idarr);
		$this->assign("data", $data); // 主数据
		$this->assign("c", $data->total()); // 总记录数
		$this->assign('cat', $cat);
		return $this->fetch();
	}

	/**
	 * 修改用户类目状态
	 *   */
	public function updataUc(){
		if(!isset($_GET['updata'])||!isset($_GET['cateid'])||!isset($_GET['userid'])){
			exit();
		}
		$state=$_GET['updata']; // 修改的状态
		$cateid=$_GET['cateid']; // 类目id
		$userid=$_GET['user']; // 用户id
		$where=array(
				"cateid" => $cateid,
				"userid" => $userid
		);
		$updata=array(
				'isapply' => $state
		);
		$url='';
		if(isset($_GET['userid'])){
			$url="?userid=".$_GET['userid'];
		}
		$result=db('myapplycate')->where($where)->update($updata); // 修改数据库
		header("location:usercate$url");
		exit();
	}

	// cateinfo添加用户和类目关系
	public function cruc(){
		if(!isset($_POST['cid'])||!isset($_POST['uid'])){
			exit();
		}
		$cid=htmlspecialchars($_POST['cid']);
		$uid=htmlspecialchars($_POST['uid']);
		$inser=array(
				'userid' => $uid,
				'cateid' => $cid,
				'isapply' => 1
		);
		$data=db('myapplycate')->field('userid')->where($inser)->find();
		if(empty($data)){ // 为空
			$relute=db('myapplycate')->insert($inser);
			if($relute>0){
				echo 1;
			}else{
				echo 3;
			}
			exit();
		}else{ // 已存在
			echo 2;
			exit();
		}
	}

	/* 修改myapply emable */
	public function upenable(){
		if(!isset($_GET['ena'])||!isset($_GET['uid'])||!isset($_GET['sid'])){
			header("location:applylist");
			exit();
		}
		if($_GET['ena']==6){
			$w1=array(
					'enable' => 6,
					'supplierid' => htmlspecialchars($_GET['sid'])
			);
			$d=db('myapply')->where($w1)->find();
			if($d){
				echo 2;
				exit();
			}
		}
		$updata=array(
				'enable' => htmlspecialchars($_GET['ena'])
		);
		$where=array(
				'userid' => htmlspecialchars($_GET['uid']),
				'supplierid' => htmlspecialchars($_GET['sid'])
		);
		$result=db("myapply")->where($where)->update($updata);
		if($result>0){
			echo 1;
		}else{
			echo 2;
		}
		exit();
	}
}