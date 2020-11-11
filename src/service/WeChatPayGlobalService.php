<?php

namespace paymentCenter\paymentService;

use Exception;
use paymentCenter\paymentClient\Application;

/**
 * 微信支付集成服务.
 */
class WeChatPayGlobalService
{
    private $_weChatPayGlobal;

    private $_weChatPayGlobalConfig;

    public function __construct(Application $app)
    {
        $this->_weChatPayGlobal = $app['wechat_global'];

        //从容器中根据相关调用的相关服务通路校验所需参数

        // 微信国际通路(MD5模式 - 非服务商):
        //1、wxpay_appid   2、wxpay_key   3、wxpay_mchid
        //退款方法 -> 4、API证书
        //报关方法 -> 5、申报海关 6、主体在海关总署备案编号

        $config_param = $this->_weChatPayGlobalConfig = $app['config'];

        //通用参数验证
        if (!isset($config_param['wx_appid']) || empty($config_param['wx_appid'])) {
            throw new Exception('微信公众账号ID不能为空');
        }

        if (!isset($config_param['wx_key']) || empty($config_param['wx_key'])) {
            throw new Exception('微信支付密钥不能为空');
        }

        if (!isset($config_param['wx_mchid']) || empty($config_param['wx_mchid'])) {
            throw new Exception('微信商户号不能为空');
        }
    }

    /**
     * 扫码支付.
     */
    public function qrCodePay($data, $signType = 'MD5')
    {
        if (empty($data)) {
            throw new Exception('参数缺失');
        } 

        if (empty($signType)) {
            throw new Exception('签名类型缺失');
        }

        return $this->_weChatPayGlobal->qrCodePay($data, $signType);
    }

    /**
     * 订单查询.
     */
    public function orderQuery($data, $signType = 'MD5')
    {
        if (empty($data)) {
            throw new Exception('参数缺失');
    }

        // if (empty($signType)) {
        //     throw new Exception('签名类型缺失');
        // }

        return $this->_weChatPayGlobal->orderQuery($data, $signType = 'MD5');
    }

    /**
     * 订单退款.
     */
    public function orderRefund($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['wx_apiclient_cert']) || empty($this->_weChatPayGlobalConfig['wx_apiclient_cert'])) {
            throw new Exception('微信交互证书apiclient_cert.pem缺失');
        }

        if (!isset($this->_weChatPayGlobalConfig['wx_apiclient_key']) || empty($this->_weChatPayGlobalConfig['wx_apiclient_key'])) {
            throw new Exception('微信交互证书apiclient_key.pem缺失');
        }

        return $this->_weChatPayGlobal->orderRefund($data);
    }

    /**
     * 订单退款.
     */
    public function orderRefundQuery($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['wx_apiclient_cert']) || empty($this->_weChatPayGlobalConfig['wx_apiclient_cert'])) {
            throw new Exception('微信交互证书apiclient_cert.pem缺失');
        }

        if (!isset($this->_weChatPayGlobalConfig['wx_apiclient_key']) || empty($this->_weChatPayGlobalConfig['wx_apiclient_key'])) {
            throw new Exception('微信交互证书apiclient_key.pem缺失');
        }

        return $this->_weChatPayGlobal->orderRefundQuery($data);
    }

    /**
     * 订单海关报关.
     */
    public function orderCustoms($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['custom']) || empty($this->_weChatPayGlobalConfig['custom'])) {
            throw new Exception('微信报关所选海关缺失');
        }

        if (!isset($this->_weChatPayGlobalConfig['custom_no']) || empty($this->_weChatPayGlobalConfig['custom_no'])) {
            throw new Exception('微信支报关所对应海关编号缺失');
        }

        return $this->_weChatPayGlobal->orderCustoms($data);
    }

    /**
     * 订单海关报关查询.
     */
    public function orderCustomsQuery($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['custom']) || empty($this->_weChatPayGlobalConfig['custom'])) {
            throw new Exception('微信报关所选海关缺失');
        }

        return $this->_weChatPayGlobal->orderCustomsQuery($data);
    }

    /**
     * 身份校验.
     */
    public function orderPersonVerify($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['custom']) || empty($this->_weChatPayGlobalConfig['custom'])) {
            throw new Exception('微信报关所选海关缺失');
        }

        if (!isset($this->_weChatPayGlobalConfig['custom_no']) || empty($this->_weChatPayGlobalConfig['custom_no'])) {
            throw new Exception('微信支报关所对应海关编号缺失');
        }

        return $this->_weChatPayGlobal->orderPersonVerify($data);
    }

    /**
     * 支付单重推.
     */
    public function orderCustomsRedeclare($data)
    {
        if (!isset($this->_weChatPayGlobalConfig['custom']) || empty($this->_weChatPayGlobalConfig['custom'])) {
            throw new Exception('微信报关所选海关缺失');
        }

        if (!isset($this->_weChatPayGlobalConfig['custom_no']) || empty($this->_weChatPayGlobalConfig['custom_no'])) {
            throw new Exception('微信支报关所对应海关编号缺失');
        }

        return $this->_weChatPayGlobal->orderCustomsRedeclare($data);
    }

    /**
     * 获取最新证书.
     */
    public function getCert()
    {
        return $this->_weChatPayGlobal->getCert();
    }
}
