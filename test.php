<?php

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\WeChatPayGlobalService;

//主体参数
$config = [
    'wx_appid' => 'wxd6245dfb69943281',
    'wx_key'   => '9bb369aab99f0523e69190231282d237',
    'wx_mchid' => '1510197911',

    'custom_no' => '4601640011',
    'custom'    => 'GUANGZHOU_ZS',

    'wx_apiclient_key'  => 'G:/wamp64/www/apiclient_key.pem',
    'wx_apiclient_cert' => 'G:/wamp64/www/apiclient_cert.pem',
];

$ioc_con_app = new Application($config);

$wechatService = new WeChatPayGlobalService($ioc_con_app);

//业务参数
// $info = [
//     'body' => 'test',
//     'order_no' => '10000001',
//     'order_fee' => '1',
//     'notify_url' => 'https://www.thinbug.com/q/34726530',
// ];

// $tmp = $wechatService->qrCodePay($info);
// var_dump($tmp);
// die();

//订单查询
// $info = [
//     'order_no' => '10000001',
// ];

// try {
//     $tmp = $wechatService->orderQuery($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());
// }
// echo json_encode($tmp);
// die();

//退款
// $info = [
//     'order_no'          => '10000001',
//     'refund_no'         => 'R10000001',
//     'total_fee'         => '1',
//     'refund_fee'        => '1',
//     'refund_fee_type'   => '',
//     'refund_desc'       => '123',
// ];

// try {
//     $tmp = $wechatService->orderRefund($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// echo json_encode($tmp);
// die();

//退款查询
// $info = [
//     'order_no'          => '10000001',
//     'refund_no'         => 'R10000001',
//     'total_fee'         => '1',
//     'refund_fee'        => '1',
//     'refund_fee_type'   => '',
//     'refund_desc'       => '123',
// ];

// try {
//     $tmp = $wechatService->orderRefundQuery($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// echo json_encode($tmp);
// die();

//支付单报关
// $info = [
//     'EntOrderNo'   => '10000001',
//     'EntPayNo' => '4200000738202011092400680961',
// ];

// try {
//     $tmp = $wechatService->orderCustoms($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// var_dump($tmp);die();
// die();

//查询报关
// $info = [
//     'order_no'   => '10000001',
//     'order_type' => 'out_trade_no', //out_trade_no-商家订单号 transaction_id-微信支付订单号 sub_order_no-商家子订单号 sub_order_id-微信子订单号
// ];

// try {
    // $tmp = $wechatService->orderCustomsQuery($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// var_dump($tmp);die();
// die();

//身份验证
$info = [
    'EntOrderNo'     => '10000001',
    'EntPayNo'       => '4200000738202011092400680961',
    'sub_order_no'   => '',
    'order_doc_id'  => '440583199705234511',
    'order_doc_name' => '陈子安',
    'cert_type'      => 'IDCARD', //out_trade_no-商家订单号 transaction_id-微信支付订单号 sub_order_no-商家子订单号 sub_order_id-微信子订单号
];

try {
    $tmp = $wechatService->orderPersonVerify($info);
} catch (Exception $e) {
    var_dump($e->getMessage());
    die();
}
var_dump($tmp); die();
die();
