<?php

namespace paymentCenter\paymentClient\WeChatGlobal;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['wechat_global'] = function ($app) {
            return new Client($app);
        };
    }
}
