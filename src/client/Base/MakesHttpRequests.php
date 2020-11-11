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
     * @throws ClientError
     */
    public function request($method, $url, array $options = [])
    {
        $response = $this->app['http_client']->request($method, $url, $options);

        return $this->transformResponse($response);
    }

    /**
     * @throws ClientError
     */
    protected function transformResponse(Response $response)
    {
        if (200 != $response->getStatusCode()) {
            throw new ClientError(
                "接口连接异常，异常码：{$response->getStatusCode()}, 异常信息:" . $response->getBody()->getContents(),
                $response->getStatusCode()
            );
        }
        return $response->getBody()->getContents();
    }
}
