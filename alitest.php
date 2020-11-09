<?php
/*
 * @Description: 
 * @Version: 
 * @Author: Yan
 * @Date: 2020-11-09 10:37:39
 * @LastEditors: Yan
 * @LastEditTime: 2020-11-09 10:37:40
 */

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\AliPayGlobalService;

//主体参数
$config = [
    'partner_id' => 'xxx',
    'key'   => 'xxx',
];


$ioc_con_app = new Application($config);

$aliPayService   = new AliPayGlobalService($ioc_con_app);

//业务参数
$info = [
    'body' => 'test',
    'order_no' => '23131',
    'order_fee' => '23131',
    'notify_url' => 'https://www.thinbug.com/q/34726530',
];



$tmp = $aliPayService->qrCodePay($info);
var_dump($tmp);
die();