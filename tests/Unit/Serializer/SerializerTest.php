<?php

namespace RAPL\Tests\Unit\Serializer;

use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Serializer\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserialize()
    {
        $entityName = 'Foo\Bar';

        $entity = \Mockery::mock();

        $manager = \Mockery::mock('RAPL\RAPL\EntityManager');

        $unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWork->shouldReceive('createEntity')->andReturn($entity);

        $manager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);

        $classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $classMetadata->shouldReceive('getName')->andReturn($entityName);
        $classMetadata->shouldReceive('getFormat')->andReturn('json');
        $classMetadata->shouldReceive('getEnvelopes')->andReturn(array('results'));

        $classMetadata->shouldReceive('hasField')->andReturn('true');

        $classMetadata->shouldReceive('getFieldName')->withArgs(array('string'))->andReturn('string');
        $classMetadata->shouldReceive('getFieldName')->withArgs(array('integer'))->andReturn('integer');
        $classMetadata->shouldReceive('getFieldName')->withArgs(array('boolean'))->andReturn('boolean');
        $classMetadata->shouldReceive('getFieldName')->withArgs(array('datetime'))->andReturn('datetime');
        $classMetadata->shouldReceive('getFieldName')->withArgs(array('embedOne'))->andReturn('embedOne');
        $classMetadata->shouldReceive('getFieldName')->withArgs(array('unknown'))->andReturn('unknown');

        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('string'))->andReturn(
            array(
                'type' => 'string'
            )
        );
        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('integer'))->andReturn(
            array(
                'type' => 'integer'
            )
        );
        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('boolean'))->andReturn(
            array(
                'type' => 'boolean'
            )
        );
        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('datetime'))->andReturn(
            array(
                'type' => 'datetime'
            )
        );
        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('embedOne'))->andReturn(
            array(
                'embedded'     => true,
                'type'         => 'one',
                'association'  => ClassMetadata::EMBED_ONE,
                'targetEntity' => 'Foo\BarBaz'
            )
        );
        $classMetadata->shouldReceive('getFieldMapping')->withArgs(array('unknown'))->andReturn(
            array(
                'type' => 'foobarbaz'
            )
        );

        $embeddedClassMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $embeddedClassMetadata->shouldReceive('getName')->andReturn('Foo\BarBaz');
        $embeddedClassMetadata->shouldReceive('getFieldName')->withArgs(array('foo'))->andReturn('foo');
        $embeddedClassMetadata->shouldReceive('hasField')->withArgs(array('foo'))->andReturn(true);
        $embeddedClassMetadata->shouldReceive('getFieldMapping')->withArgs(array('foo'))->andReturn(
            array(
                'type' => 'string'
            )
        );

        $manager->shouldReceive('getClassMetadata')->withArgs(array('Foo\BarBaz'))->andReturn($embeddedClassMetadata);

        $serializer = new Serializer($manager, $classMetadata);

        $data = '{"results":[{
            "string": "Foo Bar",
            "integer": 5,
            "boolean": false,
            "datetime": "2014-12-10 14:32:01",
            "embedOne": {
                "foo": "Bar"
            },
            "unknown": "adsf"
        }]}';

        $actual = $serializer->deserialize($data);
        $this->assertSame(1, count($actual));
        $this->assertSame($entity, $actual[0]);
    }
}
