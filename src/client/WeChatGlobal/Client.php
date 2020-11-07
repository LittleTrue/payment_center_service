<?php

namespace paymentCenter\paymentClient\WeChatGlobal;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\WeChatPayGlobalCredential;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

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
     * @var Application
     */
    private $_weChatPayGlobalConfig;

    /**
     * @var string 统一下单接口
     */
    private $unified_order_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * @var string 订单查询
     */
    private $order_query_url = 'https://api.mch.weixin.qq.com/pay/orderquery';

    /**
     * @var string 申请退款
     */
    private $refund_order_url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * @var string 退款查询
     */
    private $refund_query_url = 'https://api.mch.weixin.qq.com/pay/refundquery';

    /**
     * @var string 订单报关
     */
    private $customs_order_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/orders';

    /**
     * @var string 订单报关查询
     */
    private $customs_query_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/orders';

    /**
     * @var string 订单报关重推
     */
    private $customs_redeclare_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/redeclare';

    /**
     * @var string 身份信息验证
     */
    private $verify_person_url = 'https://apihk.mch.weixin.qq.com/global/v3/customs/verify-certificate';

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->credentialValidate     = $app['validator'];
        $this->_weChatPayGlobalConfig = $app['config'];
    }

    /**
     * 扫码支付.
     */
    public function qrCodePay($data)
    {
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
            'appid'            => $this->_weChatPayGlobalConfig['wx_appid'],
            'mch_id'           => $this->_weChatPayGlobalConfig['wx_mchid'],
            'nonce_str'        => $this->getNonceStr(),
            'body'             => $data['body'],
            'out_trade_no'     => $data['order_no'],
            'fee_type'         => 'CNY',
            'total_fee'        => $data['order_fee'],
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url'       => $data['notify_url'],
            'trade_type'       => 'JSAPI',
        ];

        $payment_arr['sign'] = $this->MakeSign($payment_arr);

        $param = $this->ToXml($payment_arr);

        //触发请求

    }

    /**
     * 订单查询.
     */
    public function orderQuery($data)
    {
    }

    /**
     * 订单退款.
     */
    public function orderRefund($data)
    {
    }

    /**
     * 订单海关报关.
     */
    public function orderCustoms($data)
    {
        $param_arr = [
            'appid'          => $this->_weChatPayGlobalConfig['wx_appid'],
            'mch_id'         => $this->_weChatPayGlobalConfig['wx_mchid'],
            'out_trade_no'   => $data['EntOrderNo'],
            'transaction_id' => $data['EntPayNo'],
            'customs'        => $this->_weChatPayGlobalConfig['custom'],
            'mch_customs_no' => $this->_weChatPayGlobalConfig['custom_no'],
            //2019年5月13日 微信验证监管对象 需要上传的字段
            'cert_type' => 'IDCARD',
            'cert_id'   => $data['OrderDocId'],
            'name'      => $data['OrderDocName'],
        ];
    }

    /**
     * 订单海关报关查询.
     */
    public function orderCustomsQuery($data)
    {
    }

    /**
     * (微信SDK) -- 输出xml字符.
     * @throws WxPayException
     */
    public function ToXml($param)
    {
        if (!is_array($param) || count($param) <= 0) {
            throw new ClientError('数组数据异常！');
        }

        $xml = '<xml>';
        foreach ($param as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }

 
}
