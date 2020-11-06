<?php

namespace paymentCenter\paymentClient\Base;

use paymentCenter\paymentClient\Application;

/**
 * Class Config.
 */
class AliPayGlobalCredential
{
    use MakesHttpRequests;

    private $_secretKey;

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->_secretKey = $this->app['config']->get('secretKey');
    }

    /**
     * Get request headers finally.
     */
    public function getRequestHeaders()
    {
        $time = time();
        return [
            'Content-Type' => 'application/json',
            'timestamp'    => $time,
        ];
    }

    /**
     * Get request params finally.
     */
    public function getRequestParams(array $params)
    {
        ksort($params);
        $send_data = [
            'data'      => json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'timestamp' => time(),
            'merchId'   => $params['merchId'],
        ];

        $sign_string       = $this->MD5Sign($send_data);
        $send_data['sign'] = $sign_string;

        return $send_data;
    }

    //进行MD5加签
    public function MD5Sign($param)
    {
        $secret_key = $this->app['config']->get('secretKey');
        $string     = $secret_key . 'data' . trim($param['data'], '"') . 'merchId' . $param['merchId'] . 'timestamp' . $param['timestamp'];
        return strtolower(md5($string));
    }
}