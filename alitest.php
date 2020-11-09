<?php
/*
 * @Description: 
 * @Version: 
 * @Author: Yan
 * @Date: 2020-11-09 10:37:39
 * @LastEditors: Yan
 * @LastEditTime: 2020-11-09 16:15:02
 */

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\AliPayGlobalService;

//主体参数
$config = [
    'partner_id' => '123321',
    'key'   => 'o8dry07svnqwbiulg6bnnbav1fl12zji',
];


$ioc_con_app = new Application($config);

$aliPayService   = new AliPayGlobalService($ioc_con_app);

// //扫码支付测试
// $info = [
//     'subject' => 'kids clothing',
//     'body' => 'goods',
//     'out_trade_no' => '202011098899',
//     'currency' => 'RMB',
//     'total_fee' => 166,
//     'rmb_fee' => 166,
//     'refer_url' =>  'http://xxx',
//     'product_code' => 'OP330090',
//     'trade_information' => [
//         'business_type' => 4,
//         'goods_info' => 'pencil^2|eraser^5|iPhone XS 256G^1',
//         'total_quantity' => 2
//     ]
// ];

// $tmp = $aliPayService->qrCodePay($info);
// var_dump($tmp);
// die();

//报关测试
$info = [
    'out_request_no' => '123312',
    'trade_no' => 'xxx',
    'merchant_customs_code' => 'xxx',
    'amount' => 100,
    'customs_place' => 'GUANGZHOU',
    'merchant_customs_name' => 'jwyhanguo_card',
    'buyer_name' => '小明',//非必填
    'buyer_id_no' => '330681199010104783', //非比填
];

$tmp = $aliPayService->orderCustoms($info);
var_dump($tmp);
die();