<?php

namespace paymentCenter\paymentClient\Base;

use GuzzleHttp\Psr7\Response;
use onion\OnionK3cClient\Base\Exceptions\ClientError;

/**
 * Trait MakesHttpRequests.
 */
trait MakesHttpRequests
{
    /**
     * @var bool
     */
    protected $transform = true;

    /**
     * @var string
     */
    protected $baseUri = '';

    /**
     * @throws ClientError
     */
    public function request($method, $uri, array $options = [])
    {
        $uri = $this->app['config']->get('base_uri') . $uri;

        $response = $this->app['http_client']->request($method, $uri, $options);

        return $this->transform ? $this->transformResponse($response) : $response;
    }

    /**
     * @throws ClientError
     */
    protected function transformResponse(Response $response)
    {
        if (200 != $response->getStatusCode()) {
            throw new ClientError(
                "接口连接异常，异常码：{$response->getStatusCode()}，异常信息:" . json_decode($response->getBody()->getContents(), true),
                $response->getStatusCode()
            );
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
