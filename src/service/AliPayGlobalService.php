<?php

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 支付宝支付集成服务.
 */
class AliPayGlobalService
{
    private $_aliPayGlobal;

    public function __construct(Application $app)
    {
        $this->_aliPayGlobal = $app['alipay_global'];
        
        //从容器中根据相关调用的相关服务通路校验所需参数
    }
}
