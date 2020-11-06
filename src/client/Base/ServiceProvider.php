<?php

namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\RequestOptions;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['http_client'] = function ($app) {
            return new GuzzleHttp([
                RequestOptions::TIMEOUT => 60,
            ]);
        };

        //注册参数验证器
        $app['validator'] = function ($app) {
            return new Validator($app);
        };

        //注册支付宝接口加签/验权机制
        $app['alipay_credential'] = function ($app) {
            return new AliPayGlobalCredential($app);
        };

        //注册微信接口加签/验权机制
        $app['wechat_credential'] = function ($app) {
            return new WeChatPayGlobalCredential($app);
        };
    }
}
