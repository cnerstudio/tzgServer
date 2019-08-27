/*
 Navicat Premium Data Transfer

 Source Server         : 信儿com_tzg
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : cnerstudio.com:3306
 Source Schema         : qdm166355542_db

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 12/03/2019 23:27:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tzg_carriagetemplate
-- ----------------------------
CREATE TABLE `tzg_carriagetemplate`  (
  `J_ID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `J_IDs` int(2) UNSIGNED ZEROFILL NOT NULL DEFAULT 00 COMMENT '子ID',
  `J_Title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `J_LastModified` datetime NOT NULL,
  `J_From` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `J_Check` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `J_CalcRule` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_express` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_area` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_starting` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_postage` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_plus` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `col_postageplus` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `isdel` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除，0 未删除，1 已删除',
  PRIMARY KEY (`J_ID`, `J_IDs`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_counter
-- ----------------------------
CREATE TABLE `tzg_counter`  (
  `largeclass` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '大类',
  `smallclass` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小类',
  `goodsID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品编号',
  `goodsname` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `price` double NULL DEFAULT NULL COMMENT '售价',
  `cost` double NULL DEFAULT NULL COMMENT '成本',
  `profit` double NULL DEFAULT NULL COMMENT '利润',
  `cityP` double NULL DEFAULT NULL COMMENT '同城价',
  `cityS` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '同城店',
  `cityC` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '同城链',
  `retime` datetime NULL DEFAULT NULL COMMENT '更新',
  `stock` int(11) NULL DEFAULT NULL COMMENT '库存',
  `sold` int(11) NULL DEFAULT NULL COMMENT '已售',
  `avep` double NULL DEFAULT NULL COMMENT '成交均价',
  `inter` int(11) NULL DEFAULT NULL COMMENT '内编',
  `numR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '数比',
  `recommend` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '推荐',
  `freeS` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '包邮',
  `clearH` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '清仓',
  `order` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订购',
  PRIMARY KEY (`goodsID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_dispurchase
-- ----------------------------
CREATE TABLE `tzg_dispurchase`  (
  `ID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易ID',
  `purchaseID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '采购订单商品ID',
  `disnum` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分销流水号',
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `buyerN` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家昵称',
  `buyerID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家ID',
  `mktime` datetime NOT NULL COMMENT '成交时间',
  `paymethod` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '付款方式',
  `goodsname` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `color` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '颜色分类',
  `busID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商家编码',
  `price` float NOT NULL COMMENT '售价',
  `purP` float NOT NULL COMMENT '采购价',
  `num` int(11) NOT NULL COMMENT '数量',
  `supplier` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商',
  `supplierID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商ID',
  `cProfit` float NOT NULL COMMENT '总利润',
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '状态',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_distribution
-- ----------------------------
CREATE TABLE `tzg_distribution`  (
  `ID` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商品ID',
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `suppliername` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商',
  `supplierID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '供应商ID',
  `goodsname` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `goodsID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品ID',
  `mcode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商家编码',
  `server` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '保障服务',
  `pprice` float NOT NULL COMMENT '采购价',
  `profitmin` float NOT NULL COMMENT '最低利润',
  `profitmax` float NOT NULL COMMENT '最高利润',
  `price` float NOT NULL COMMENT '售价',
  `profit` float NOT NULL COMMENT '利润',
  `temp` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '运费模版',
  `stock` int(11) NOT NULL COMMENT '库存',
  `rtime` datetime NOT NULL COMMENT '采购价更新时间',
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '未关联' COMMENT '状态:未上架,已上架,未关联,已删除',
  PRIMARY KEY (`ID`, `goodsID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_disupplier
-- ----------------------------
CREATE TABLE `tzg_disupplier`  (
  `suppliID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商ID',
  `suppliname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商名称',
  `location` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所在地',
  `pcategory` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '产品大类',
  `mcategory` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主营类目',
  `brand` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '品牌',
  `btype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商家类型',
  `payee` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款人',
  `cway` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系方式',
  `alipay` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝',
  `upway` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上架方式',
  `csales` float NOT NULL COMMENT '累计销售',
  `salesv` int(11) NOT NULL COMMENT '累计销量',
  `aprofit` float NOT NULL COMMENT '累计利润',
  `cprice` float NOT NULL COMMENT '平均售价',
  `cprofit` float NOT NULL COMMENT '平均利润',
  `ctime` datetime NOT NULL COMMENT '开始日期',
  `mcsales` float NOT NULL COMMENT '月均销量',
  `mcprofit` float NOT NULL COMMENT '月均利润',
  `rtime` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`suppliID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_goods_detail
-- ----------------------------
CREATE TABLE `tzg_goods_detail`  (
  `itemId` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品ID',
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品名',
  `catId` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类ID',
  `mainCatId` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主分类ID',
  `catPath` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类目路径',
  `property` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '商品属性',
  `sku` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `skuProp` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'sku属性',
  `multiMedia` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '主图',
  `price` float NULL DEFAULT NULL COMMENT '价格',
  `quantity` int(11) NULL DEFAULT NULL COMMENT '总库存',
  `descForPC` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'PC端描述',
  `location` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `deliverTemplate` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '运费模版ID',
  `updatetime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态:已上架,仓库中',
  `jsondetail` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`itemId`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_inventory
-- ----------------------------
CREATE TABLE `tzg_inventory`  (
  `inter` int(11) NOT NULL COMMENT '内编',
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类型',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `speci` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '规格',
  `cost` double NOT NULL COMMENT '成本',
  `countN` int(11) NOT NULL COMMENT '总量',
  `selfU` int(11) NOT NULL COMMENT '自用',
  `allowance` int(11) NOT NULL COMMENT '余量',
  `cdate` datetime NOT NULL COMMENT '校验日期',
  `bBus` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '最优商家',
  `bPrice` double NULL DEFAULT NULL COMMENT '最优进价',
  `purpose` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用途',
  `enca` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '进柜',
  PRIMARY KEY (`inter`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_listbought
-- ----------------------------
CREATE TABLE `tzg_listbought`  (
  `channel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '渠道',
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易ID',
  `orderID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号',
  `ctime` datetime NOT NULL COMMENT '成交时间',
  `buyerN` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卖家昵称',
  `buyerID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卖家ID',
  `babyDetails` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '宝贝详情',
  `babyname` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '宝贝名称',
  `price` double NOT NULL COMMENT '标价',
  `num` int(11) NOT NULL COMMENT '数量',
  `freight` double NOT NULL COMMENT '运费',
  `freightstatus` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '运费状态',
  `trstatus` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易状态',
  `orderDetails` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单详情',
  `inter` int(11) NULL DEFAULT NULL COMMENT '内编',
  `numR` double NOT NULL COMMENT '数比',
  `warehous` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '入库',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_listsold
-- ----------------------------
CREATE TABLE `tzg_listsold`  (
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易ID',
  `orderID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号',
  `ctime` datetime NOT NULL COMMENT '成交时间',
  `buyerN` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家昵称',
  `buyerID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家ID',
  `goodsname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '宝贝名称',
  `busID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商家编码',
  `price` double NOT NULL COMMENT '标价',
  `num` int(11) NOT NULL COMMENT '数量',
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易状态',
  `orderDetails` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单详情',
  `warehous` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '入库',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_log_action
-- ----------------------------
CREATE TABLE `tzg_log_action`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `actioncode` int(4) UNSIGNED ZEROFILL NULL DEFAULT NULL COMMENT '操作编号',
  `content` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作内容',
  `from` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作主机',
  `duration` float(10, 2) NULL DEFAULT NULL COMMENT '操作周期(min)',
  `num` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '操作条数',
  `version` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作编号版本',
  `time` datetime NULL DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2345 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_log_goods
-- ----------------------------
CREATE TABLE `tzg_log_goods`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `goodcnt` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '商品总数',
  `increment` int(11) NOT NULL COMMENT '当日增量',
  `category` tinyint(1) NOT NULL COMMENT '商品类型：0-淘宝，1-分销，2-拼多多',
  `state` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品状态',
  `time` date NOT NULL COMMENT '记录日期',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1923 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_log_pddgoods
-- ----------------------------
CREATE TABLE `tzg_log_pddgoods`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `goodcnt` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '上架总数',
  `increment` int(11) NOT NULL COMMENT '当日增量',
  `pricerange` tinyint(1) NOT NULL COMMENT '分布区间：0-\'10-\', 1-\'10~20\', 2-\'20~35\', 3-\'35~60\', 4-\'60~100\', 5-\'100+\'',
  `time` date NOT NULL COMMENT '记录日期',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1907 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_log_supply
-- ----------------------------
CREATE TABLE `tzg_log_supply`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplycnt` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '供应商数',
  `increment` int(11) NOT NULL DEFAULT 0 COMMENT '当日增量',
  `category` tinyint(1) NOT NULL COMMENT '分销平台：0-天猫，1-1688，2-京东',
  `state` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商状态',
  `time` date NOT NULL COMMENT '记录日期',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 125 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_myapply
-- ----------------------------
CREATE TABLE `tzg_myapply`  (
  `userid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `supplierid` int(11) UNSIGNED NOT NULL COMMENT '供应商ID',
  `applytime` datetime NULL DEFAULT NULL COMMENT '拒绝时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 未知/不可申请,1可申请,2 申请中,3 合作中,4 已拒绝,5 已过期 ',
  `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 默认, 1 风险供应, 2 交易违约, 3 超额供应, 6 独享供应, 101 品牌供应, 111 代理供应, 112 分店供应, 113 虚假发货, 161 品牌独享',
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`, `supplierid`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_myapplycate
-- ----------------------------
CREATE TABLE `tzg_myapplycate`  (
  `userid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `cateid` int(11) UNSIGNED NOT NULL COMMENT '供应类目id',
  `isapply` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 未启用，1启用',
  `updatetime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userid`, `cateid`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_mycate
-- ----------------------------
CREATE TABLE `tzg_mycate`  (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '供应类目id',
  `catename` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '供应类目id',
  `isshow` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 不显示;1 显示',
  `isTop` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 未设置;1 置顶;-1 置底',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 201131102 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_mysupplier
-- ----------------------------
CREATE TABLE `tzg_mysupplier`  (
  `id` int(200) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '供应商ID',
  `name` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '供应商名字',
  `province` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所在省',
  `city` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '所在市',
  `searchid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cateid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '类目id',
  `rstatus` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '招募状态',
  `guarantee` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '保障',
  `mcate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品类别',
  `brand` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '品牌',
  `btype` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商家类型',
  `productcount` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品总数',
  `priceavg` float NOT NULL DEFAULT 0 COMMENT '平均价',
  `priceamax` float NOT NULL DEFAULT 0 COMMENT '正序最高价',
  `priceamin` float NOT NULL DEFAULT 0 COMMENT '正序最低价',
  `priceaavg` float NOT NULL DEFAULT 0 COMMENT '正序平均价',
  `pricedmax` float NOT NULL DEFAULT 0 COMMENT '倒序最高价',
  `pricedmin` float NOT NULL DEFAULT 0 COMMENT '倒序最低价',
  `pricedavg` float NOT NULL DEFAULT 0 COMMENT '倒序平均价',
  `isdel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 未删除,1 已删除',
  `ctime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `rtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `编码`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27785053 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_mysupplierdetail
-- ----------------------------
CREATE TABLE `tzg_mysupplierdetail`  (
  `id` int(200) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '供应商ID',
  `payname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收款人',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系会员名',
  `contacts` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系人',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '电话号码',
  `mobilephone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'email',
  `alipay` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝帐号',
  `website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '供应商网站',
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `编码`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27785053 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_ordertracking
-- ----------------------------
CREATE TABLE `tzg_ordertracking`  (
  `orderID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `buyerN` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家昵称',
  `proID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '项目编号',
  `prodesc` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单简述',
  `profit` double NOT NULL COMMENT '利润',
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '状态',
  `payN` double NOT NULL COMMENT '支付金额',
  `paytime` datetime NOT NULL COMMENT '付款时间',
  `cost` double NOT NULL COMMENT '成本',
  `remark` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `express` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '快递物流',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地址',
  `contace` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系方式',
  PRIMARY KEY (`orderID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_pdd_cate
-- ----------------------------
CREATE TABLE `tzg_pdd_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL DEFAULT 0 COMMENT '类目id',
  `cat_name` varchar(30) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL,
  `parent_id` int(11) NOT NULL COMMENT '父id',
  `level` tinyint(4) NOT NULL COMMENT '级别',
  `spec` mediumtext CHARACTER SET gbk COLLATE gbk_chinese_ci NULL COMMENT '规格数组',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 12162 CHARACTER SET = gbk COLLATE = gbk_chinese_ci;

-- ----------------------------
-- Table structure for tzg_pdd_config
-- ----------------------------
CREATE TABLE `tzg_pdd_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'owner_id等同授权店铺id',
  `mall_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '店铺名字',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'pdd 授权code',
  `access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼多多授权码',
  `access_token_time` datetime NULL DEFAULT NULL COMMENT '授权码有效期',
  `refresh_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼多多授权刷新码',
  `refresh_token_time` datetime NULL DEFAULT NULL COMMENT '刷新码有效期',
  `owner_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '等同授权账户',
  `cost_template_id` int(11) NOT NULL DEFAULT 0 COMMENT '默认运费模板',
  `ctime` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1547463929 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_pdd_goods
-- ----------------------------
CREATE TABLE `tzg_pdd_goods`  (
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拼多多商品编码',
  `shopID` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '店铺id',
  `goods_sn` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'goods_commit_id(草稿id)',
  `goods_name` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `goods_quantity` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '总库存',
  `onsale_price` int(11) NOT NULL DEFAULT 0 COMMENT '售价，单位为分(2倍差价)',
  `market_price` int(11) NOT NULL DEFAULT 0 COMMENT '市场价格，单位为分(3倍差价)',
  `difference_price` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '成本差价，单位为分',
  `reserve_price` decimal(10, 2) NULL DEFAULT NULL COMMENT 'TB商品的一口价，单位为元',
  `cat_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '叶子类目ID',
  `cost_template_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '运费模版id',
  `is_onsale` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否在架上，0-未上架 , 1-上架，2-下架，3-售罄 , 4-已删除 ,  44-彻底删除 , 7-草稿中 , 14-回收站 , 101-待审核 , 103-审核驳回',
  `itemId` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'goods表id',
  `sku_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'sku信息',
  `price_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '	价格信息',
  `item_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '商品信息',
  `stock_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '库存信息',
  `mobile_desc_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '移动描述信息',
  `delivery_info` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '物流信息',
  `errormsg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '错误信息',
  `ctime` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `jsondata` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '上架json数据',
  PRIMARY KEY (`goods_id`, `shopID`) USING BTREE,
  INDEX `itemId`(`itemId`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_pdd_order
-- ----------------------------
CREATE TABLE `tzg_pdd_order`  (
  `order_sn` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单编号',
  `receiver_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `receiver_phone` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '收件号码',
  `country` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '城市',
  `town` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '乡镇',
  `address` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `goods` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品json数组',
  `trade_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '订单类型 0-普通订单 ，1- 定金订单',
  `buyer_memo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '买家备注',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商家订单备注',
  `confirm_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '成交状态：0：未成交、1：已成交、2：已取消',
  `order_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '订单状态',
  `created_time` datetime NULL DEFAULT NULL COMMENT '订单创建时间',
  `confirm_time` datetime NULL DEFAULT NULL COMMENT '成交时间',
  `receive_time` datetime NULL DEFAULT NULL COMMENT '收货时间',
  `updated_at` datetime NULL DEFAULT NULL COMMENT '订单的更新时间',
  `is_lucky_flag` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是抽奖订单，1-非抽奖订单，2-抽奖订单',
  `goods_amount` decimal(11, 2) NOT NULL COMMENT '商品金额，单位：元，商品金额=商品销售价格*商品数量-改价金额（接口暂无该字段）',
  `discount_amount` decimal(11, 2) NOT NULL COMMENT '折扣金额，单位：元，折扣金额=平台优惠+商家优惠+团长免单优惠金额',
  `pay_amount` decimal(11, 2) NOT NULL COMMENT '支付金额，单位：元，支付金额=商品金额-折扣金额+邮费',
  `postage` decimal(11, 2) NOT NULL COMMENT '\r\n\r\n邮费，单位：元',
  `group_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '成团状态：0：拼团中、1：已成团、2：团失败',
  `return_freight_payer` tinyint(1) NOT NULL DEFAULT 0 COMMENT '退货包运费，1:是，0:否',
  `capital_free_discount` decimal(11, 2) NOT NULL COMMENT '团长免单金额，单位：元',
  `seller_discount` decimal(11, 2) NOT NULL COMMENT '商家优惠金额，单位：元',
  `platform_discount` decimal(11, 2) NOT NULL COMMENT '平台优惠金额，单位：元',
  `pay_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_no` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付单号',
  `pay_time` datetime NULL DEFAULT NULL COMMENT '付款时间',
  `last_ship_time` datetime NULL DEFAULT NULL COMMENT '最晚发货时间',
  `tracking_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递单号',
  `logistics_id` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递公司在拼多多的代码',
  `shipping_time` datetime NULL DEFAULT NULL COMMENT '发货时间',
  `refund_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '售后状态 1：无售后或售后关闭，2：售后处理中，3：退款中，4： 退款成功 5：全部',
  `after_sales_status` tinyint(255) NULL DEFAULT 0 COMMENT '售后状态 0：无售后 2：买家申请退款，待商家处理 3：退货退款，待商家处理 4：商家同意退款，退款中 5：平台同意退款，退款中 6：驳回退款， 待买家处理 7：已同意退货退款,待用户发货 8：平台处理中 9：平台拒 绝退款，退款关闭 10：退款成功 11：买家撤销 12：买家逾期未处 理，退款失败 13：买家逾期，超过有效期 14 : 换货补寄待商家处理 15:换货补寄待用户处理 16:换货补寄成功 17:换货补寄失败 18:换货补寄待用户确认完成',
  `cat_id` int(11) NOT NULL DEFAULT 0 COMMENT '叶子类目id',
  `id_card_name` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证姓名',
  `id_card_num` varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证号码',
  `order_depot_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '仓库信息json数组',
  `ctime` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `shop` int(11) NOT NULL COMMENT '店铺id',
  PRIMARY KEY (`order_sn`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_pdd_outsold
-- ----------------------------
CREATE TABLE `tzg_pdd_outsold`  (
  `goods_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拼多多商品编码',
  `shopID` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '店铺id',
  `goods_name` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品名称',
  `out_sold_count_month` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第三方月销量',
  `out_sold_count_total` int(11) NOT NULL DEFAULT 0 COMMENT '第三方总销量',
  `out_mall_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '第三方店铺名称：\"信儿众创&2944652975\"',
  `out_source_type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '淘宝/天猫 0，京东1，1688 2，唯品会3，苏宁4，亚马逊,5，网易6，其他7',
  `out_goods_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'TB的itemId',
  `serial_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '申请标号',
  `audit_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态',
  `ctime` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `updatetime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`goods_id`, `shopID`) USING BTREE,
  INDEX `itemId`(`out_goods_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_pdd_prop
-- ----------------------------
CREATE TABLE `tzg_pdd_prop`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `pname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '拼多多名字',
  `tname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '淘宝名字',
  `cate` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类别：1品牌',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pname`(`pname`, `tname`, `cate`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1766 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_punish
-- ----------------------------
CREATE TABLE `tzg_punish`  (
  `irregID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '违规编号',
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `irregcate` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '违规类别',
  `goodsID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品ID',
  `suppID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '供应ID',
  `irregtime` datetime NOT NULL COMMENT '违规时间',
  `irregcase` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '违规案例',
  `irregreason` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '违规原因',
  `remind` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '小二提醒',
  `management` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '处置方式',
  PRIMARY KEY (`irregID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_taobao_area
-- ----------------------------
CREATE TABLE `tzg_taobao_area`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标准行政区域代码.参考:http://www.stats.gov.cn/tjbz/xzqhdm/t20120105_402777427.htm',
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(11) NULL DEFAULT 0 COMMENT '父节点区域标识.如北京市的area_id是110100,朝阳区是北京市的一个区,所以朝阳区的parent_id就是北京市的area_id.',
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '区域类型.area区域 1:country/国家;2:province/省/自治区/直辖市;3:city/地区(省下面的地级市);4:district/县/市(县级市)/区;abroad:海外.',
  `zip` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '邮编',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 990101 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'taobao地区表\r\nhttp://open.taobao.com/apitools/apiTools.htm?catId=7&apiId=59&apiName=taobao.areas.get';

-- ----------------------------
-- Table structure for tzg_taobao_cat
-- ----------------------------
CREATE TABLE `tzg_taobao_cat`  (
  `id` int(11) NOT NULL COMMENT '分类cid',
  `catename` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类别名称',
  `is_parent` int(1) NOT NULL DEFAULT 0 COMMENT '是否有子类0否1是',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级类别',
  `level` smallint(1) NOT NULL DEFAULT 0,
  `pathid` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类别缩略图',
  `path` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '种类背景图',
  `isshow` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 不显示;1 显示',
  `isTop` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 未设置;1 置顶;-1 置底',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cat_id`(`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  INDEX `level`(`level`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_taobao_tae_items
-- ----------------------------
CREATE TABLE `tzg_taobao_tae_items`  (
  `itemId` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品ID',
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品标题',
  `reserve_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '商品的一口价',
  `num` int(11) NULL DEFAULT NULL COMMENT '库存数量',
  `pic_url` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主图链接',
  `cid` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品叶子类目',
  `catename` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类别名称',
  `open_iid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '商品混淆ID',
  `istk` tinyint(1) NULL DEFAULT 0 COMMENT '是否淘客商品,0 否，1 是',
  `tk_rate` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '淘客佣金比例，比如：750 表示 7.50%',
  `desc_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '描述信息',
  `sku_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'sku信息',
  `price_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '	价格信息',
  `item_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '商品信息',
  `stock_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '库存信息',
  `mobile_desc_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '移动描述信息',
  `seller_info` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '卖家信息',
  `delivery_info` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '物流信息',
  `store_info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '门店信息',
  `item_buy_info` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '购买约束信息',
  `coupon_info` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '优惠卷信息',
  `updatetime` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态(同tzg_distribution):已上架,仓库中',
  PRIMARY KEY (`itemId`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_uinfo
-- ----------------------------
CREATE TABLE `tzg_uinfo`  (
  `ID` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `picurl` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像地址',
  `Category` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bought` datetime NULL DEFAULT NULL COMMENT '已买导入',
  `sold` datetime NULL DEFAULT NULL COMMENT '已卖导入',
  `location` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所在地',
  `locationCode` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所在地编码',
  `freightTemp` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主要运费模板',
  `tempID` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主要模板ID',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci;

-- ----------------------------
-- Table structure for tzg_user
-- ----------------------------
CREATE TABLE `tzg_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `upass` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `tel` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `state` tinyint(4) NOT NULL DEFAULT 1 COMMENT '用户状态',
  `ctime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `stime` int(11) NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uname`(`uname`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
