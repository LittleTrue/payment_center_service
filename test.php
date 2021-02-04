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

    //HTTPS 请求双向证书 -- 和商户私钥
    'wx_apiclient_key'  => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDM61JbaQHFdzzA
    Ypw8y9m5G4c4Dx9SqFLRJiaDUY2TZvwzWt6ORiS0jrJmDhjNsZxAvVDE72B9+Vdk
    8YaY4cWYOzWshDdNLVCAcANeR+I+KZeJrsR0v9qUZ4oZAINbx1baGInRfZcemaMJ
    OnK8OpgxYHcSQ3zHnW6lZ46u5Ww/+NSDA3u03Uf6mlU9MWxt5iiLVb+7w+onGeeG
    lqe11OZ0QOF1q3hOXpi2I9EkfxpGJ+MUNmh2IAFXENClPCUgGIw017hdhs7gOUnl
    If4LWGFmfNg3xkAmCQRmO4cb47164t96swl4ahufwj+R7IRfjesuEJpRt2m5F32g
    Qg+tkKvvAgMBAAECggEAGEK4AmiBNC60u5YxJyV/RmIS9TkdHT0eaPKaVqu+Fjh4
    HWBhvvhg+ht21YxPtXKUrDl9qYMpqmBtz16k786zBR3lceJJZdK6mCoqy+u4xmFI
    Np3BVo0sRKupduJPqvsDtlh3YJz82juYSMxprw1E3XutPTVOPZfO9Lg49U3mLgCC
    Ys4Xi0JJmhvKpZ85alDJBr1etCWMj22CaZ4BC17HRxQQ9jpONUp+8lZGOcCfr8qL
    LS7PiU2XHD4C8lPLfW/+nToOEpoK/D50Q1avlDahyR3QFlQBohb+Re5UkRn9yuP8
    pSig46/fFePuOw/BsVRPBYFwOXdPeAxeQMhY9PyPcQKBgQDw9IedrzqOPFP9jY/b
    Xj4TAyklsjHK8trgK8nUTu+HOLpm1Rl2NvMWcbxA7MSNoAcbfk3ym2a6I4n7yBIh
    CD5V82/okm/e4npOWMMpPSw7QT3b6oxiyc7Beb/QjYo06GnHMVqDqBZZ5OYIIHM1
    B7pUVtlOo8nrPLT+2uvCJDeQmQKBgQDZtslkOtPTPAjiBBmQe4HVNZw9E49C1ptq
    wuHapfQIbiBta4HcGIS9yzXjjzvOINMtz1Fj71c9TwGx2J6W8QkYOURVVMT+f8K6
    KUIhFzbOW16N1VKtarloeS64yQ9/ont8WjJY3Zk/36XfHmVB+knk2DDOAjWna2Xz
    Gj5kD3ONxwKBgDGL1qcR9RRyS0MD7OTKyDDYoYlizuJQdblKx8GkSZSgkZO7lfS8
    79dZo8Al73S/xzZcDmMomUMFM8ZWKYQpUcgSupw0IFTQdR3PGrJMJeA5ViL2Y8+c
    d6tJAaw8roKal8Wh3F7eHVp2uuZI5eetox7cjOqiu05nuT/+fCP7loFhAoGAUctY
    km1/k8bkV3XO3Pdp6d6AWnn0wtm5/jxFek4LBvfI+xL+8N/NTzj/gAUaJPE0JevT
    2kXbcs8yBc0ql+7qyc0KHT0B3dPGJwbFL4V3M8QkHAlfTIPiMJwGr6nqRruWxTtl
    2vk2UEcIHHFg/Y65IF8QxvURj1rMLK9ZbWJYuZUCgYEAwLbk59TjZlOHi3t8fQbq
    e4H3h+oiWXUyCT0kgux5qLwrLFcq6v0fC7E/D/Ha2GxpZmIgTzEJzuaMnIBSL/wr
    W5HNdbmwFlj1xG7+AygydOUqKSKW9o7TkeRia6eG2jOxsLHTzsKsZaiB1FBrf8hd
    NqER0pRIpUZjOUOvFmMmx6Y=',
    'wx_apiclient_cert' => 'MIID9jCCAt6gAwIBAgIUQuP+8G/dOLh1tOO1euY1ICttHIgwDQYJKoZIhvcNAQEL
    BQAwXjELMAkGA1UEBhMCQ04xEzARBgNVBAoTClRlbnBheS5jb20xHTAbBgNVBAsT
    FFRlbnBheS5jb20gQ0EgQ2VudGVyMRswGQYDVQQDExJUZW5wYXkuY29tIFJvb3Qg
    Q0EwHhcNMjAwNjIyMDc0MDMzWhcNMjUwNjIxMDc0MDMzWjCBhzETMBEGA1UEAwwK
    MTUxMDE5NzkxMTEbMBkGA1UECgwS5b6u5L+h5ZWG5oi357O757ufMTMwMQYDVQQL
    DCrmtbfljZfmlrDmr4Xlm73pmYXot6jlooPnlLXllYbmnInpmZDlhazlj7gxCzAJ
    BgNVBAYMAkNOMREwDwYDVQQHDAhTaGVuWmhlbjCCASIwDQYJKoZIhvcNAQEBBQAD
    ggEPADCCAQoCggEBAMzrUltpAcV3PMBinDzL2bkbhzgPH1KoUtEmJoNRjZNm/DNa
    3o5GJLSOsmYOGM2xnEC9UMTvYH35V2TxhpjhxZg7NayEN00tUIBwA15H4j4pl4mu
    xHS/2pRnihkAg1vHVtoYidF9lx6Zowk6crw6mDFgdxJDfMedbqVnjq7lbD/41IMD
    e7TdR/qaVT0xbG3mKItVv7vD6icZ54aWp7XU5nRA4XWreE5emLYj0SR/GkYn4xQ2
    aHYgAVcQ0KU8JSAYjDTXuF2GzuA5SeUh/gtYYWZ82DfGQCYJBGY7hxvjvXri33qz
    CXhqG5/CP5HshF+N6y4QmlG3abkXfaBCD62Qq+8CAwEAAaOBgTB/MAkGA1UdEwQC
    MAAwCwYDVR0PBAQDAgTwMGUGA1UdHwReMFwwWqBYoFaGVGh0dHA6Ly9ldmNhLml0
    cnVzLmNvbS5jbi9wdWJsaWMvaXRydXNjcmw/Q0E9MUJENDIyMEU1MERCQzA0QjA2
    QUQzOTc1NDk4NDZDMDFDM0U4RUJEMjANBgkqhkiG9w0BAQsFAAOCAQEAEX0Rgui3
    QOqEee+B1DYTNO9SWYoEnNoc//NadnghJjR8JaI37IYHkkpqP4Lg8FwvakHyjaDm
    7/48WaT5LVhYwD7cWbzXp3X0yBjblL7Ckakk30cllX8tcXtE7B9Elm35m4oVk8Vh
    ssAQbsglrwMgos50+uFS9Ya5w+1B3b+svmd+3lR1K93M/TKnTepKD/IZydGE/a1H
    vD+Y6pRseeg9L4RiB1FaOvuCT43gtuhjggb+XoyTq8r0afpzr6xp/4c8nACIPMc7
    kXVw1CYaybVWsDxzY5X4rCjouGjsuu29vFQuSXWhZDnyNAzEubkgSTVpP9Ha0pAF
    fOVVysGtVrtbhQ==',

    //平台公钥证书
    'cert' => 'MIID3DCCAsSgAwIBAgIUPhlzlSA71RwABp06Ko8SdIOEk3QwDQYJKoZIhvcNAQEL
    BQAwXjELMAkGA1UEBhMCQ04xEzARBgNVBAoTClRlbnBheS5jb20xHTAbBgNVBAsT
    FFRlbnBheS5jb20gQ0EgQ2VudGVyMRswGQYDVQQDExJUZW5wYXkuY29tIFJvb3Qg
    Q0EwHhcNMjAwNjIyMDc0MDMyWhcNMjUwNjIxMDc0MDMyWjBuMRgwFgYDVQQDDA9U
    ZW5wYXkuY29tIHNpZ24xEzARBgNVBAoMClRlbnBheS5jb20xHTAbBgNVBAsMFFRl
    bnBheS5jb20gQ0EgQ2VudGVyMQswCQYDVQQGDAJDTjERMA8GA1UEBwwIU2hlblpo
    ZW4wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC6r2qrsKAErrMnDK5N
    HVP3f2F8zp02JsIGxUnIBWdYu0XVZmTEfjHdVprndGL8HCb8dX3eZm9MTkGjswRx
    3WNdftjL3Q/aKGMQpElkFGIjYl8/ZS2ul/LDh+5/3HifpsavULamqOkJqPd6Wwgb
    gGhNXzjnPEaZUgcvxy9SmI66vgfNi/s0BStVihfy5ppJJ/naYeUi8/aALV5GreSb
    qBiRR54ChPZCrPEBGwyCk1EpBY9K6DrINvY61ju7uDoVDmnHwC1wEX4+X1al+Gz5
    Ui+EjgQq5SgZ3yf0ptXds2iKy8s7AkOMy+mGo0RBeu3+4IFYQkVB+DrDVbUQQClD
    XRa3AgMBAAGjgYEwfzAJBgNVHRMEAjAAMAsGA1UdDwQEAwIE8DBlBgNVHR8EXjBc
    MFqgWKBWhlRodHRwOi8vZXZjYS5pdHJ1cy5jb20uY24vcHVibGljL2l0cnVzY3Js
    P0NBPTFCRDQyMjBFNTBEQkMwNEIwNkFEMzk3NTQ5ODQ2QzAxQzNFOEVCRDIwDQYJ
    KoZIhvcNAQELBQADggEBAEqNZ7aA836V79xVP68Za8j3WbmSpZ6WYL3W2WSMIHJk
    MZpfF119bSvzB4wBe/hO+Ake1ppKgL2ilXAg/KEWU9XpHfAK1iwlPpGsCmo+oXzp
    aGM3RBbwO5sjnfLymYNmA0SQIM7cR0Nf2pamZDqMkekvPSZGjAaiweAX3QEJq2Rb
    7Js2lCz6yAEqlcuAMz2gk3/sxj1kSBiLSgvF52qMWAE1XMwms++zFCTEdXPyWw6F
    6ITy0aU3YNta4Ki4eZx5/xKrGMTxYPdEGl870JqKHPQVI+MGJHeyWqnYLaDuCYtf
    HDFpKhiz/zLWoh/RVi81yV4UTq6fR5yUWQeDD1WAy48=',

    //aesKey -- 解密平台证书
    'APIv3_key' => 'HHainanxinyiguojikuajing20180814',
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
$info = [
    'order_no'          => '10000001',
    'refund_no'         => 'R10000001',
    'total_fee'         => '1',
    'refund_fee'        => '1',
    'refund_fee_type'   => '',
    'refund_desc'       => '123',
];

try {
    $tmp = $wechatService->orderRefund($info);
} catch(Exception $e) {
    var_dump($e->getMessage());die();
}
echo json_encode($tmp);
die();

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
// $info = [
//     'order_no'   => '10000001',
//     'pay_no' => '4200000738202011092400680961',
// ];

// try {
//     $tmp = $wechatService->orderCustoms($info);
// } catch(Exception $e) {
//     var_dump($e->getMessage());die();
// }
// var_dump($tmp);die();
// die();

//查询报关
$info = [
    'order_no'   => '10000001',
    'order_type' => 'out_trade_no', //out_trade_no-商家订单号 transaction_id-微信支付订单号 sub_order_no-商家子订单号 sub_order_id-微信子订单号
];

try {
    $tmp = $wechatService->orderCustomsQuery($info);
} catch(Exception $e) {
    var_dump($e->getMessage());die();
}
var_dump(json_decode($tmp));die();
die();

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
