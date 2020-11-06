<?php

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\WeChatPayService;

//主体参数
$config = [
    'base_uri' => 'http://203.195.149.21:8080/x2oms/x2wms/api/index.do',
    'secret'   => '123456',
];

$ioc_con_app = new Application($config);

$wechatService   = new WeChatPayService($ioc_con_app);

//业务参数
$info = [
    'customerCode' => 'TT',
    'sitecode'     => 'TEST01',
];


$tmp = $wechatService->qrCodePay($info);
var_dump($tmp);
die();