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
     * @var string
     */
    public $url;

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
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $language = 'zh-cn';

    /**
     * @var string SSL证书 PHP版本 CERT  PEM
     */
    private $_sslCert;

    /**
     * @var string SSL证书 PHP版本 KEY PEM
     */
    private $_sslKey;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->headers = [
            'Content-Type'  => 'application/json',
            'timestamp'     => time(),
            'User-Agent'    => '',
            'Accept'        => '*/*'
        ];
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
     * Set SslCert.
     */
    public function setSslCert(string $cert)
    {
        $this->_sslCert = $cert;
    }

    /**
     * Set SslKey.
     */
    public function setSslKey(string $key)
    {
        $this->_sslKey = $key;
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
     * 证书文件字符串转化为临时文件路径.
     */
    public function getTmpPathByContent($content)
    {
        static $tmpFile = null;
        $tmpFile        = tmpfile();
        fwrite($tmpFile, $content);
        $tempPemPath = stream_get_meta_data($tmpFile);
        return $tempPemPath['uri'];
    }

    /**
     * Get request headers finally.
     */
    public function getRequestHeaders()
    {
        return $this->headers;
    }



    /**
     * @throws ClientError
     */
    protected function httpPostJson()
    {
        $options[RequestOptions::JSON]    = $this->json;
        $options[RequestOptions::HEADERS] = $this->getRequestHeaders();

        return $this->request('POST', $this->url, $options);
    }

    /**
     * set SSL CERT.
     *
     * @return array
     */
    protected function setApiCert()
    {
        //识别路径或者字符串, 如果是字符串还需进行换行, 过滤处理
        if (empty($this->_sslCert) || empty($this->_sslKey)) {
            throw new ClientError('证书文件缺失。');
        }

        if (!is_file($this->_sslCert)) {
            $cert_key_str = chunk_split($this->_sslCert, 64, "\n");

            $cert_key_data = "-----BEGIN RSA PRIVATE KEY-----\n{$cert_key_str}-----END RSA PRIVATE KEY-----\n";

            $this->_sslCert = $this->getTmpPathByContent($cert_key_data);
        }

        if (!is_file($this->_sslKey)) {
            $ssl_key_str = chunk_split($this->_sslKey, 64, "\n");

            $ssl_key_data = "-----BEGIN RSA PRIVATE KEY-----\n{$ssl_key_str}-----END RSA PRIVATE KEY-----\n";

            $this->_sslCert = $this->getTmpPathByContent($ssl_key_data);
        }

        $options[RequestOptions::SSL_KEY] = $this->_sslKey;
        $options[RequestOptions::CERT]    = $this->_sslCert;

        return $options;
    }

    public function setHeaders(array $param)
    {
        $this->headers[key($param)] = $param[key($param)];
    }
}
