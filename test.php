<?php

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\WeChatPayGlobalService;

//主体参数
$config = [
    'wx_appid' => 'wx96400c2dd16445c2',
    'wx_key'   => 'yZ1hTuPp1pphAnlUzUFcx6YekShpStm8',
    'wx_mchid'   => '1341112801'
];


$ioc_con_app = new Application($config);

$wechatService   = new WeChatPayGlobalService($ioc_con_app);

//业务参数
$info = [
    'body' => 'test',
    'order_no' => '23131',
    'order_fee' => '23131',
    'notify_url' => 'https://www.thinbug.com/q/34726530',
];



$tmp = $wechatService->qrCodePay($info);
var_dump($tmp);
die();