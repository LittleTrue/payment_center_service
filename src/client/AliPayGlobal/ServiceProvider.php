<?php

namespace paymentCenter\paymentClient\AliPayGlobal;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['alipay_global'] = function ($app) {
            return new Client($app);
        };
    }
}
