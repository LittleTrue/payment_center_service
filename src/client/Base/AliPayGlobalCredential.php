<?php
namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\RequestOptions;
use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * Class Config.
 */
class AliPayGlobalCredential extends BaseClient
{
    use MakesHttpRequests;

    private $_secretKey;

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->_secretKey = $this->app['config']->get('key');
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

        ksort($param);

        $string = '';

        foreach ($param as $key => $value) {
            if ('' != $value && 'sign' != $value && 'signType' != $key) {
                $string .= $key . '=' . $value . '&';
            }
        }

        $string = trim($string, '&');

        return strtolower(md5($string . $secret_key));
    }

    public function requestPost($param)
    {
        ksort($param);

        $string = '';

        foreach ($param as $key => $value) {
            if ('' != $value && 'sign' != $value && 'signType' != $key) {
                $string .= $key . '=' . $value . '&';
            }
        }

        $string = trim($string, '&');

        $options[RequestOptions::HEADERS] = $string;

        return $this->request('GET', $this->url, $options);
    }
}