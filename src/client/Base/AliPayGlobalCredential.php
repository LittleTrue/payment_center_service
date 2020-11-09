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

    protected $_secretKey;

    protected $_partnerId;

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->_secretKey = $this->app['config']->get('key');
        $this->_partnerId = $this->app['config']->get('partner_id');
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
        ksort($param);

        $string = '';

        foreach ($param as $key => $value) {
            if ('' != $value && 'sign' != $value && 'signType' != $key) {
                $string .= $key . '=' . $value . '&';
            }
        }

        $string = trim($string, '&');

        return strtolower(md5($string . $this->_secretKey));
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

        $options[RequestOptions::TIMEOUT] = 30.0;

        file_put_contents('./response.html',$this->request('GET', $this->url . '?' . $string));

        //to do list
        //直接返回，然调用端解析响应。因为支付宝的不同接口，有不同格式的响应
        return 'OK';
    }
}