<?php

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;

/**
 * TODO --
 * 支付宝支付集成服务.
 */
class AliPayService
{
    private $_aliPay;

    public function __construct(Application $app)
    {
        $this->_aliPay = $app['alipay'];

        //从容器中根据相关调用的相关服务通路校验所需参数
    }
}
