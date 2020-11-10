<?php

namespace paymentCenter\paymentClient\WeChatGlobal;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;
use paymentCenter\paymentClient\Base\WeChatPayGlobalCredential;

/**
 * 客户端.
 */
class Client extends WeChatPayGlobalCredential
{
    /**
     * @var Application
     */
    protected $credentialValidate;

    /**
     * @var string 统一下单接口
     */
    private $unified_order_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * @var string 订单查询
     */
    private $order_query_url = 'https://api.mch.weixin.qq.com/pay/orderquery';

    /**
     * @var string 申请退款 -- 需要API证书
     */
    private $refund_order_url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * @var string 退款查询 -- 需要API证书
     */
    private $refund_query_url = 'https://api.mch.weixin.qq.com/pay/refundquery';

    /**
     * @var string 报关申报 -- 需要报关备案信息
     */
    private $customs_order_url = 'https://api.mch.weixin.qq.com/global/v3/customs/orders';

    /**
     * @var string 报关查询 
     */
    private $customs_query_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/orders';

    /**
     * @var string 报关重推
     */
    private $customs_redeclare_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/redeclare';

    /**
     * @var string 身份信息验证
     */
    private $verify_person_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/verify-certificate';

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->credentialValidate = $app['validator'];
    }

    /**
     * 扫码支付.
     */
    public function qrCodePay($data)
    {
        //设置方法接口路由
        $this->url = $this->unified_order_url;

        //验证参数
        // $this->credentialValidate->setRule(
        //     [
        //         'MessageID'    => 'require|max:36',
        //         'MessageType'  => 'require|max:36',
        //         'Sender'       => 'require|max:36',
        //         'Receiver'     => 'require|max:36',
        //         'FunctionCode' => 'require|max:36',
        //         'BusinessType' => 'require|max:10',
        //         'IeFlag'       => 'require|max:1',
        //         'OpType'       => 'require|max:1',

        //         'DeclEntNo'   => 'require|max:50',
        //         'DeclEntName' => 'require|max:100',
        //         'EBEntNo'     => 'require|max:50',
        //         'EBEntName'   => 'require|max:100',
        //         'CustomsCode' => 'require|max:50',
        //         'CIQOrgCode'  => 'require|max:50',
        //         'EBPEntNo'    => 'require|max:50',
        //         'EBPEntName'  => 'require|max:100',
        //     ]
        // );

        // if (!$this->credentialValidate->check($data)) {
        //     throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        // }

        //生成商品备案包报文
        $payment_arr = [
            'appid'            => $this->appId,
            'mch_id'           => $this->mchId,
            'nonce_str'        => $this->getNonceStr(),
            'body'             => $data['body'],
            'out_trade_no'     => $data['order_no'],
            'fee_type'         => 'CNY',
            'total_fee'        => $data['order_fee'],
            'spbill_create_ip' => empty($_SERVER['REMOTE_ADDR']) ? '0.0.0.0' : $_SERVER['REMOTE_ADDR'],
            'notify_url'       => $data['notify_url'],
            'trade_type'       => 'NATIVE',
        ];

        $payment_arr['sign'] = $this->MakeSign($payment_arr);

        $param = $this->ToXml($payment_arr);

        //触发请求
        return $this->requestXmlPost($param);
    }

    /**
     * 订单查询.
     */
    public function orderQuery($data)
    {
        //设置方法接口路由
        $this->url = $this->order_query_url;

        //验证参数
        // $this->credentialValidate->setRule(
        //     [
        //         'order_no' => 'requireIf,pay_no=|max:36',
        //         'pay_no'   => 'requireIf,order_no=|max:36',
        //     ]
        // );

        // if (!$this->credentialValidate->check($data)) {
        //     throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        // }

        //生成商品备案包报文
        $payment_arr = [
            'appid'          => $this->appId,
            'mch_id'         => $this->mchId,
            'nonce_str'      => $this->getNonceStr(),
            'transaction_id' => isset($data['pay_no']) ? $data['pay_no'] : '',
            'out_trade_no'   => isset($data['order_no']) ? $data['order_no'] : '',
        ];

        $payment_arr['sign'] = $this->MakeSign($payment_arr);

        $param = $this->ToXml($payment_arr);
        //触发请求
        return $this->requestXmlPost($param);
    }

    /**
     * 订单退款.
     */
    public function orderRefund($data)
    {
        //设置方法接口路由
        $this->url = $this->refund_order_url;

        //验证参数
        // $this->credentialValidate->setRule(
        //     [
        //         'order_no'        => 'requireIf,pay_no=|max:36',
        //         'pay_no'          => 'requireIf,order_no=|max:36',
        //         'refund_no'       => 'require|max:36',
        //         'total_fee'       => 'require',
        //         'refund_fee'      => 'require',
        //         'refund_fee_type' => 'require|max:36',
        //         'refund_desc'     => 'max:36',
        //     ]
        // );

        // if (!$this->credentialValidate->check($data)) {
        //     throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        // }

        //退款报文组装
        $payment_arr = [
            'appid'           => $this->appId,
            'mch_id'          => $this->mchId,
            'nonce_str'       => $this->getNonceStr(),
            'transaction_id'  => isset($data['pay_no']) ? $data['pay_no'] : '',
            'out_trade_no'    => isset($data['order_no']) ? $data['order_no'] : '',
            'out_refund_no'   => $data['refund_no'],
            'total_fee'       => $data['total_fee'],
            'refund_fee'      => $data['refund_fee'],
            'refund_fee_type' => $data['refund_fee_type'],
            'refund_desc'     => $data['refund_desc'],
        ];

        $payment_arr['sign'] = $this->MakeSign($payment_arr);

        $param = $this->ToXml($payment_arr);
        //触发请求
        return $this->requestXmlPost($param, true);
    }

    /**
     * 订单退款查询.
     */
    public function orderRefundQuery($data)
    {
        //设置方法接口路由
        $this->url = $this->refund_query_url;
        //验证参数
        // $this->credentialValidate->setRule(
        //     [
        //         'transaction_id' => 'require|max:36',
        //         'out_trade_no'   => 'require|max:36',
        //         'out_refund_no'  => 'require|max:36',
        //         'refund_id'      => 'require|max:36',
        //     ]
        // );

        // if (!$this->credentialValidate->check($data)) {
        //     throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        // }

        //退款查询报文组装
        $payment_arr = [
            'appid'          => $this->appId,
            'mch_id'         => $this->mchId,
            'nonce_str'      => $this->getNonceStr(),
            'transaction_id' => isset($data['pay_no']) ? $data['pay_no'] : '',
            'out_trade_no'   => isset($data['order_no']) ? $data['order_no'] : '',
            'out_refund_no'  => isset($data['refund_no']) ? $data['refund_no'] : '',
            'refund_id'      => isset($data['refund_id']) ? $data['refund_id'] : '',
        ];

        $payment_arr['sign'] = $this->MakeSign($payment_arr);

        $param = $this->ToXml($payment_arr);

        //触发请求
        return $this->requestXmlPost($param, true);
    }

    /**
     * 订单海关报关.
     */
    public function orderCustoms($data)
    {
        //设置方法接口路由
        $this->url = $this->customs_order_url;

        $param_arr = [
            'appid'                 => $this->appId,
            'mchid'                 => $this->mchId,
            'out_trade_no'          => $data['EntOrderNo'],
            'transaction_id'        => $data['EntPayNo'],
            'customs'               => $this->custom,
            'merchant_customs_no'   => $this->customNo,
            'fee_type'              => 'CNY',
        ];

        $auth = $this->sign(json_encode($param_arr, JSON_UNESCAPED_UNICODE));

        $this->setJsonParams($param_arr);

        $this->setHeaders(['Authorization' => $auth]);

        return $this->httpPostJson();
    }

    /**
     * 订单海关报关查询.
     */
    public function orderCustomsQuery($data)
    {
        //设置方法接口路由
        $this->url = $this->customs_query_url;
    }

    /**
     * 订单海关报关重推.
     */
    public function orderCustomsRedeclare($data)
    {
        //设置方法接口路由
        $this->url = $this->customs_redeclare_url;
    }

    /**
     * 身份证验证.
     */
    public function orderPersonVerify($data)
    {
        //设置方法接口路由
        $this->url = $this->verify_person_url;
    }
}
