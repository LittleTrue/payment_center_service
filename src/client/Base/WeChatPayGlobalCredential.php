<?php

namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\RequestOptions;
use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * 微信国际通路 -- 验权/加签, 初始化, 业务支持类.
 */
class WeChatPayGlobalCredential extends BaseClient
{
    use MakesHttpRequests;

    //通路参数
    protected $secretKey;

    protected $mchId;

    protected $appId;

    protected $apiClientCert;

    protected $apiClientKey;

    protected $custom;

    protected $customNo;

    protected $cert;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->app = $app;

        //初始化基础参数
        $this->secretKey = $this->app['config']->get('wx_key');
        $this->mchId     = $this->app['config']->get('wx_mchid');
        $this->appId     = $this->app['config']->get('wx_appid');

        //证书参数
        $this->apiClientCert = $this->app['config']->get('wx_apiclient_cert');
        $this->apiClientKey  = $this->app['config']->get('wx_apiclient_key');

        //报关参数
        $this->custom   = $this->app['config']->get('custom');
        $this->customNo = $this->app['config']->get('custom_no');

        $this->cert = $this->app['config']->get('cert');
    }

    /**
     * 生成签名.
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($values, $signType = 'MD5')
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->ToUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->secretKey;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if ('MD5' == $signType) {
            $string = md5($string);
        } elseif ('HMAC-SHA256' == $signType) {
            $string = hash_hmac('sha256', $string, $this->secretKey);
        } else {
            throw new ClientError('签名类型不支持！');
        }

        //签名步骤四：所有字符转为大写
        return strtoupper($string);
    }

    /**
     * 格式化参数格式化成url参数.
     */
    public function ToUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ('sign' != $k && '' != $v && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }

        return trim($buff, '&');
    }

    /**
     * (微信SDK) -- 输出xml字符.
     * @throws WxPayException
     */
    public function ToXml($param)
    {
        if (!is_array($param) || count($param) <= 0) {
            throw new ClientError('数组数据异常！');
        }

        $xml = '<xml>';
        foreach ($param as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * set Headers.
     *
     * @return array
     */
    public function setWxGlobalHeaders()
    {
        $time = time();

        return [
            'Content-Type' => 'text/xml; charset=UTF8',
            'timestamp'    => $time,
        ];
    }

    /**
     * post xml.
     *
     * @return array
     */
    public function requestXmlPost($xml, $certificateFlag = false)
    {
        if ($certificateFlag) {
            $this->setSslCert($this->apiClientCert);
            $this->setSslKey($this->apiClientKey);

            //设置PHP-PEM双向通讯证书
            $options = $this->setApiCert();
        }

        $options[RequestOptions::HEADERS] = $this->setWxGlobalHeaders();
        $options[RequestOptions::TIMEOUT] = 30.0;
        $options[RequestOptions::BODY]    = $xml;

        return $this->FromXml($this->request('POST', $this->url, $options));
    }

    /**
     * 解析回执参数 -- 判断通讯和业务状态.
     * @throws ClientError
     */
    public function parsingResponse($response)
    {
        //第一层 -- 请求及通讯层状态 -- 扔出错误, 该种类型的错误, 不需要业务参与, 属于调试阶段的对接问题
        if ('SUCCESS' != $response['return_code']) {
            foreach ($response as $key => $value) {
                //除了return_code和return_msg之外其他的参数存在，则报错
                if ('return_code' != $key && 'return_msg' != $key) {
                    throw new ClientError('返回数据存在异常！');
                }
            }

            throw new ClientError($response['return_msg']);
        }

        //第二层 -- 返回业务内容以供业务层解析
        return $response;
    }

    /**
     * (微信SDK) -- 将xml转为array.
     * @throws ClientError
     */
    public function FromXml($xmlResponse)
    {
        if (!$xmlResponse) {
            throw new ClientError('xml数据异常！');
        }

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xmlResponse, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 签名
     * 方法为 GET 时， body传空/其他方法传json格式字符串
     * method GET POST PUT
     */
    public function sign($body, $method)
    {
        $mch_private_key = file_get_contents($this->apiClientKey);
        $url_parts = parse_url($this->url);
        $nonce = $this->getNonceStr();
        $timestamp = time();

        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));

        switch ($method) {
            case 'GET':
                $body = '';
                break;
            case 'PUT':
            case 'POST':
                $body = json_encode($body);
                break;
            default:
                # code...
                break;
        }

        $message = $method ."\n".
            $canonical_url."\n".
            $timestamp ."\n".
            $nonce ."\n".
            $body."\n";

        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');

        $sign = base64_encode($raw_sign);
        
        $serial_no = openssl_x509_parse(file_get_contents($this->apiClientCert));

        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',$this->mchId, $nonce, $timestamp, $serial_no['serialNumberHex'], $sign);

        return 'WECHATPAY2-SHA256-RSA2048 ' . $token;
    }

    public function rsa($content)
    {
        openssl_public_encrypt($content, $encrypted, file_get_contents($this->cert));
        $encrypted = base64_encode($encrypted);  
        return $encrypted;
    }
}
