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

        $element = array(
            'resource'   => array(
                'route'     => 'books/{id}',
                'envelopes' => array('results', 0),
            ),
            'collection' => array(
                'route'     => 'books',
                'envelopes' => array('results'),
            ),
        );
        $metadata->shouldReceive('setRoute')->withArgs(array('resource', $element['resource']))->once();
        $metadata->shouldReceive('setEnvelopes')->withArgs(array('resource', $element['resource']))->once();

        $metadata->shouldReceive('setRoute')->withArgs(array('collection', $element['collection']))->once();
        $metadata->shouldReceive('setEnvelopes')->withArgs(array('collection', $element['collection']))->once();

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
