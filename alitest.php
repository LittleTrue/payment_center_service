<?php
/*
 * @Description:
 * @Version:
 * @Author: Yan
 * @Date: 2020-11-09 10:37:39
 * @LastEditors: Yan
 * @LastEditTime: 2020-11-18 14:23:58
 */

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\AliPayGlobalService;

//主体参数
$config = [
    'partner_id' => '2088621891278220',
    // 'partner_id' => '2088931955294438',
    'key' => 'x017pu0k7eunv1azw69w5tyoafr36w46',
    // 'key'   => '1gq2qomq76qk5su0rkhuvc4508apbp8o',
];

$ioc_con_app = new Application($config);

$aliPayService = new AliPayGlobalService($ioc_con_app);

//扫码支付测试
$info = [
    'subject'      => 'shopping',
    'body'         => '测试商品【演示用】测试规格',
    'out_trade_no' => '20201116009029201',
    'currency'     => 'USD',
    'total_fee'    => '',
    'rmb_fee'      => 0.08,
    'refer_url'    => 'http://www.baidu.com',
    // 'product_code' => 'NEW_WAP_OVERSEAS_SELLER',
    'product_code'      => 'NEW_OVERSEAS_SELLER',
    'notify_url'        => 'http://www.baidu.com',
    'return_url'        => 'http://www.baidu.com',
    'trade_information' => [
        'business_type'  => '4',
        'goods_info'     => '测试商品【演示用】测试规格^1',
        'total_quantity' => 1,
    ],
];

$tmp = $aliPayService->qrCodePay($info);
var_dump($tmp);
die();

// //报关测试
// $info = [
//     'out_request_no' => '123312',
//     'trade_no' => 'xxx',
//     'merchant_customs_code' => 'xxx',
//     'amount' => 100,
//     'customs_place' => 'GUANGZHOU',
//     'merchant_customs_name' => 'jwyhanguo_card',
//     'buyer_name' => '小明',//非必填
//     'buyer_id_no' => '330681199010104783', //非比填
// ];

// $tmp = $aliPayService->orderCustoms($info);
// var_dump($tmp);
// die();

// //退款测试
// $info = [
//     'out_return_no'     => '123123',
//     'out_trade_no'      => '123123',
//     'return_amount'     => 0,
//     'return_rmb_amount' => 0.8,
//     'currency'          => 'USD',
//     'gmt_return'        => date('Y-m-d H:i:s'),
//     'product_code'      => 'NEW_OVERSEAS_SELLER',
//     'notify_url'        => 'http://xxx',
// ];

// $tmp = $aliPayService->orderRefund($info);
// var_dump($tmp);
// die();

//支付单查询测试
// $info = [
//     'trade_no' => '202011098899155', //交易流水号
//     'out_trade_no' => '202011098899155', //订单号
// ];

// $tmp = $aliPayService->orderQuery($info);
// var_dump($tmp);
// die();

// //报关查询测试
// $info = [
//     'out_request_nos' => '201506010001,201506010002,201506010003', //多个用英文逗号隔开
// ];
// $tmp = $aliPayService->orderCustomsQuery($info);
// var_dump($tmp);
// die();

//退款查询测试
// $info = [
//     'out_trade_no'  => '111',
//     'out_return_no' => '111',
// ];
// $tmp = $aliPayService->refundQuery($info);
// var_dump($tmp);
// die();

//加密方法测试
// $param = [
//     'out_return_no'     => '',
//     'out_trade_no'      => '',
//     'return_amount'     => '',
//     'return_rmb_amount' => '',
//     'currency'          => '',
//     'gmt_return'        => date('Y-m-d H:i:s'),
//     'product_code'      => '',
//     'notify_url'        => '',
// ];
// var_dump($aliPayService->getMd5Sign($param));
