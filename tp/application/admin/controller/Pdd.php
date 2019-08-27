<?php

// 拼多多后台管理
namespace app\admin\controller;

use think\Request;
use app\api\controller\Pddapi;
use app\api\model\PddGoods;
use app\api\model\PddOrder;
use think\Config;

class Pdd extends Base {

	// 获得拼多多商品详情
	public function getList(){
		$p=15;
		$where=array();
		if(isset($_GET['supply'])){
			$supply=$_GET['supply'];
			if($_GET['supply']==1){
				$where['itemId']=array(
						'neq',
						''
				);
			}elseif($_GET['supply']==2){
				$where['itemId']='';
			}
		}else{
			$supply=-1;
		}
		if(isset($_GET['order'])&&$_GET['order']==1){
			$order='goods_name asc,goods_quantity desc,onsale_price desc';
			$this->assign('order', 1);
		}else{
			$order='goods_quantity desc,onsale_price desc';
			$this->assign('order', 2);
		}
		isset($_GET['searchgoods'])?$goodsname=$_GET['searchgoods']:$goodsname="";
		isset($_GET['catid'])?$curcat=$_GET['catid']:$curcat=(-1);
		isset($_GET['catid'])&&$_GET['catid']!=(-1)?$where['a.cat_id']=$_GET['catid']:'';
		isset($_GET['isonsale'])&&$_GET['isonsale']!=(-1)?$where['is_onsale']=$_GET['isonsale']:$where['is_onsale']=array(
				'exp',
				' > 0 and is_onsale !=44'
		);
		isset($_GET['isonsale'])?$curstate=$_GET['isonsale']:$curstate=(-1);
		isset($goodsname)&&$goodsname!=""?$where['goods_name']=array(
				'like',
				"%$goodsname%"
		):'';
		$sxw=array(
				'difference_price' => array(
						'<>',
						''
				)
		);
		if(isset($_GET['shopid'])&&$_GET['shopid']!=0){
			$where['shopID']=$_GET['shopid'];
			$shop=$_GET['shopid'];
			$sxw['shopID']=$shop;
		}else{
			$where['shopID']='446612686'; // 改成默认选择第一个下拉店铺
			$shop='446612686';
			$sxw['shopID']=$shop;
		}
		if(isset($_GET['diff'])&&$_GET['diff']!='全部公式'){
			$diff=trim($_GET['diff']);
			if(strpos($diff, '*')===false){ // 没有符号 ++
				if(strpos($diff, '-')===false){
					$diff='+'.$diff;
				}else{
					$diff=$_GET['diff'];
				}
			}
			$where['difference_price']=$diff;
		}else{
			$diff='全部公式';
		}
		$data=PddGoods::alias('a')->field('goods_id,goods_name,goods_quantity,difference_price,cat_id,market_price,onsale_price,is_onsale,updatetime,itemId')->where($where)->order($order)
		->paginate($p, false, ['query' => Request::instance()->param()]);
		{ // 下载使用
			$joins=array(
					array(
							'tzg_pdd_cate b',
							'a.cat_id=b.cat_id',
							'left'
					)
			);
			$dataDown=array(
					'table' => 'pddGoods',
					'alias' => 'a',
					'field' => 'goods_id,goods_name,goods_quantity,difference_price,cat_name,market_price,onsale_price,case is_onsale when 1 then "已上架" when 2 then "已下架" when 3 then "已售罄" when 4 then "已删除" end as is_onsale,updatetime,itemId',
					'head' => array(
							'商品id',
							'商品名',
							'库存',
							'计算公式',
							'类目id',
							'市场价格(单位分)',
							'销售价格(单位分)',
							'上架状态',
							'更新时间',
							'淘宝id'
					),
					'join' => $joins,
					'where' => $where,
					'order' => "$order"
			);
			$this->setDownSession($dataDown);
		}
		// 查询类目
		$w2=array(
				'cat_id' => array(
						'neq',
						'0'
				)
		);

		$catarr=PddGoods::field('cat_id')->where($w2)->group('cat_id')->select();
		$catid=$this->arrayColumn($catarr, 'cat_id');
		$w3=array(
				'cat_id' => array(
						'in',
						$catid
				)
		);
		$catname=db('pddCate')->field('cat_id,cat_name')->where($w3)->select();
		$cat=$this->arrayColumn($catname, 'cat_name', 'cat_id', 1);
		$cat['-1']='所有类目';
		ksort($cat);
		// 定义上架状态数组
		$state=array(
				'-1' => '上架状态',
				'1' => '已上架',
				'2' => '已下架',
				'3' => '已售罄',
				'4' => '已删除',
				'44' => '彻底删除',
				'101' => '待审核',
				'103' => '审核驳回'
		);
		$sx=PddGoods::field('difference_price')->where($sxw)->group('difference_price')->select();
		$sxt=$this->arrayColumn($sx, 'difference_price');
		array_unshift($sxt, '全部公式');

		if($shop!=0){
			$w1['shopID']=$shop;
		}
		$pddapi= new Pddapi();
		$shoparr=$pddapi->getShops();
		$this->assign('shoparr', $shoparr);
		$this->assign('shop', $shop);
		$this->assign('data', $data); // 主数据
		$this->assign('cat', $cat);
		$this->assign('curcat', $curcat); // 当前的类目
		$this->assign('state', $state); // 状态
		$this->assign('curstate', $curstate); // 当前上架状态
		$this->assign('goodsname', $goodsname); // 当前筛选商品
		$this->assign('sxt', $sxt);
		$this->assign('diff', $diff);
		$this->assign('supply', $supply);
		return $this->fetch();
	}

	/* 店铺管理 */
	public function shop(){
		$p=15;
		$w=array();
		$join=array(
				array(
						'tzg_pdd_goods b',
						'b.shopID=a.id',
						'left'
				)
		);
		$data=db('pddConfig')->alias('a')->field('id,mall_name,refresh_token_time,count(goods_id) as goodsnum,(unix_timestamp(refresh_token_time)-unix_timestamp(now()))  as time,a.cost_template_id')->where($w)->join($join)->group('id')->paginate($p, false, [
				'query' => Request::instance()->param()
		]);

		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 订单列表
	 */
	public function order(){
		{ // 设置初始化参数
			$p=15;
			$w=array();
			isset($_GET['shopid'])?$shop=$_GET['shopid']:$shop='446612686';
			if(isset($_GET['catid'])&&$_GET['catid']!=-1){
				$catid=$_GET['catid'];
				$w['cat_id']=$_GET['catid'];
			}else{
				$catid=-1;
			}
			if(isset($_GET['confirm'])&&($_GET['confirm']==1||$_GET['confirm']==2)){ // 成交状态
				$confirm=$_GET['confirm'];
				$w['confirm_status']=$confirm;
			}elseif(isset($_GET['confirm'])&&$confirm=$_GET['confirm']==3){
				$w['confirm_status']=0;
				$confirm=3;
			}else{
				$confirm=-1;
			}
			if(isset($_GET['group'])&&($_GET['group']==1||$_GET['group']==2)){ // 成团状态
				$w['group_status']=$_GET['group'];
				$group=$_GET['group'];
			}elseif(isset($_GET['group'])&&$_GET['group']==3){
				$w['group_status']=0;
				$group=3;
			}else{
				$group=-1;
			}
			if(isset($_GET['order'])&&$_GET['order']!=-1){ // 订单状态
				$order=$_GET['order'];
				$w['order_status']=$order;
			}else{
				$order=-1;
			}
			if(isset($_GET['after'])&&$_GET['after']!=-1){
				if($_GET['after']==1){
					$w['after_sales_status']=0;
					$after=1;
				}else{
					$w['after_sales_status']=$_GET['after'];
					$after=$_GET['after'];
				}
			}else{
				$after=-1;
			}
		}
		{ // 获取店铺信息
			$pddapi= new Pddapi();
			$shoparr=$pddapi->getShops();
			$w['shop']=$shop;
		}
		{
			$data=db('pddOrder')->field('order_sn,receiver_name,confirm_status,group_status,order_status,after_sales_status,goods,goods_amount,pay_amount,pay_time,cat_id')->where($w)->order('pay_time desc')->paginate($p, false, [
					'query' => Request::instance()->param()
			]);
		}
		{ // 处理商品数组
			/* dump($data);exit(); */
			if($data){
				$dataAarr=$data->toArray();
				$goodname=array();
				foreach($dataAarr['data'] as $v){
					$goods=json_decode($v['goods'], true);
					$str='';
					$num=0;
					foreach($goods as $vk => $vv){
						$str.=$vv['goods_name'].'<br/>';
						$num+=$vv['goods_count'];
					}
					trim($str, '<br/>');
					$goodname[$v['order_sn']]['goods_name']=$str;
					$goodname[$v['order_sn']]['goods_count']=$num;
				}
			}
		}
		{ // 查询订单所有的类目
			$join1=array(
					array(
							'tzg_pdd_cate b',
							'a.cat_id=b.cat_id',
							'left'
					)
			);
			$cate=db('pddOrder')->alias('a')->field('a.cat_id,cat_name')->join($join1)->group('a.cat_id')->select();
			$catename=$this->arrayColumn($cate, 'cat_name', 'cat_id', 1);
			$catename['-1']='所有类目';
			asort($catename);
		}
		{ // 定义筛选状态
			$confirm_status=array(
					'-1' => '成交状态',
					'3' => '未成交',
					'1' => '已成交',
					'2' => '已取消'
			); // 成交状态
			$group_status=array(
					'-1' => '成团状态',
					'3' => '拼团中',
					'1' => '已成团',
					'2' => '团失败'
			); // 成团状态
			$order_status=array(
					'-1' => '订单状态',
					'1' => '待发货',
					'2' => '待签收',
					'3' => '已签收'
			); // 订单状态
			$after_sales_status=array(
					'-1' => '售后状态',
					'0' => '无售后',
					'2' => '买家申请退款，待商家处理',
					'3' => '退货退款，待商家处理',
					'4' => '商家同意退款，退款中',
					'5' => '平台同意退款，退款中',
					'6' => '驳回退款，待买家处理',
					'7' => '已同意退货退款,待用户发货',
					'8' => '平台处理中',
					'9' => '平台拒绝退款，退款关闭',
					'10' => '退款成功',
					'11' => '买家撤销',
					'12' => '买家逾期未处理，退款失败',
					'13' => '买家逾期，超过有效期',
					'14' => '换货补寄待商家处理',
					'15' => '换货补寄待用户处理',
					'16' => '换货补寄成功',
					'17' => '换货补寄失败',
					'18' => '换货补寄待用户确认完成'
			); // 售后状态
		}
		$this->assign('data', $data);
		$this->assign('shoparr', $shoparr);
		$this->assign('shop', $shop);
		$this->assign('goodname', $goodname);
		$this->assign('catename', $catename);
		$this->assign('catid', $catid);
		$this->assign('after_sales_status', $after_sales_status);
		$this->assign('confirm_status', $confirm_status);
		$this->assign('group_status', $group_status);
		$this->assign('order_status', $order_status);
		$this->assign('confirm', $confirm);
		$this->assign('group', $group);
		$this->assign('order', $order);
		$this->assign('after', $after);
		return $this->fetch();
	}
	
	/**
	 * 刷新授权
	 */
	public function warrant(){
		$client_id=Config::get('pdd.client_id');
		$url=REDIRECT_URI;
		$href="http://mms.pinduoduo.com/open.html?response_type=code&client_id=$client_id&redirect_uri=$url&state=126";
		header("Location:$href");
		exit();
	}

	
	/**
	 * 下架商品
	 */
	public function downShelf(){
		try{
			if(!isset($_POST['shop'])||!isset($_POST['data'])||!isset($_POST['status'])){
				echo '数据错误';
				exit();
			}
			$pddapi= new Pddapi();
			$ini=$pddapi->_init($_POST['shop']); // 初始化
			if($ini===0){
				echo 'code错误';
				exit();
			}elseif($ini==2){
				echo '店铺不存在';
				exit();
			}
			if($_POST['status']!=2&&$_POST['status']!=1){
				echo '状态错误';
				exit();
			}
			$_POST['data']=substr($_POST['data'], 0, -1);
			$id=explode(',', $_POST['data']);
			foreach($id as $v){
				$pddapi->PddGoodsSaleStatusSet($v, $_POST['status']);
			}
			echo 1;
		}catch(\Exception $e){
			echo $e->getMessage();
		}
		exit();
	}


	/**
	 * 修改商品价格
	 */
	public function upPrice(){		
		$validate=validate('Pdd');
		$result=$validate->scene('upPrice')->check($_POST);
		if(true!==$result){
			return json(['message'=>$validate->getError(), 'result'=>0]);
		}
		try{
			$pddapi= new Pddapi();
			$ini=$pddapi->_init($_POST['shop']); // 初始化
			if($ini===0){
				return json(['message'=>'code错误', 'result'=>0]);
			}elseif($ini==2){
				return json(['message'=>'店铺不存在', 'result'=>0]);
			}
			if($_POST['id']==1){ // 修改选中的
				$goodid=trim($_POST['data'], ',');
				$w['goods_id']=['in',$goodid];
			}elseif($_POST['id']==2){ // 选中类目
				$w['cat_id']=$_POST['data'];
			}else{
				return json(['message'=>'类目条件错误', 'result'=>0]);
			}
			$synbol=$_POST['synbol']; // 符号
			$value=$_POST['value']; // 值
			$w['']=array(
					'exp',
					'difference_price !="" and difference_price!=(-1)'
			);
			$w['shopID']=$_POST['shop'];
			$w['is_onsale']=1;
			$data=PddGoods::field('jsondata,goods_id,difference_price,price_info,delivery_info')->where($w)->select();

			if(!$data){
				return json(['message'=>'无满足条件的商品', 'result'=>0]);
			}
			$sun=0; // 修改成功条数
			$errn=0; // 失败的条数
			foreach($data as $v){
				$update=array();
				$car=json_decode($v['delivery_info'], true); // 运费
				if($car['CarriageList'][0]['Price']=='免运费'){
					$carr=0;
				}else{
					$carr=$car['CarriageList'][0]['Price'];
				}
				$pricearr=json_decode($v['price_info'], true);
				$jsondata=json_decode($v['jsondata'], true);
				$sku=json_decode($jsondata['sku_list'], true);
				$newsku=array();
				foreach($pricearr['SkuPriceList'] as $vd){
					$sku_price=array();
					foreach($sku as $vsk){
						if($vd['SkuId']==$vsk['skutaoid']){
							$sku_price=array(
									'sku_id' => $vsk['sku_id']
							);
							if($synbol==1){
								$single_price=($vd['Price']['Price']+$carr)*100+$value*2; // 单独购买
								$group_price=($vd['Price']['Price']+$carr)*100+$value; // 团购购买
							}else{
								$single_price=($vd['Price']['Price']+$carr)*100*$value*2; // 单独购买
								$group_price=($vd['Price']['Price']+$carr)*100*$value; // 团购购买
							}
							$sku_price['single_price']=$single_price;
							$sku_price['group_price']=$group_price;
							$newsku[]=$sku_price;
							break;
						}
					}
				}

				$skudata=array(
						'goods_id' => $v['goods_id'],
						'sku_price_list' => json_encode($newsku, JSON_UNESCAPED_UNICODE)
				);

				if($synbol==1){
					$update['onsale_price']=($pricearr['ItemPrice']['Price']['Price']+$carr)*100+$value*2;
					$diff='+'.$value;
				}else{
					$update['onsale_price']=($pricearr['ItemPrice']['Price']['Price']+$carr)*100*$value*2;
					$diff='*'.$value;
				}
				$update['updatetime']=date('Y-m-d H:i:s');
				$update['difference_price']=$diff;
				$result=$pddapi->NewsetData('pdd.goods.sku.price.update', $skudata);
				if(isset($result['goods_update_sku_price_response'])){
					$w=array(
							'goods_id' => $v['goods_id']
					);
					PddGoods::where($w)->update($update);
					$sun++;
				}else{
					$errn++;
				}
			}
			echo json_encode(array(
					'result' => 1,
					'message' => array(
							's' => $sun,
							'e' => $errn
					)
			), JSON_UNESCAPED_UNICODE);
		}catch(\Exception $e){
			echo json_encode(array(
					'result' => 0,
					'message' => $e->getLine().'行'.$e->getMessage()
			), JSON_UNESCAPED_UNICODE);
		}
		exit();
	}


	/**
	 * 后台请求获取到当前日期得最新订单
	 */
	public function getOrder(){
		try{
			$u=0; // 修改
			$inc=0; // 新增
			if(!isset($_POST['shop'])){
				return json(['message'=>'参数错误', 'result'=>2]);
			}else{
				$pddapi= new Pddapi();
				$shop=$pddapi->_init($_POST['shop']); // 初始化
				if($shop['result']==0){ 
					return json(['message'=>$shop['message'], 'result'=>2]);
				}else{
					$nt=time();
					$stime=strtotime("-1 month");
					for($i=$stime; $i<=$nt; $i+=86400){
						$num=100;
						for($one=1; $one<=$num; $one++){
							if($one==1){
								$rdata=array(
										'order_status' => 5,//5：全部
										'refund_status' => 5,//5：全部
										'start_confirm_at' => $i,
										'end_confirm_at' => $i+86400,
										'page' => $one
								);
								$result2=$pddapi->PddOrderListGet($rdata);
								if(isset($result2['result'])&&$result2['result']==0){ // 请求错误
									return json(['message'=>$result2['message'], 'result'=>2]);
								}
								$num=ceil($result2['total_count']/100);
							}
							if($result2['total_count']==0){
								continue;
							}
							$list=$result2['order_list'];
							$insert=array();
							foreach($list as $v1){
								$result1=PddOrder::where('order_sn',$v1['order_sn'])->order('confirm_time asc')->find(); // 查询出需要更新的订单
								if($result1){											
									if($v1['updated_at']!=$result1['updated_at']){ // 更新时间不一致，需要更新
										$update=array();
										$update['receiver_name']=$v1['receiver_name']; // 收件人
										(strpos($v1['receiver_phone'],"**") > 0)?'':$update['receiver_phone']=$v1['receiver_phone']; // 收件号码
										$update['country']=$v1['country']; // 国家
										$update['province']=$v1['province']; // 省份
										$update['city']=$v1['city']; // 城市
										$update['town']=$v1['town']; // 乡镇
										$update['address']=$v1['address']; // 详细地址
										$update['trade_type']=$v1['trade_type']; // 订单类型 0-普通订单 ，1- 定金订单
										$update['buyer_memo']=$v1['buyer_memo']; // 买家备注
										$update['remark']=$v1['remark']; // 商家订单备注
										$update['confirm_status']=$v1['confirm_status']; // 成交状态：0：未成交、1：已成交、2：已取消
										$update['order_status']=$v1['order_status']; // 订单状态
										$v1['created_time']!=''?$update['created_time']=$v1['created_time']:''; // 订单创建时间
										$v1['confirm_time']!=''?$update['confirm_time']=$v1['confirm_time']:''; // 成交时间
										$v1['receive_time']!=''?$update['receive_time']=$v1['receive_time']:''; // 收货时间
										$update['updated_at']=$v1['updated_at']; // 订单的更新时间
										$update['is_lucky_flag']=$v1['is_lucky_flag']; // 是否是抽奖订单，1-非抽奖订单，2-抽奖订单
										$update['goods_amount']=$v1['goods_amount']; // 商品金额，单位：元，商品金额=商品销售价格商品数量-改价金额（接口暂无该字段）
										$update['discount_amount']=$v1['discount_amount']; // 折扣金额，单位：元，折扣金额=平台优惠+商家优惠+团长免单优惠金额
										$update['pay_amount']=$v1['pay_amount']; // 支付金额，单位：元，支付金额=商品金额-折扣金额+邮费
										$update['postage']=$v1['postage']; // 邮费，单位：元
										$update['group_status']=$v1['group_status']; // 成团状态：0：拼团中、1：已成团、2：团失败
										$update['return_freight_payer']=$v1['return_freight_payer']; // tinyint(1) NOT NULL DEFAULT0' COMMENT退货包运费，1:是，0:否
										$update['capital_free_discount']=$v1['capital_free_discount']; // 团长免单金额，单位：元
										$update['seller_discount']=$v1['seller_discount']; // 商家优惠金额，单位：元
										$update['platform_discount']=$v1['platform_discount']; // 平台优惠金额，单位：元
										$update['pay_type']=$v1['pay_type']; // COMMENT支付方式
										$update['pay_no']=$v1['pay_no']; // COMMENT支付单号
										$v1['pay_time']!=''?$update['pay_time']=$v1['pay_time']:''; // 付款时间
										$v1['last_ship_time']!=''?$update['last_ship_time']=$v1['last_ship_time']:''; // 最晚发货时间
										$update['tracking_number']=$v1['tracking_number']; // COMMENT快递单号
										$update['logistics_id']=$v1['logistics_id']; // 快递公司在拼多多的代码
										$v1['shipping_time']!=''?$update['shipping_time']=$v1['shipping_time']:''; // 发货时间
										$update['refund_status']=$v1['refund_status']; // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
										$update['after_sales_status']=$v1['after_sales_status']; // 售后状态
										$update['id_card_name']=$v1['id_card_name']; // 身份证姓名
										$update['id_card_num']=$v1['id_card_num']; // 身份证号码
										if($v1['cat_id_4']!=0){
											$update['cat_id']=$v1['cat_id_4']; // 类目id
										}elseif($v1['cat_id_3']!=0){
											$update['cat_id']=$v1['cat_id_3']; // 类目id
										}
										$goods=array();
										foreach($v1['item_list'] as $v3){ // 商品数组
											$goods1=array();
											$goods1['goods_name']=$v3['goods_name']; // 商品名称
											$goods1['goods_price']=$v3['goods_price']; // 商品单件 单价：元
											$goods1['sku_id']=$v3['sku_id']; // 商品sku编码
											$goods1['goods_id']=$v3['goods_id']; // 商品编码
											$goods1['goods_count']=$v3['goods_count']; // 商品数量
											$goods[]=$goods1;
										}
										$update['goods']=json_encode($goods, JSON_UNESCAPED_UNICODE);
										if($v1['order_depot_info']){
											$update['order_depot_info']=json_encode($v1['order_depot_info'], JSON_UNESCAPED_UNICODE);
										}else{
											$update['order_depot_info']='';
										}
										$update['updatetime']=date('Y-m-d H:i:s');
										$w1=array(
												'order_sn' => $v1['order_sn'],
												'shop' => $_POST['shop']
										);
										PddOrder::where($w1)->update($update);
										$u++;
									}
								}else{
									$ins=array();
									$ins['receiver_name']=$v1['receiver_name']; // 收件人
									$ins['receiver_phone']=$v1['receiver_phone']; // 收件号码
									$ins['country']=$v1['country']; // 国家
									$ins['province']=$v1['province']; // 省份
									$ins['city']=$v1['city']; // 城市
									$ins['town']=$v1['town']; // 乡镇
									$ins['address']=$v1['address']; // 详细地址
									$ins['trade_type']=$v1['trade_type']; // 订单类型 0-普通订单 ，1- 定金订单
									$ins['buyer_memo']=$v1['buyer_memo']; // 买家备注
									$ins['remark']=$v1['remark']; // 商家订单备注
									$ins['confirm_status']=$v1['confirm_status']; // 成交状态：0：未成交、1：已成交、2：已取消
									$ins['order_status']=$v1['order_status']; // 订单状态
									$ins['created_time']=$v1['created_time']; // 订单创建时间
									$ins['confirm_time']=$v1['confirm_time']; // 成交时间
									$ins['receive_time']=$v1['receive_time']; // 收货时间
									$ins['updated_at']=$v1['updated_at']; // 订单的更新时间
									$ins['is_lucky_flag']=$v1['is_lucky_flag']; // 是否是抽奖订单，1-非抽奖订单，2-抽奖订单
									$ins['goods_amount']=$v1['goods_amount']; // 商品金额，单位：元，商品金额=商品销售价格商品数量-改价金额（接口暂无该字段）
									$ins['discount_amount']=$v1['discount_amount']; // 折扣金额，单位：元，折扣金额=平台优惠+商家优惠+团长免单优惠金额
									$ins['pay_amount']=$v1['pay_amount']; // 支付金额，单位：元，支付金额=商品金额-折扣金额+邮费
									$ins['postage']=$v1['postage']; // 邮费，单位：元
									$ins['group_status']=$v1['group_status']; // 成团状态：0：拼团中、1：已成团、2：团失败
									$ins['return_freight_payer']=$v1['return_freight_payer']; // tinyint(1) NOT NULL DEFAULT0' COMMENT退货包运费，1:是，0:否
									$ins['capital_free_discount']=$v1['capital_free_discount']; // 团长免单金额，单位：元
									$ins['seller_discount']=$v1['seller_discount']; // 商家优惠金额，单位：元
									$ins['platform_discount']=$v1['platform_discount']; // 平台优惠金额，单位：元
									$ins['pay_type']=$v1['pay_type']; // COMMENT支付方式
									$ins['pay_no']=$v1['pay_no']; // COMMENT支付单号
									$ins['pay_time']=$v1['pay_time']; // 付款时间
									$ins['last_ship_time']=$v1['last_ship_time']; // 最晚发货时间
									$ins['tracking_number']=$v1['tracking_number']; // COMMENT快递单号
									$ins['logistics_id']=$v1['logistics_id']; // 快递公司在拼多多的代码
									$ins['shipping_time']=$v1['shipping_time']; // 发货时间
									$ins['refund_status']=$v1['refund_status']; // 售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部
									$ins['after_sales_status']=$v1['after_sales_status']; // 售后状态
									$ins['id_card_name']=$v1['id_card_name']; // 身份证姓名
									$ins['id_card_num']=$v1['id_card_num']; // 身份证号码
									if($v1['cat_id_4']!=0){
										$ins['cat_id']=$v1['cat_id_4']; // 类目id
									}elseif($v1['cat_id_3']!=0){
										$ins['cat_id']=$v1['cat_id_3']; // 类目id
									}
									$goods=array();
									foreach($v1['item_list'] as $v3){ // 商品数组
										$goods1=array();
										$goods1['goods_name']=$v3['goods_name']; // 商品名称
										$goods1['goods_price']=$v3['goods_price']; // 商品单件 单价：元
										$goods1['sku_id']=$v3['sku_id']; // 商品sku编码
										$goods1['goods_id']=$v3['goods_id']; // 商品编码
										$goods1['goods_count']=$v3['goods_count']; // 商品数量
										$goods[]=$goods1;
									}
									$ins['goods']=json_encode($goods, JSON_UNESCAPED_UNICODE);
									if($v1['order_depot_info']){
										$ins['order_depot_info']=json_encode($v1['order_depot_info'], JSON_UNESCAPED_UNICODE);
									}else{
										$ins['order_depot_info']='';
									}
									$ins['updatetime']=date('Y-m-d H:i:s');
									$ins['order_sn']=$v1['order_sn'];
									$ins['ctime']=date('Y-m-d H:i:s');
									$ins['updatetime']=date('Y-m-d H:i:s');
									$ins['shop']=$_POST['shop'];
									$insert[]=$ins;
									$inc++;
								}
							}
							PddOrder::insertAll($insert);
						}
					}
				}
			}
			return json(['inc'=>$inc,'u'=>$u, 'result'=>1]);
		}catch(\Exception $e){
			return json(['message'=>$e->getMessage(), 'result'=>2]);
		}
	}

}


