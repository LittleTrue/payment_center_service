<?php

namespace paymentCenter\paymentService;

use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * Rsa加签.
 */
class RsaSignService
{
    /**
     * @var RsaSign
     */
    private $_rsaClient;

    public function __construct(Application $app)
    {
        $this->_rsaClient = $app['rsa'];
    }

    /**
     * 报文RSA加签.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function sign($key, $xml)
    {
        if (empty($key) || empty($xml)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_rsaClient->sign($key, $xml);
    }
}
