<?php
/*
 * @Description: 
 * @Version: 
 * @Author: Yan
 * @Date: 2020-11-09 10:37:39
 * @LastEditors: Yan
 * @LastEditTime: 2020-11-09 14:18:13
 */

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\AliPayGlobalService;

//主体参数
$config = [
    'partner_id' => '2088431658457999',
    'key'   => 'o8dry07svnqwbiulg6bnnbav1fl12zji',
];


$ioc_con_app = new Application($config);

$aliPayService   = new AliPayGlobalService($ioc_con_app);

//业务参数
$info = [
    'subject' => 'kids clothing',
    'body' => 'goods',
    'out_trade_no' => '202011098899',
    'currency' => 'RMB',
    'total_fee' => 166,
    'rmb_fee' => 166,
    'refer_url' =>  'http://xxx',
    'product_code' => 'OP330090',
    'trade_information' => [
        'business_type' => 4,
        'goods_info' => 'pencil^2|eraser^5|iPhone XS 256G^1',
        'total_quantity' => 2
    ]
];



$tmp = $aliPayService->qrCodePay($info);
var_dump($tmp);
die();