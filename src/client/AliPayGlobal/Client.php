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

    private $method;

    private $request_url = 'https://mapi.alipaydev.com/gateway.do';

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['validator'];
    }

    /**
     * 扫码支付
     */
    public function qrCodePay($data, $signType = 'MD5')
    {
        $this->url = $this->request_url;

        //验证参数
        // $this->credentialValidate->setRule(
        //     [
        //         'subject' => 'require|max:256',
        //         'body' => 'max:400',
        //         'out_trade_no' => 'require|max:64',
        //         'currency' => 'require|max:10',
        //         'total_fee' => 'require|number',
        //         'rmb_fee' => 'require|number',
        //         'refer_url' => 'require',
        //         'product_code' => 'require|max:32',
        //     ]
        // );

        // if (!$this->credentialValidate->check($data)) {
        //     throw new ClientError('参数错误：'.$this->credentialValidate->getError());
        // }

        if (!isset($data['trade_information']) || empty($data['trade_information'])) {
            throw new ClientError('参数错误：缺少trade_information');
        }

        //在验证trade_information
        // $this->credentialValidate->setRule(
        //     [
        //         'business_type' => 'require|max:1',
        //         'goods_info' => 'require',
        //         'total_quantity' => 'require|number'
        //     ]
        // );

        // if (!$this->credentiaValidate->check($data['trade_information'])) {
        //     throw new ClientError('参数错误：'.$this->credentialValidate->getError());
        // }

        //生成发送数据
        $param = [
            'service' => 'create_forex_trade',
            'partner' => $this->app['config']['partner_id'],
            '_input_charset' => 'UTF-8',
            'sign_type' => 'MD5',
            'subject' => $data['subject'],
            'body' => isset($data['body']) ? $data['body'] : '',
            'out_trade_no' => $data['out_trade_no'],
            'currency' => $data['currency'],
            'total_fee' => $data['total_fee'],
            'rmb_fee' => $data['rmb_fee'],
            'refer_url' =>  $data['refer_url'],
            'product_code' => $data['product_code'],
            'trade_information' => json_encode($data['trade_information'])
        ];

        $param['sign'] = $this->MD5Sign($param);

        //发送请求
        return $this->requestPost($param);
    }

}
 