<?php

namespace RAPL\Tests\Functional;

use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use RAPL\RAPL\Configuration;
use RAPL\RAPL\EntityManager;
use RAPL\RAPL\Mapping\Driver\YamlDriver;
use RAPL\Tests\Fixtures\Entities\Book;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        $json = '{"results": [{
            "id": 1,
            "title": "Winnie the Pooh",
            "isbn": "1234567890123"
        }]}';

        $response = new Response(200, array(), $json);

        $connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');
        $connection->shouldReceive('createRequest')->withArgs(array('GET', 'books/1'))->andReturn($request)->once();
        $connection->shouldReceive('sendRequest')->withArgs(array($request))->andReturn($response)->once();

        $configuration = new Configuration();
        $paths         = array(__DIR__ . '/../Fixtures/config');
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $manager    = new EntityManager($connection, $configuration);
        $repository = $manager->getRepository('RAPL\Tests\Fixtures\Entities\Book');

        /** @var Book $actual */
        $actual = $repository->find(1);

        $this->assertInstanceOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
        $this->assertSame('Winnie the Pooh', $actual->getTitle());
    }

    public function testFindNonExistingReturnsNull()
    {
        $request  = new Request('GET', 'books/1');
        $response = new Response(404, array());

        $connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');
        $connection->shouldReceive('createRequest')->withArgs(array('GET', 'books/1'))->andReturn($request)->once();

        $exception = ClientErrorResponseException::factory($request, $response);
        $connection->shouldReceive('sendRequest')->withArgs(array($request))->andThrow($exception);

        $configuration = new Configuration();
        $paths         = array(__DIR__ . '/../Fixtures/config');
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $manager    = new EntityManager($connection, $configuration);
        $repository = $manager->getRepository('RAPL\Tests\Fixtures\Entities\Book');

        $actual = $repository->find(1);

        $this->assertNull($actual);
    }

    public function testFindThrowsOtherExceptions()
    {
        $request  = new Request('GET', 'books/1');
        $response = new Response(403, array());

        $connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');
        $connection->shouldReceive('createRequest')->withArgs(array('GET', 'books/1'))->andReturn($request)->once();

        $exception = ClientErrorResponseException::factory($request, $response);
        $connection->shouldReceive('sendRequest')->withArgs(array($request))->andThrow($exception);

        $configuration = new Configuration();
        $paths         = array(__DIR__ . '/../Fixtures/config');
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $manager    = new EntityManager($connection, $configuration);
        $repository = $manager->getRepository('RAPL\Tests\Fixtures\Entities\Book');

        $this->setExpectedException('Guzzle\Http\Exception\ClientErrorResponseException');

        $repository->find(1);
    }

    public function testFindAll()
    {
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        $json = '{"results": [
    {
        "id": 1,
        "title": "Winnie the Pooh",
        "isbn": "1234567890123"
    },
    {
        "id": 2,
        "title": "Moby Dick",
        "isbn": "9876543210321"
    },
    {
        "id": 3,
        "title": "Harry Potter",
        "isbn": "1968132132980"
    }
]}';

        $response = new Response(200, array(), $json);

        $connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');
        $connection->shouldReceive('createRequest')->withArgs(array('GET', 'books'))->andReturn($request)->once();
        $connection->shouldReceive('sendRequest')->withArgs(array($request))->andReturn($response)->once();

        $configuration = new Configuration();
        $paths         = array(__DIR__ . '/../Fixtures/config');
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $manager    = new EntityManager($connection, $configuration);
        $repository = $manager->getRepository('RAPL\Tests\Fixtures\Entities\Book');

        $actual = $repository->findAll();

        $this->assertSame(3, count($actual));
        $this->assertContainsOnlyInstancesOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
    }

    public function testFindOneBy()
    {
        $request = \Mockery::mock('Guzzle\Http\Message\RequestInterface');

        $json = '{"results": [
    {
        "id": 1,
        "title": "Winnie the Pooh",
        "isbn": "1234567890123"
    },
    {
        "id": 2,
        "title": "Moby Dick",
        "isbn": "9876543210321"
    },
    {
        "id": 3,
        "title": "Harry Potter",
        "isbn": "1968132132980"
    }
]}';

        $response = new Response(200, array(), $json);

        $connection = \Mockery::mock('RAPL\RAPL\Connection\ConnectionInterface');
        $connection->shouldReceive('createRequest')->withArgs(array('GET', 'books'))->andReturn($request)->once();
        $connection->shouldReceive('sendRequest')->withArgs(array($request))->andReturn($response)->once();

        $configuration = new Configuration();
        $paths         = array(__DIR__ . '/../Fixtures/config');
        $driver        = new YamlDriver($paths, '.rapl.yml');
        $configuration->setMetadataDriver($driver);

        $manager    = new EntityManager($connection, $configuration);
        $repository = $manager->getRepository('RAPL\Tests\Fixtures\Entities\Book');

        /** @var Book $actual */
        $actual = $repository->findOneBy(array());

        $this->assertInstanceOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
        $this->assertSame('Winnie the Pooh', $actual->getTitle());
        $this->assertSame('1234567890123', $actual->getIsbn());
    }
}
