<?php
/*
 * @Description:
 * @Version:
 * @Author: Yan
 * @Date: 2020-11-09 10:32:36
 * @LastEditors: Yan
 * @LastEditTime: 2020-11-16 17:23:52
 */

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 支付宝国际  支付通路集成服务.
 *
 * 功能受限的实现:
 * 1、MD5模式
 * 2、非服务商
 *
 * 所支持的支付功能:
 * -- 通用功能(支付结果查询、退款、退款结果查询)
 * -- NATIVE支付:  PC扫码支付.
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
     * 扫码支付.
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
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        return $this->_aliPayGlobal->orderQuery($data);
    }

    /**
     * 订单退款.
     */
    public function orderRefund($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        return $this->_aliPayGlobal->orderRefund($data);
    }

    /**
     * 订单海关报关.
     */
    public function orderCustoms($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        return $this->_aliPayGlobal->orderCustoms($data);
    }

    /**
     * 订单海关报关查询.
     */
    public function orderCustomsQuery($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        return $this->_aliPayGlobal->orderCustomsQuery($data);
    }

    /**
     * 退款查询.
     */
    public function refundQuery($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        return $this->_aliPayGlobal->refundQuery($data);
    }

    /**
     * md5加密.
     */
    public function getMd5Sign($data)
    {
        if (empty($data)) {
            throw new ClientError('参数缺失');
        }

        if (!is_array($data)) {
            throw new ClientError('参数必须是array');
        }

        return $this->_aliPayGlobal->getMd5Sign($data);
    }
}
