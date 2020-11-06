<?php

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 微信支付集成服务.
 */
class WeChatPayService
{
    private $_weChatPay;

    public function __construct(Application $app)
    {
        $this->_weChatPay = $app['wechat'];
        
        //从容器中根据相关调用的相关服务通路校验所需参数
    }

    /**
     * 扫码支付.
     */
    public function qrCodePay(array $info)
    {
        return $this->_weChatPay->qrCodePay($info);
    }
}
