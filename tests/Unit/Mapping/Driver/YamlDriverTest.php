<?php

namespace RAPL\Tests\Unit\Mapping\Driver;

use RAPL\RAPL\Mapping\Driver\YamlDriver;

class YamlDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadMetadataForClass()
    {
        $className = 'RAPL\Tests\Fixtures\Entities\Book';

        $paths  = array(__DIR__ . '/../../../Fixtures/config/');
        $driver = new YamlDriver($paths);

        $metadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $metadata->shouldReceive('setFormat')->withArgs(array('json'))->once();
        $metadata->shouldReceive('setRoutes')->withArgs(array(array('resource' => 'books/{id}', 'collection' => 'books')))->once();
        $metadata->shouldReceive('setEnvelopes')->withArgs(array(array('resource' => array('results'), 'collection' => array('results'))))->once();

        $metadata->shouldReceive('mapField')->withArgs(
            array(
                array(
                    'fieldName'      => 'id',
                    'type'           => 'integer',
                    'serializedName' => null,
                    'id'             => true
                )
            )
        )->once();
        $metadata->shouldReceive('mapField')->withArgs(
            array(
                array(
                    'fieldName'      => 'title',
                    'type'           => 'string',
                    'serializedName' => null
                )
            )
        )->once();
        $metadata->shouldReceive('mapField')->withArgs(
            array(
                array(
                    'fieldName'      => 'isbn',
                    'type'           => null,
                    'serializedName' => null
                )
            )
        )->once();

        $metadata->shouldReceive('mapEmbedOne')->withArgs(
            array(
                array(
                    'targetEntity'   => 'RAPL\Tests\Fixtures\Entities\Author',
                    'fieldName'      => 'author',
                    'serializedName' => null
                )
            )
        )->once();

        $driver->loadMetadataForClass($className, $metadata);
    }
}
