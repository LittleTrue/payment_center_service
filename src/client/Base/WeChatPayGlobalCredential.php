<?php

namespace paymentCenter\paymentClient\Base;

use paymentCenter\paymentClient\Application;

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
        $this->app        = $app;
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
     * set Headers.
     *
     * @return array
     */
    private function setWxGlobalHeaders()
    {
        


        return $options;
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
}
