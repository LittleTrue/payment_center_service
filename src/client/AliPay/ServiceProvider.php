<?php

namespace paymentCenter\paymentClient\AliPay;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['alipay'] = function ($app) {
            return new Client($app);
        };
    }
}
