<?php

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 微信支付集成服务.
 */
class WeChatPayGlobalService
{

    private $_weChatPayGlobal;

    public function __construct(Application $app)
    {
        $this->_weChatPayGlobal = $app['wechat_global'];
        
        //从容器中根据相关调用的相关服务通路校验所需参数
    }
}
