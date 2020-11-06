<?php
/**
 *  @department : Commercial development.
 *  @description : This file is part of [DZ Purchase].
 *  DZ all rights reserved.
 */

namespace paymentCenter;

//统一服务调用工厂
class CustomsTradePostFactory
{
    /**
     * 获取服务的工厂类, 使用反射机制的优雅实现.
     *
     * @throws \Exception
     */
    public function getInstance($className, $args)
    {
        if (class_exists('\\paymentCenter\\paymentService\\' . $className)) {
            return (new \ReflectionClass('\\paymentCenter\\paymentService\\' . $className))->newInstance($args);
        }
        throw new \Exception('class not found!');
    }
}
