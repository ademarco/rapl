<?php

namespace RAPL\Tests\Unit\Connection;

use RAPL\RAPL\Connection\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $baseUrl    = 'http://example.com/api/';
        $connection = new Connection($baseUrl);

        $actual = $connection->createRequest('GET', 'foo/bar');

        $this->assertInstanceOf('Guzzle\Http\Message\RequestInterface', $actual);
        $this->assertSame('http://example.com/api/foo/bar', $actual->getUrl());
    }

    public function testSendRequest()
    {
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');
        $request->shouldReceive('send');

        $baseUrl    = 'http://example.com/api/';
        $connection = new Connection($baseUrl);

        $connection->sendRequest($request);
    }

    public function testAddSubscriber()
    {
        $subscriber = \Mockery::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $subscriber->shouldReceive('getSubscribedEvents')->andReturn(array());

        $connection = new Connection('http://example.com/api/');

        $connection->addSubscriber($subscriber);
    }
}
