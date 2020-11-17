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
            if ('' != $value && 'sign' != $key && 'sign_type' != $key) {
                $string .= $key . '=' . $value . '&';
            }
        }

        $string = trim($string, '&');

        return strtolower(md5($string . $this->_secretKey));
    }

    /**
     * 组建访问URL
     */
    public function buildRequestUrl($param)
    {
        // ksort($param);

        $string = '';

        foreach ($param as $key => $value) {
            if ('' != $value) {
                $string .= $key . '=' . $value . '&';
            }
        }

        $string = trim($string, '&');

        return $this->url . '?' . $string;
    }

    public function requestPost($url = '')
    {
        if ('' == $url) {
            throw new ClientError('支付宝错误提示：请求url为空');
        }
        
        $options[RequestOptions::TIMEOUT] = 30.0;
        
        $response = $this->request('GET', $url);

        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $response, true)) {
            xml_parser_free($xml_parser);
            
            return $response;
        } else {
            $response = $this->FromXml($response);

            if ('T' == $response['is_success']) {
                return $response;
            } else {
                throw new ClientError('支付宝错误提示：' . $response['error']);
            }
        }
    }

    /**
     * 将xml转为array.
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
}