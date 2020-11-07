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
     * @var array
     */
    protected $param = [];

    /**
     * @var string
     */
    protected $language = 'zh-cn';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Set params.
     */
    public function setJsonParams(array $json)
    {
        $this->json = $json;
    }

    /**
     * Set params.
     */
    public function setParams(array $param)
    {
        $this->param = $param;
    }


    /**
     * 产生随机字符串，不长于32位.
     * @param  int                      $length
     * @return 产生的随机字符串
     */
    public function getNonceStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str   = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取毫秒级别的时间戳.
     */
    public function getMillisecond()
    {
        //获取毫秒的时间戳
        $time  = explode(' ', microtime());
        $time  = $time[1] . ($time[0] * 1000);
        $time2 = explode('.', $time);
        return $time2[0];
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
        $options[RequestOptions::HEADERS] = $this->getRequestHeaders();
        return $this->request('GET', $uri, $options);
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
     * @throws ClientError
     */
    protected function httpPostJson()
    {
        $options[RequestOptions::JSON]    = $this->json;
        $options[RequestOptions::HEADERS] = $this->getRequestHeaders();

        return $this->request('POST', $this->uri, $options);
    }
}
