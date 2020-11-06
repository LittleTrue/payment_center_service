<?php

namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\RequestOptions;
use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 底层请求.
 */
class BaseClient
{
    use MakesHttpRequests;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $json = [];

    /**
     * @var string
     */
    protected $language = 'zh-cn';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 获取特定位数时间戳.
     * @return int
     */
    public function getTimestamp($digits = 10)
    {
        $digits = $digits > 10 ? $digits : 10;

        $digits = $digits - 10;

        if ((!$digits) || (10 == $digits)) {
            return time();
        }

        return number_format(microtime(true), $digits, '', '') - 50000;
    }

    /**
     * 浮点数比较规则.
     * @return int
     */
    public function floatCmp($f1, $f2, $precision = 10)
    {
        $e  = pow(10, $precision);
        $i1 = intval($f1 * $e);
        $i2 = intval($f2 * $e);
        return $i1 == $i2;
    }

    /**
     * Set Headers Language params.
     *
     * @param string $language 请求头中的语种标识
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Make a get request.
     *
     * @throws ClientError
     */
    public function httpGet($uri, array $options = [])
    {
        $options = $this->_headers($options);

        return $this->request('GET', $uri, $options);
    }

    /**
     * Make a post request.
     *
     * @throws ClientError
     */
    public function httpPostJson($uri)
    {
        return $this->requestPost($uri, [RequestOptions::JSON => $this->json]);
    }

    /**
     * Set json params.
     *
     * @param array $json Json参数
     */
    public function setParams(array $json)
    {
        $time = $this->getTimestamp(13);

        //数据公共格式
        $param = [
            'data'          => json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'digest'        => $sign,
            'timestamp'     => $time,
            'customerCode'  => $declareConfig['customerCode'],
            'sitecode'      => $declareConfig['sitecode'],
            'version'       => 'V1',
            'serviceBeanId' => 'wmsComApiService'
        ];

        return $send_string;
    }

    /**
     * set Headers.
     *
     * @return array
     */
    private function _headers(array $options = [])
    {
        $time = time();

        $options[RequestOptions::HEADERS] = [
            'Content-Type' => 'application/json',
            'timestamp'    => $time,
        ];
        return $options;
    }

    //TODO -- 不同通路各自的请求方法

    /**
     * 微信通路请求方法
     * @throws ClientError
     */
    protected function weChatRequestPost($uri, array $options = [])
    {
        $options = $this->_headers($options);

        //微信验权/加签

        return $this->request('POST', $uri, $options);
    }

    /**
     * 支付宝通路请求方法
     * @throws ClientError
     */
    protected function aliPayRequestPost($uri, array $options = [])
    {
        $options = $this->_headers($options);

        //支付宝验权/加签

        return $this->request('POST', $uri, $options);
    }
}
