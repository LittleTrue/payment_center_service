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
    'customerCode' => 'TT',
    'sitecode'     => 'TEST01',
];



$tmp = $wechatService->qrCodePay($info);
var_dump($tmp);
die();