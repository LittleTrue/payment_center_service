<?php

namespace paymentCenter\paymentClient\WeChat;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['wechat'] = function ($app) {
            return new Client($app);
        };
    }
}
