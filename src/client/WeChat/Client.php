<?php

namespace paymentCenter\paymentClient\WeChat;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\BaseClient;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    /**
     * @var Application
     */
    protected $credentialValidate;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['validator'];
    }

    /**
     * 调拨单同步.
     */
    public function qrCodePay(array $infos)
    {
        //参数验证
        $rule = [
            'customerCode' => 'require',
            'sitecode'     => 'require',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('传输配置' . $this->credentialValidate->getError());
        }

        $this->checkInfo($infos);
        
        $send_data = $this->setParams($infos, $this->method);

        return $this->httpGet('?' . $send_data);
    }
}
