<?php

require_once __DIR__ . '/vendor/autoload.php';

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentService\WeChatPayGlobalService;

//主体参数
$config = [
    'wx_appid' => 'wx35a4eae2a40da25b',
    'wx_key'   => 'omall2020omall2020omall2020omall',
    'wx_mchid' => '127165103',

    'custom_no' => '4401963G59',
    'custom'    => 'GUANGZHOU_ZS',

    //HTTPS 请求双向证书 -- 和商户私钥
    'wx_apiclient_key'  => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC2dl3HP6AlwVm+
    6q2K7iXEihrrU1K7T8sOrPbeQ6tFtqpLNi3gwIfHlnWd4ONGqmpAHEXfUMleG6FP
    nT11CDuPnt9BET7IAqMDmPr0ysHJUMopcKX8N0LX8M5dooN9AWJe7TI7TCs3U0ns
    EvrpCK8+ldCFFX9V7Hijkzhx02el6Wr9XqLyvE+m9Khr/AStRLvkG0HdGwtBgv3H
    gT+RjlnyYuqIAz5leh4cgNYtxSPgRCdI+ma+NPqyJ+bDcTiOC9sJR3/r6ykC+orX
    B8KXKyvfAzWeMXH6YsHBAwIZiAtjuAyGu33g3X0N3QBAuAwINVAChXHQw4v3uo4Q
    L/oLunG1AgMBAAECggEAbQ/hTAie7BJfV3uk0cc5cfnuzzyV1fqC8Dm4sfAWvuvJ
    bH1s/Mt4HXe0w/K5RvLz4XBE34/FXWf8ir79Digmmdknrxfkw963m53kW0+ad3+/
    5vc7b7+ER9jbMSLDn4OlTgJPpRwJNN+m7uIFcidLwIgCBJpt62kWTwfflEH90S/q
    NloF+8FbpYYKHsOC7vfW4rMfpHU1aU9Exy0eeCry3AVES3cfwvsF+/L1OY0wq9BA
    ZR4u3yR6j/F/ALLJoFqjjSRwnriXiYZH4TAJsCds0o991IO+cVzfI94KljsozRcf
    H5w6y7KeiLluOY9qsnjfKIeDnl7n21MbI+KhYmPwvQKBgQDpA75PTs+9kibfYS+s
    r3Npw5/m6HDTyf3aiBaV+7c4uAblZUTpJniZeZqa5nuWeMRJphOhnKM51zTW89SA
    Wpx1tGP9JK0/b8eJyi39lldKmbpSyxxpDaI5v8VpBTGZ2AvPAEx+hBNTdWht7QuN
    gxVnxFkcQ5s1pp6m3uBW5wVi0wKBgQDIdgpMRSmogrNLVy7bIo8+x4hJOou83WDh
    W5jDcWDTD8n1TYoJm6fX8IlwN1G+9cT3eUg1alKN30F2wDXVyn4YCtAx0fvzqhl2
    VTORwO00JHug7lLEDPEmM7kigSsnnLre/Www+JIuYi506ZDFRRcCP/+lZ5p3BFxh
    zEXv+gE0VwKBgQDXRPAS0Nq3ZkinYl/rB4R2l5Yoe2GRKDFwLP3AvnX83nbwguhx
    BWuxTlj49ioDT7r314iXa+CYVSup0kXl9tBJJciiW7n6u0f7El/+wWAPuYobZy3u
    F/xhobu9VGEIokH2kW4aC+bW2ccKl05vBEVIQmuY4xSHMlGsFIfpgTDGpQKBgQC7
    We8n92AG2Ri/Kl1lezhL4WqTnj8ppfG8zcHJsDZhLJmBRRXlAvBslqetFFa3VbXf
    4R9GpPdBF050sRHfnSAaUkjgtzN2OR0RBnJrH9fR6uMMtpDuIHZlUOQPxo9Rt17D
    uDCc2ESvSx3GMOEYLgliFfRVX63y3EWHNcaLkan8gwKBgQC9Sgwp5csAx85DpOa1
    +BrMt5a5teZKXPIA1Rc1/T0uOksHDaCPS3sTGUjyYfjbMjo8g/An66FzCg+cA47g
    omb0xVpdXtA0MsWbQQSn14aJjelJVnp2EqmZBtADzuHWBmb9ONVX2+90SIRuBdXr
    1TFjxlgB9AFZHpFq5r5PuFl55w==',

    'wx_apiclient_cert' => 'MIID/DCCAuSgAwIBAgIUTlM4yqJTto2J2ybCUEf5l4dYxiUwDQYJKoZIhvcNAQEL
    BQAwXjELMAkGA1UEBhMCQ04xEzARBgNVBAoTClRlbnBheS5jb20xHTAbBgNVBAsT
    FFRlbnBheS5jb20gQ0EgQ2VudGVyMRswGQYDVQQDExJUZW5wYXkuY29tIFJvb3Qg
    Q0EwHhcNMjEwMjA5MDU1NzM5WhcNMjYwMjA4MDU1NzM5WjCBjTESMBAGA1UEAwwJ
    MTI3MTY1MTAzMSowKAYDVQQKDCHotKLku5jpgJrmlK/ku5jnp5HmioDmnInpmZDl
    hazlj7gxKzApBgNVBAsMIlZPWUFHRSBPRiBUSEUgREFXTiBUUkFESU5HIExJTUlU
    RUQxCzAJBgNVBAYMAkNOMREwDwYDVQQHDAhTaGVuWmhlbjCCASIwDQYJKoZIhvcN
    AQEBBQADggEPADCCAQoCggEBALZ2Xcc/oCXBWb7qrYruJcSKGutTUrtPyw6s9t5D
    q0W2qks2LeDAh8eWdZ3g40aqakAcRd9QyV4boU+dPXUIO4+e30ERPsgCowOY+vTK
    wclQyilwpfw3Qtfwzl2ig30BYl7tMjtMKzdTSewS+ukIrz6V0IUVf1XseKOTOHHT
    Z6Xpav1eovK8T6b0qGv8BK1Eu+QbQd0bC0GC/ceBP5GOWfJi6ogDPmV6HhyA1i3F
    I+BEJ0j6Zr40+rIn5sNxOI4L2wlHf+vrKQL6itcHwpcrK98DNZ4xcfpiwcEDAhmI
    C2O4DIa7feDdfQ3dAEC4DAg1UAKFcdDDi/e6jhAv+gu6cbUCAwEAAaOBgTB/MAkG
    A1UdEwQCMAAwCwYDVR0PBAQDAgTwMGUGA1UdHwReMFwwWqBYoFaGVGh0dHA6Ly9l
    dmNhLml0cnVzLmNvbS5jbi9wdWJsaWMvaXRydXNjcmw/Q0E9MUJENDIyMEU1MERC
    QzA0QjA2QUQzOTc1NDk4NDZDMDFDM0U4RUJEMjANBgkqhkiG9w0BAQsFAAOCAQEA
    q/VyuFxtfZ2qNoz00b1g0xP7LGwh8/cDnBxyuQ/5yTA6qszUDeLcdqUS2ZN1/esY
    XDVEalF+H5W/Rx1y8tDvUfrLNplL+IXKIJqXzaYNNfPO8QRXWWP+dS1Xii+qp7Ir
    rupZLD2ylZphQKe8NxMABKONz9g20zN8qAqlZrx2xzFfDdvXYtXJK2wbPYxun6oO
    BUMQO5cAzMIXwzn/uW6hZ6IwRzWEB6mkkNYhaa+1l0nHPLYzLK3YGWDQ8NOnBjBB
    vHil+46KIoKcOdtVLMCxcnOjdk505PuTt+9C+nPt6v1D8sg1Odr2G16AwsRj4aqM
    exDs/pf8yDzK8olGV2WOWg==',

    //平台公钥证书
    'cert' => 'MIID3DCCAsSgAwIBAgIUNlpkgAyK1cR4aIrl6YXSz4y45cQwDQYJKoZIhvcNAQEL
    BQAwXjELMAkGA1UEBhMCQ04xEzARBgNVBAoTClRlbnBheS5jb20xHTAbBgNVBAsT
    FFRlbnBheS5jb20gQ0EgQ2VudGVyMRswGQYDVQQDExJUZW5wYXkuY29tIFJvb3Qg
    Q0EwHhcNMjEwMjA5MDU1NzM4WhcNMjYwMjA4MDU1NzM4WjBuMRgwFgYDVQQDDA9U
    ZW5wYXkuY29tIHNpZ24xEzARBgNVBAoMClRlbnBheS5jb20xHTAbBgNVBAsMFFRl
    bnBheS5jb20gQ0EgQ2VudGVyMQswCQYDVQQGDAJDTjERMA8GA1UEBwwIU2hlblpo
    ZW4wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCqMl5HjIaVPoJcTTyq
    jGHRhhDcUmM5i9siUsIjcE2JRNXJVmXqmrPXAEOeg7if0ImC4fnOzMbQEkJRJZIG
    RA7e0OLyAiHhlmAGbDjA0/tQvfl05GgsCQZucghguAwnlBAVMqF+wmwO0DbE8/wd
    MOQPBVuxgEiTPGDUVsTu+aGxqVNjy/iq1rKUtZmpGpdvzXuNH2naWeCFKls3lu8q
    yZFjWcOieOedP9DG2O44Tx0CwpZOWs1Kx07CM7AB8B3KzFBMwFFpZamG8gDuurrY
    IPqUxlrmGA1Jb20kZMNqcpSlq2L65/2nit+c0AIHqp/R261fKUFYArmy1nyHFMqb
    9WCxAgMBAAGjgYEwfzAJBgNVHRMEAjAAMAsGA1UdDwQEAwIE8DBlBgNVHR8EXjBc
    MFqgWKBWhlRodHRwOi8vZXZjYS5pdHJ1cy5jb20uY24vcHVibGljL2l0cnVzY3Js
    P0NBPTFCRDQyMjBFNTBEQkMwNEIwNkFEMzk3NTQ5ODQ2QzAxQzNFOEVCRDIwDQYJ
    KoZIhvcNAQELBQADggEBACJHi4kkWe3Xlsn5ZNWeKyfR/ssoMrB6q41oduZZ843/
    PV/i0m8s6s67gh//sSD8A1qmuP8GzTShP6Zxi/jFvyIUKpD2BWNCKsJ2XClPJk8v
    1n09w1apvSfQ+bQX/+ia/bGRNtSwtqD0lGW7HvIIui0Grb/0zwOXKZIxfra5TIrN
    irnnYdk0T1le706FLbkyMQmLtOrkcxnVfjSEo5YPBcfpKpwCUiRlESpqTDLfLgmB
    ZJuI2y1gyV904+EHc3e4xQee82/MXQIPAf1qmEhgk69QH3zTH0hFNVVq8KixBplf
    MrYMjxPsEet179/koTsP7gsx7UEDWDU3B+OgYcIDiXg=',

    //aesKey -- 解密平台证书
    'APIv3_key' => 'omall2020omall2020omall2020omall',
];

$ioc_con_app = new Application($config);

$wechatService = new WeChatPayGlobalService($ioc_con_app);

//业务参数
// $info = [
//     'body'       => 'test',
//     'attach'     => '1',
//     'order_no'   => '1000000111',
//     'order_fee'  => '1',
//     'notify_url' => 'https://www.thinbug.com/q/34726530',
// ];

// $tmp = $wechatService->qrCodePay($info);
// var_dump($tmp);
// die();

//订单查询
// $info = [
//     'order_no' => '2102014046316388',
// ];

// try {
//     $tmp = $wechatService->orderQuery($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());
//     die();
// }
// var_dump($tmp);
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
//     'order_no'        => '10000001',
//     'refund_no'         => '',
//     'pay_no'          => '4200000738202011092400680961',
//     'refund_id'          => '',
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
$info = [
    'order_no'   => '2103059327286515',
    'pay_no' => '4200000996202103059752235499',
    'sub_order_no' => '2103059327286515',
    'sub_order_fee' => 1,
    'product_fee' => 1,
    'transport_fee' => 0,
];

try {
    $tmp = $wechatService->orderCustoms($info);
} catch(Exception $e) {
    var_dump($e->getMessage());die();
}
var_dump($tmp);die();
die();

//查询报关
// $info = [
//     'order_no'   => '10000001',
//     'order_type' => 'out_trade_no', //out_trade_no-商家订单号 transaction_id-微信支付订单号 sub_order_no-商家子订单号 sub_order_id-微信子订单号
// ];

// try {
//     $tmp = $wechatService->orderCustomsQuery($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// var_dump(json_decode($tmp));die();
// die();

//身份验证
// $info = [
//     'order_no'     => '10000001',
//     'pay_no'       => '4200000738202011092400680961',
//     'sub_order_no'   => '',
//     'order_doc_id'  => '440583199705234511',
//     'order_doc_name' => '陈子安',
//     'cert_type'      => 'IDCARD', //out_trade_no-商家订单号 transaction_id-微信支付订单号 sub_order_no-商家子订单号 sub_order_id-微信子订单号
// ];

// try {
//     $tmp = $wechatService->orderPersonVerify($info);
// } catch (Exception $e) {
//     var_dump($e->getMessage());
//     die();
// }
// var_dump($tmp); die();
// die();

//获取证书信息
// try {
//     $tmp = $wechatService->getCert();
// } catch (Exception $e) {
//     var_dump($e->getMessage());
//     die();
// }
// var_dump($tmp); die();
// die();

//重推海关
// $info = [
//     'order_no'     => '10000001',
//     'pay_no'       => '4200000738202011092400680961',
// ];

// try {
//     $tmp = $wechatService->orderCustomsRedeclare($info);
// } catch (Exception $e) {
//     var_dump($e->getMessage());
//     die();
// }
// var_dump($tmp); die();
// die();
