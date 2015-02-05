<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\UriBuilder;

class UriBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUri()
    {
        $classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');

        $classMetadata->shouldReceive('getRoute')->withArgs(array('resource'))->andReturn('books/{id}.json')->once();
        $classMetadata->shouldReceive('getRoute')->withArgs(array('collection'))->andReturn('books.json')->once();

        $uriBuilder = new UriBuilder($classMetadata);

        $criteria = array(
            'id' => 4
        );

        $actual = $uriBuilder->createUri($criteria);
        $this->assertSame('books/4.json', $actual);

        $criteria = array();

        $actual = $uriBuilder->createUri($criteria);
        $this->assertSame('books.json', $actual);
    }
}
