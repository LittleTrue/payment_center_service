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

        $config_param = $this->_aliPayGlobalConfig = $app['config'];

        //通用参数验证
        if (!isset($config_param['partner_id']) || empty($config_param['partner_id'])) {
            throw new ClientError('partner_id不能为空');
        }

        if (!isset($config_param['key']) || empty($config_param['key'])) {
            throw new ClientError('key不能为空');
        }
    }

    /**
     * 扫码支付
     */
    public function qrCodePay($data, $signType = 'MD5')
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        } 

        if (empty($signType)) {
            throw new ClientError('签名类型缺失');
        }

        return $this->_aliPayGlobal->qrCodePay($data);
    }

    /**
     * 订单查询.
     */
    public function orderQuery($data)
    {
    }

    /**
     * 订单退款
     */
    public function orderRefund($data)
    {
    }

    /**
     * 订单海关报关
     */
    public function orderCustoms($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        } 
        
        return $this->_aliPayGlobal->orderCustoms($data);
    }

    /**
     * 订单海关报关查询
     */
    public function orderCustomsQuery($data)
    {
    }
}
