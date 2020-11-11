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

    /**
     * @var string 获取最新证书
     */
    private $get_cert_url = 'https://api.mch.weixin.qq.com/v3/certificates';

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
        $this->credentialValidate->setRule(
            [
                'body'       => 'require|max:128',
                'order_fee'  => 'require|max:10',
                'notify_url' => 'require|max:256',
                'attach'     => 'require|max:128',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

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
            'attach'           => $data['attach'],
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
        $this->credentialValidate->setRule(
            [
                'order_no' => 'require|max:32',
                'pay_no'   => 'require|max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        if (empty($data['order_no']) && empty($data['pay_no'])) {
            throw new ClientError('参数错误: order_no , pay_no not be null');
        }

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
        $this->credentialValidate->setRule(
            [
                'order_no'        => 'requireIf,pay_no=|max:32',
                'pay_no'          => 'requireIf,order_no=|max:32',
                'refund_no'       => 'require|max:32',
                'total_fee'       => 'require',
                'refund_fee'      => 'require',
                'refund_fee_type' => 'max:8',
                'refund_desc'     => 'max:80',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        if (empty($data['order_no']) && empty($data['pay_no'])) {
            throw new ClientError('参数错误: order_no, pay_no not be null');
        }

        //退款报文组装
        $payment_arr = [
            'appid'           => $this->appId,
            'mch_id'          => $this->mchId,
            'nonce_str'       => $this->getNonceStr(),
            'transaction_id'  => isset($data['pay_no']) ? $data['pay_no'] : '',
            'out_trade_no'    => isset($data['EntOrder']) ? $data['order_no'] : '',
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
        $this->credentialValidate->setRule(
            [
                'pay_no'    => 'max:32',
                'order_no'  => 'max:32',
                'refund_no' => 'max:32',
                'refund_id' => 'max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        if (empty($data['pay_no']) && empty($data['order_no']) && empty($data['refund_no']) && empty($data['refund_id'])) {
            throw new ClientError('参数错误: pay_no,order_no,refund_no,refund_id not be null');
        }

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

        $this->credentialValidate->setRule(
            [
                'order_no' => 'require|max:32',
                'pay_no'   => 'require|max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        $param_arr = [
            'appid'               => $this->appId,
            'mchid'               => $this->mchId,
            'out_trade_no'        => $data['order_no'],
            'transaction_id'      => $data['pay_no'],
            'customs'             => $this->custom,
            'merchant_customs_no' => $this->customNo,
            'fee_type'            => 'CNY',
        ];

        $auth = $this->sign($param_arr, 'POST');

        $this->setJsonParams($param_arr);

        $this->setHeaders(['Authorization' => $auth]);

        $this->setHeaders(['User-Agent' => $this->mchId]);

        return $this->httpPostJson();
    }

    /**
     * 订单海关报关查询.
     */
    public function orderCustomsQuery($data)
    {
        //设置方法接口路由
        $this->url = $this->customs_query_url;

        $this->credentialValidate->setRule(
            [
                'order_type' => 'require|in:IDCARD',
                'order_no'   => 'require|max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        $param_arr = [
            'appid'      => $this->appId,
            'mchid'      => $this->mchId,
            'order_type' => $data['order_type'],
            'order_no'   => $data['order_no'],
            'customs'    => $this->custom,
        ];

        $this->url .= '?';

        //组装url
        foreach ($param_arr as $key => $value) {
            $this->url .= $key . '=' . $value . '&';
        }

        $this->url = substr($this->url, 0, strlen($this->url) - 1);

        $auth = $this->sign($param_arr, 'GET');

        $this->setHeaders(['Authorization' => $auth]);

        $this->setHeaders(['User-Agent' => $this->mchId]);

        return $this->httpGet($this->url, $param_arr);
    }

    /**
     * 订单海关报关重推.
     */
    public function orderCustomsRedeclare($data)
    {
        //设置方法接口路由
        $this->url = $this->customs_redeclare_url;

        $this->credentialValidate->setRule(
            [
                'out_trade_no'   => 'require|max:32',
                'transaction_id' => 'require|max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误:' . $this->credentialValidate->getError());
        }

        $param_arr = [
            'appid'               => $this->appId,
            'mchid'               => $this->mchId,
            'out_trade_no'        => $data['order_no'],
            'transaction_id'      => $data['pay_no'],
            'customs'             => $this->custom,
            'merchant_customs_no' => $this->customNo,
        ];

        $auth = $this->sign($param_arr, 'POST');

        $this->setJsonParams($param_arr);

        $this->setHeaders(['Authorization' => $auth]);

        return $this->httpPostJson();
    }

    /**
     * 身份证验证.
     */
    public function orderPersonVerify($data)
    {
        //设置方法接口路由
        $this->url = $this->verify_person_url;

        $serial_no = openssl_x509_parse(file_get_contents($this->cert));

        $param_arr = [
            'appid'               => $this->appId,
            'mchid'               => $this->mchId,
            'out_trade_no'        => $data['order_no'],
            'transaction_id'      => $data['pay_no'],
            'sub_order_no'        => '',
            'customs'             => $this->custom,
            'merchant_customs_no' => $this->customNo,
            'certificate_type'    => $data['cert_type'],
            'certificate_id'      => $this->rsa($data['order_doc_id']),
            'certificate_name'    => $this->rsa($data['order_doc_name']),
        ];

        $auth = $this->sign($param_arr, 'POST');

        $this->setHeaders(['Authorization' => $auth]);
        $this->setHeaders(['Wechatpay-Serial' => $serial_no]);

        $this->setJsonParams($param_arr);

        return $this->httpPostJson();
    }
}
