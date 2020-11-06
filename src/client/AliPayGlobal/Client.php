<?php

namespace paymentCenter\paymentClient\AliPayGlobal;

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

    private $method;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['validator'];
    }

}
