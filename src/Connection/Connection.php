<?php

namespace RAPL\RAPL\Connection;

use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->httpClient = new Client($baseUrl);
    }

    /**
     * @param string $method
     * @param string $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri)
    {
        return $this->httpClient->createRequest($method, $uri);
    }

    /**
     * @param RequestInterface $request
     *
     * @return Response
     */
    public function sendRequest(RequestInterface $request)
    {
        return $response = $request->send();
    }

    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->httpClient->addSubscriber($subscriber);
    }
}
