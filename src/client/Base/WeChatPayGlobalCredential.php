<?php

namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\RequestOptions;
use paymentCenter\paymentClient\Application;
use paymentCenter\paymentClient\Base\Exceptions\ClientError;

/**
 * Class Config.
 */
class WeChatPayGlobalCredential extends BaseClient
{
    use MakesHttpRequests;

    private $_secretKey;

    private $_mchId;

    public function __construct(Application $app)
    {
        $this->app = $app;
        //初始化各种参数
        $this->_secretKey = $this->app['config']->get('wx_key');
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
        $string = $string . '&key=' . $this->_secretKey;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if ('MD5' == $signType) {
            $string = md5($string);
        } elseif ('HMAC-SHA256' == $signType) {
            $string = hash_hmac('sha256', $string, $this->_secretKey);
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
            //设置PHP-PEM双向通讯证书
            $options = $this->setApiCert();
        }

        $options[RequestOptions::HEADERS] = $this->setWxGlobalHeaders();
        $options[RequestOptions::TIMEOUT] = 30.0;
        $options[RequestOptions::BODY]    = $xml;

        return $this->FromXml($this->request('POST', $this->url, $options));
    }

    /**
     * (微信SDK) -- 将xml转为array.
     * @throws WxPayException
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
}
