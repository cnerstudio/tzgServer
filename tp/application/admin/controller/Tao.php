<?php

namespace app\admin\controller;

use think\Request;
use app\api\controller\Pddapi;
use app\api\controller\Logapi;

class Tao extends Echarts {
	/* 淘宝商品详情 */
	public function goodsInfo(){
		$p=15;
		$w=array();
		$url='';
		{ // 定义参数
			isset($_GET['value'])?$num=$_GET['value']:$num=100; // 上传数量
			isset($_GET['bol'])?$bol=$_GET['bol']:$bol=2; // + or *
			isset($_GET['svn'])?$svn=$_GET['svn']:$svn=1.5;
		}
		if(isset($_GET['cid'])&&$_GET['cid']!=(-1)){ // 类目状态
			$w['cid']=$_GET['cid'];
			$cid=$_GET['cid'];
		}else{
			$cid=150704; // 默认全部:(-1);
		}
		if(isset($_GET['cate'])&&$_GET['cate']!=-1){
			$cate=$_GET['cate'];
			if($cate==1){
				$w['state']='已上架';
			}elseif($cate==2){
				$w['state']='未上架';
			}elseif($cate==3){
				$w['state']='未关联';
			}elseif($cate==4){
				$w['state']='已删除';
			}
		}else{
			$cate=-1;
		}
		if(isset($_GET['staid'])&&$_GET['staid']!=-1){
			$staid=$_GET['staid'];
			$w['goods_id']=array(
					'exp',
					'is not null'
			);
			if($staid==110){
				$str='and is_onsale = 0';
			}elseif($staid==1){
				$str='and is_onsale = 1';
			}elseif($staid==2){
				$str='and is_onsale = 2';
			}elseif($staid==3){
				$str='and is_onsale = 3';
			}elseif($staid==4){
				$str='and is_onsale = 4';
			}elseif($staid==44){
				$str='and is_onsale = 44';
			}elseif($staid==100){
				$str='and is_onsale = 100';
			}elseif($staid==101){
				$str='and is_onsale = 101';
			}elseif($staid==103){
				$str='and is_onsale = 103';
			}elseif($staid==111){
				$str='';
				$w['goods_id']=array(
						'exp',
						'is null'
				);
				$w['errormsg']=array(
						'exp',
						'is null'
				);
			}
			$join=array(
					array(
							'tzg_pdd_goods b',
							"a.itemId=b.itemId $str",
							'left'
					)
			);
			$cl=',errormsg';
		}else{
			$staid=-1;
			$str='';
			$join=array();
			$cl='';
		}
		if(isset($_GET['searchgoods'])&&$_GET['searchgoods']!=''){ // searchgoods 商品名字
			$goodsname=$_GET['searchgoods'];
			$w['a.title']=array(
					'like',
					"%$goodsname%"
			);
		}else{
			$goodsname='';
		}

		isset($_GET['shopid'])?$shop=$_GET['shopid']:$shop=0;

		$data=db('taobaoTaeItems')->alias('a')->field("a.itemId,a.title,a.reserve_price,a.num,a.cid,a.updatetime,a.state,a.catename $cl")-> // ,b.is_onsale,shopID
		where($w)->join($join)->order('updatetime desc')->paginate($p, false, [
				'query' => Request::instance()->param()
		]);

		{ // 下载
			$join=array(
					array(
							'tzg_pdd_goods b',
							"a.itemId=b.itemId",
							'left'
					)
			);
			$dataDown=array(
					'table' => 'taobaoTaeItems',
					'alias' => 'a', // cateid
					'field' => "a.itemId,a.title,a.reserve_price,a.num,a.catename,a.updatetime,a.state,case  when b.itemId is null then '未上架' when b.itemId is not null and (errormsg is null or errormsg='') then '已上架' when b.itemId is not null and (errormsg is not null or errormsg!='' ) then '上架失败' end as p",
					'head' => array(
							'id',
							'名字',
							'一口价',
							'库存',
							'类目id',
							'更新时间',
							'状态',
							'上架状态'
					),
					'where' => $w,
					'join' => $join,
					'order' => "updatetime desc"
			);
			$this->setDownSession($dataDown);
		}
		if($staid==-1){ // 判断全部状态的时候
			if($staid==-1){
				$dataarr=$data->toArray();
				$itemid=array();
				foreach($dataarr['data'] as $v){
					$itemid[]=$v['itemId'];
				}
				if($itemid){
					$w2=array(
							'itemId' => array(
									'in',
									$itemid
							) // case when errormsg is null then -2 else errormsg end errormsg
					);
					$result1=db('pddGoods')->field('itemId,case when goods_id is null then 111 when goods_id is not null and is_onsale=0 then errormsg else is_onsale end as is_onsale ')->where($w2)->select(false);
					if($result1){ // 存在itemid字段，成功or失败的
						$result1=$this->arrayColumn($result1, 'is_onsale', 'itemId', 1);
					}
				}
			}
			foreach($itemid as $bk){
				if(!isset($result1[$bk])){
					$result1[$bk]=111;
				}
			}
			$this->assign('result1', $result1);
		}

		// 查询上架错误
		$w1=array(
				'' => array(
						'exp',
						'errormsg is not null and errormsg != "" and is_onsale < 4'
				)
		);
		$errData=db('pddGoods')->field('errormsg,count(errormsg) as total')->where($w1)->group('errormsg')->having('count(errormsg)>1')->order('total desc')->select();    
		// 类目筛选
		$cidarr=db('taobaoTaeItems')->field('cid,catename')->group('cid')->select();
		$cidarr=$this->arrayColumn($cidarr, 'catename', 'cid', 1);
		$cidarr['-1']='所有类目';
		ksort($cidarr);
		$sta=array(
				'-1' => '拼状态',
				'1' => '已上架',
				'2' => '已下架',
				'3' => '已售罄',
				'4' => '已删除',
				'44' => '彻底删除',
				'100' => '编辑中',
				'101' => '待审核',
				'103' => '审核驳回',
				'110' => '上架失败',
				'111' => '未上传'
		); // 上传状态
		   // 淘宝状态
		$cate2=array(
				'-1' => '淘状态',
				'1' => '已上架',
				'2' => '未上架',
				'3' => '未关联',
				'4' => '已删除'
		);
		$pddapi= new Pddapi();
		$shoparr=$pddapi->getShops();
		$this->assign('errData', $errData); // 错误分组统计
		$this->assign('data', $data);
		$this->assign('shoparr', $shoparr); // 当前所有的店铺
		$this->assign('shop', $shop);
		$this->assign('cidarr', $cidarr);
		$this->assign('cid', $cid);
		$this->assign('sta', $sta);
		$this->assign('staid', $staid);
		$this->assign('goodsname', $goodsname);
		$this->assign('cate2', $cate2);
		$this->assign('cate', $cate);
		$this->assign('num', $num); // 上传的数量
		$this->assign('bol', $bol); // 符号
		$this->assign('svn', $svn); // 数值
		return $this->fetch();
	}

	/* 拼多多淘宝属性对应 */
	public function toProp(){
		$p=15;
		$w=[];
		if(isset($_GET['cateid'])&&!empty($_GET['cateid'])){
			$cateid=$_GET['cateid'];
			$w['cate']=['like',"%,$cateid,%"];
		}
		if(isset($_GET['name'])&&!empty($_GET['name'])){
			$name=$_GET['name'];
			$w['']=['exp',"pname like '%$name%' or tname like '%$name%'"];
		}else{
			$name='';
		}
		$daata=db('pddProp')->field(true)->where($w)->order('pname asc')
				->paginate($p, false, ['query' => Request::instance()->param()]);
		{ // 下载使用
			$dataDown=array(
					'table' => 'pddProp',
					'field' => true,
					'head' => array(
							'拼多多id',
							'拼多多属性名',
							'淘宝属性名',
							'级别'
					),
					'where' => $w,
					'order' => "pname asc"
			);
			$this->setDownSession($dataDown);
		}

		$cate=array(
				'A001' => '类目',
				'A002' => '品牌',
				'A003' => '型号'
		);
		$ws=$w;
		$ws['pname']='';
		$nullTotal=db('pddProp')->field('count(tname) as total')->where($ws)->find();
		$this->assign('nullTotal', $nullTotal);
		$this->assign('cate', $cate);
		$this->assign('data', $daata);
		$this->assign('cateid', $cateid);
		$this->assign('name', $name);
		$this->assign('suffix', isset($_GET['suffix'])?$_GET['suffix']:'');
		return $this->fetch();
	}

	/**
	 * 商品价格分布图
	 */
	public function goodsChart(){
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

		$cs=array(
				'1' => '按月',
				'2' => '按季度',
				'3' => '半年',
				'4' => '一年',
				'5' => '全部'
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
		$data=db('logPddgoods')->field(true)->where($wlog)->order('time asc')->select();
		if($data){
			if($cur==5){
				$times=strtotime($data[0]['time']);
				$time=date('Y-m-d', $times);
			}
		}else{
			$times=time();
			$time=date('Y-m-d', $times);
		}
		$cat=array(
				'10-',
				'10~20',
				'20~35',
				'35~60',
				'60~100',
				'100+'
		);
		$wlog=array();
		if(isset($days)){
			$times=$totime-$days*86400;
			$time=date('Y-m-d', $times);
			$wlog['time']=array(
					'>=',
					$time
			);
		}
		$data=db('logPddgoods')->field(true)->where($wlog)->order('time,pricerange asc')->select();
		$seriestemp=array(); // 区间缓存
		$dataseries=array();
		foreach($data as $vk => $vs){
			$pricerange=$vs['pricerange']; // 区间序号0~5
			if(isset($seriestemp[$pricerange])){
				$serieslast=$seriestemp[$pricerange];
				$dataseries=$this->fillchart($dataseries, $serieslast['time'], strtotime($vs['time']), $cat[$pricerange], $serieslast['cnt']);
			}else
				$dataseries=$this->fillchart($dataseries, $times, strtotime($vs['time']), $cat[$pricerange], 0);
			$seriestemp[$pricerange]=array(
					'time' => strtotime($vs['time']),
					'cnt' => $vs['goodcnt']
			);
		}

		foreach($seriestemp as $vk => $serieslast){
			$dataseries=$this->fillchart($dataseries, $serieslast['time'], $totime, $cat[$vk], $serieslast['cnt']);
		}
		$this->assign('cur', $cur);
		$this->assign('cs', $cs);
		$this->assign('dataseries', json_encode($dataseries));
		return $this->fetch();
	}
}