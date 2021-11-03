<?php

namespace paymentCenter\paymentClient\AliPayGlobal;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\AliPayGlobalCredential;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 客户端.
 */
class Client extends AliPayGlobalCredential
{
    /**
     * @var Application
     */
    protected $credentialValidate;

    /**
     * 测试环境
     */
    // private $request_url = 'https://mapi.alipaydev.com/gateway.do';

    /**
     * 生产环境
     */
    private $request_url = 'https://intlmapi.alipay.com/gateway.do';

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['validator'];
    }

    /**
     * 扫码支付.
     */
    public function qrCodePay($data, $signType = 'MD5')
    {
        $this->url = $this->request_url;

        //验证参数
        $this->credentialValidate->setRule(
            [
                'subject'      => 'require|max:256',
                'body'         => 'max:400',
                'out_trade_no' => 'require|max:64',
                'currency'     => 'require|max:10',
                // 'total_fee' => 'number',
                // 'rmb_fee' => 'number',
                'refer_url'    => 'require',
                'product_code' => 'require|max:32',
                'notify_url'   => 'require|max:200',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        if (!isset($data['trade_information']) || empty($data['trade_information'])) {
            throw new ClientError('参数错误：缺少trade_information');
        }

        //在验证trade_information
        $this->credentialValidate->setRule(
            [
                'business_type'  => 'require|max:1',
                'goods_info'     => 'require',
                'total_quantity' => 'require|number',
            ]
        );

        if (!$this->credentialValidate->check($data['trade_information'])) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            '_input_charset' => 'UTF-8',
            'currency'       => $data['currency'],
            'out_trade_no'   => $data['out_trade_no'],
            'partner'        => $this->_partnerId,
            'product_code'   => $data['product_code'],
            'service'           => 'create_forex_trade',
            'sign_type'         => 'MD5',
            'subject'           => $data['subject'],
            'body'              => isset($data['body']) ? $data['body'] : '',
            'total_fee'         => isset($data['total_fee']) ? $data['total_fee'] : '',
            'rmb_fee'           => isset($data['rmb_fee']) ? $data['rmb_fee'] : '',
            'refer_url'         => $data['refer_url'],
            'trade_information' => json_encode($data['trade_information']),
            'return_url'        => isset($data['return_url']) ? $data['return_url'] : '',
            'notify_url'        => isset($data['notify_url']) ? $data['notify_url'] : '',
        ];

        $param['sign'] = $this->MD5Sign($param);
        $url           = $this->buildRequestUrl($param);

        return ['response' => $url, 'pay_initial_request' => $url];
    }

    /**
     * 报关.
     */
    public function orderCustoms($data)
    {
        $this->url = $this->request_url;

        //验证参数
        $this->credentialValidate->setRule(
            [
                'out_request_no'        => 'require|max:32',
                'trade_no'              => 'require|max:64',
                'merchant_customs_code' => 'require|max:20',
                'amount'                => 'require',
                'customs_place'         => 'require|max:20',
                'merchant_customs_name' => 'require|max:256',
                'buyer_name'            => 'max:10',
                'buyer_id_no'           => 'max:18',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            'service'               => 'alipay.acquire.customs',
            'partner'               => $this->_partnerId,
            '_input_charset'        => 'UTF-8',
            'sign_type'             => 'MD5',
            //触发拆单申报时,推时保持customs_place、merchant_customs_name、out_request_no 一致
            //out_request_no这个字段是偏向请求编号的字段, 不过业务也可以将对应的订单/子订单传入当做唯一编号,方便之后触发查询也用订单号
            'out_request_no'        => $data['out_request_no'],
            'trade_no'              => $data['trade_no'],
            'merchant_customs_code' => $data['merchant_customs_code'],
            'amount'                => $data['amount'],//支付宝触发拆单申报的时候是传子订单的申报金额
            'customs_place'         => $data['customs_place'],
            'merchant_customs_name' => $data['merchant_customs_name'],
            'buyer_name'            => isset($data['buyer_name']) ? $data['buyer_name'] : '',
            'buyer_id_no'           => isset($data['buyer_id_no']) ? $data['buyer_id_no'] : '',
        ];

        //如果有传子订单号, 则控制拆单条件, 并透传
        if (isset($data['sub_order_no']) && !empty($data['sub_order_no'])) {
            $param['is_split'] = 'T';
            //子订单编号为,子支付单号会在相应的trade_no获取
            $param['sub_out_biz_no'] = $data['sub_order_no'];
        }

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($this->buildRequestUrl($param));
    }

    /**
     * 退款.
     */
    public function orderRefund($data)
    {
        $this->url = $this->request_url;

        // //验证参数
        $this->credentialValidate->setRule(
            [
                'notify_url'    => 'require|max:200',
                'out_return_no' => 'require|max:64',
                'out_trade_no'  => 'require|max:64',
                // 'return_amount' => 'require',
                'return_rmb_amount' => 'require',
                'currency'          => 'require|max:10',
                'gmt_return'        => 'require',
                'product_code'      => 'require|max:32',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            'service'           => 'forex_refund',
            'partner'           => $this->_partnerId,
            '_input_charset'    => 'UTF-8',
            'sign_type'         => 'MD5',
            'notify_url'        => isset($data['notify_url']) ? $data['notify_url'] : '',
            'out_return_no'     => $data['out_return_no'],
            'out_trade_no'      => $data['out_trade_no'],
            'return_amount'     => isset($data['return_amount']) ? $data['return_amount'] : '',
            'return_rmb_amount' => isset($data['return_rmb_amount']) ? $data['return_rmb_amount'] : '',
            'currency'          => $data['currency'],
            'gmt_return'        => $data['gmt_return'],
            'reason'            => isset($data['reason']) ? $data['reason'] : '',
            'product_code'      => $data['product_code'],
        ];

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($this->buildRequestUrl($param));
    }

    /**
     * 支付单查询.
     */
    public function orderQuery($data)
    {
        $this->url = $this->request_url;

        // //验证参数
        $this->credentialValidate->setRule(
            [
                'trade_no'     => 'max:64',
                'out_trade_no' => 'max:64',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            'service'        => 'single_trade_query',
            'partner'        => $this->_partnerId,
            '_input_charset' => 'UTF-8',
            'sign_type'      => 'MD5',
            'trade_no'       => $data['trade_no'],
            'out_trade_no'   => $data['out_trade_no'],
        ];

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($this->buildRequestUrl($param));
    }

    /**
     * 报关进度查询.
     */
    public function orderCustomsQuery($data)
    {
        $this->url = $this->request_url;

        // //验证参数
        $this->credentialValidate->setRule(
            [
                'out_request_nos' => 'require|max:329',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            'service'         => 'alipay.overseas.acquire.customs.query',
            'partner'         => $this->_partnerId,
            '_input_charset'  => 'UTF-8',
            'sign_type'       => 'MD5',
            'out_request_nos' => $data['out_request_nos'],
        ];

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($this->buildRequestUrl($param));
    }

    /**
     * 退款查询.
     */
    public function refundQuery($data)
    {
        $this->url = $this->request_url;

        // //验证参数
        $this->credentialValidate->setRule(
            [
                'out_trade_no'  => 'require|max:64',
                'out_return_no' => 'require|max:128',
            ]
        );

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('参数错误：' . $this->credentialValidate->getError());
        }

        //生成发送数据
        $param = [
            'out_trade_no'   => $data['out_trade_no'],
            'partner'        => $this->_partnerId,
            'service'        => 'alipay.acquire.refund.query',
            '_input_charset' => 'UTF-8',
            'out_return_no'  => $data['out_return_no'],
            'sign_type'      => 'MD5',
        ];

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($this->buildRequestUrl($param));
    }

    /**
     * md5加密.
     */
    public function getMd5Sign($data)
    {
        return $this->MD5Sign($data);
    }
}
