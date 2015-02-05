<?php

namespace RAPL\Tests\Unit\Mapping;

use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\Tests\Fixtures\Entities\Book;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $name = 'FooBar';

        $metadata = new ClassMetadata($name);

        $this->assertSame($name, $metadata->getName());
    }

    public function testGetReflectionClass()
    {
        $metadata = new ClassMetadata('RAPL\Tests\Fixtures\Entities\Book');
        $actual   = $metadata->getReflectionClass();
        $this->assertInstanceOf('ReflectionClass', $actual);

        $actual2 = $metadata->getReflectionClass();
        $this->assertSame($actual, $actual2);
    }

    public function testIsIdentifier()
    {
        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'fieldName' => 'id',
                'id'        => true
            )
        );
        $metadata->mapField(
            array(
                'fieldName' => 'foo'
            )
        );

        $this->assertTrue($metadata->isIdentifier('id'));
        $this->assertFalse($metadata->isIdentifier('foo'));
    }

    public function testHasField()
    {
        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'fieldName' => 'foo'
            )
        );

        $this->assertTrue($metadata->hasField('foo'));
        $this->assertFalse($metadata->hasField('bar'));
    }

    public function testGetFieldNames()
    {
        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'fieldName' => 'foo'
            )
        );

        $expected = array('foo');
        $this->assertSame($expected, $metadata->getFieldNames());
    }

    public function testGetFieldName()
    {
        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'fieldName'      => 'foo',
                'serializedName' => 'foobar_foo'
            )
        );

        $this->assertSame('foo', $metadata->getFieldName('foobar_foo'));
    }

    public function testGetFieldMapping()
    {
        $mapping = array(
            'fieldName'      => 'foo',
            'serializedName' => 'foo_bar',
            'type'           => 'integer'
        );

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField($mapping);

        $this->assertSame($mapping, $metadata->getFieldMapping('foo'));
    }

    public function testGetNonExistingFieldMappingThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $metadata = new ClassMetadata('FooBar');

        $metadata->getFieldMapping('fooBar');
    }

    public function testGetIdentifierFieldNames()
    {
        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'fieldName' => 'id',
                'type'      => 'integer',
                'id'        => true
            )
        );

        $this->assertEquals(array('id'), $metadata->getIdentifierFieldNames());
        $this->assertEquals(array('id'), $metadata->getIdentifier());
    }

    public function testGetTypeOfField()
    {
        $metadata = $this->getClassMetadata();

        $this->assertSame('string', $metadata->getTypeOfField('title'));
    }

    public function testGetNonExistingAssociationTargetClassThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $metadata = new ClassMetadata('FooBar');
        $metadata->getAssociationTargetClass('fooBar');
    }

    public function testIsAssociationInverseSide()
    {
        $this->markTestIncomplete();
    }

    public function testGetAssociationMappedByTargetField()
    {
        $this->markTestIncomplete();
    }

    public function testGetIdentifierValues()
    {
        $metadata = $this->getClassMetadata();

        $object = new Book();
        $object->setTitle('Foo Bar');
        $object->setIsbn('1234567890');

        $actual = $metadata->getIdentifierValues($object);
        $this->assertSame(array('id' => null), $actual);
    }

    public function testMapField()
    {
        $mapping = array(
            'fieldName' => 'test'
        );

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField($mapping);

        $this->assertTrue($metadata->hasField('test'));
        $this->assertSame($metadata->getTypeOfField('test'), 'string');
    }

    public function testMapFieldWithoutFieldNameThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapField(
            array(
                'type' => 'string'
            )
        );
    }

    public function testEmbedOne()
    {
        $fieldName = 'author';

        $mapping = array(
            'fieldName' => $fieldName,
            'targetEntity' => 'RAPL\Tests\Fixtures\Entities\Author'
        );

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapEmbedOne($mapping);

        $this->assertTrue($metadata->hasField($fieldName));
        $this->assertTrue($metadata->hasEmbed($fieldName));
        $this->assertTrue($metadata->hasAssociation($fieldName));
        $this->assertTrue($metadata->isSingleValuedAssociation($fieldName));
        $this->assertFalse($metadata->isCollectionValuedAssociation($fieldName));
        $this->assertSame(array('author'), $metadata->getAssociationNames());
        $this->assertSame('RAPL\Tests\Fixtures\Entities\Author', $metadata->getAssociationTargetClass($fieldName));
    }

    public function testMapEmbedOneWithoutFieldNameThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapEmbedOne(
            array(
                'targetEntity' => 'RAPL\Tests\Fixtures\Entities\Author'
            )
        );
    }

    public function testMapEmbeddedWithoutTargetEntityThrowsException()
    {
        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException');

        $metadata = new ClassMetadata('FooBar');
        $metadata->mapEmbedOne(
            array(
                'fieldName' => 'author'
            )
        );
    }

    public function testNewInstance()
    {
        $metadata = $this->getClassMetadata();

        $actual = $metadata->newInstance();

        $this->assertInstanceOf('RAPL\Tests\Fixtures\Entities\Book', $actual);
    }

    public function testSetGetFormat()
    {
        $format = 'xml';

        $metadata = $this->getClassMetadata();
        $metadata->setFormat($format);

        $actual = $metadata->getFormat();

        $this->assertSame($format, $actual);
    }

    public function testSetGetRoutes()
    {
        $metadata = $this->getClassMetadata();

        $this->assertFalse($metadata->hasRoute('resource'));
        $this->assertFalse($metadata->hasRoute('collection'));

        $this->assertNull($metadata->getRoute('resource'));

        $element = array(
          'resource' => array(
            'route' => 'books/{id}',
            'envelopes' => array('results', 0),
          ),
          'collection' => array(
            'route' => 'books',
            'envelopes' => array('results'),
          ),
        );

        $metadata->setRoute('resource', $element['resource']);
        $metadata->setRoute('collection', $element['collection']);

        $this->assertTrue($metadata->hasRoute('resource'));
        $this->assertTrue($metadata->hasRoute('collection'));

        $actual = $metadata->getRoute('collection');

        $this->assertSame('books', $actual);
    }

    public function testSetGetEnvelopes()
    {
        $metadata = $this->getClassMetadata();

        $element = array(
          'resource' => array(
            'route' => 'books/{id}',
            'envelopes' => array('results', 0),
          ),
          'collection' => array(
            'route' => 'books',
            'envelopes' => array('results'),
          ),
        );

        $metadata->setEnvelopes('resource', $element['resource']);
        $metadata->setEnvelopes('collection', $element['collection']);

        $this->assertSame($element['resource']['envelopes'], $metadata->getEnvelopes('resource'));
        $this->assertSame($element['collection']['envelopes'], $metadata->getEnvelopes('collection'));
    }

    public function testSetFieldValue()
    {
        $metadata = $this->getClassMetadata();

        $book = new Book();

        $metadata->setFieldValue($book, 'title', 'FooBar');
        $this->assertSame('FooBar', $book->getTitle());
    }

    /**
     * @return ClassMetadata
     */
    private function getClassMetadata()
    {
        $metadata = new ClassMetadata('RAPL\Tests\Fixtures\Entities\Book');
        $metadata->mapField(
            array(
                'fieldName' => 'id',
                'type'      => 'integer',
                'id'        => true
            )
        );
        $metadata->mapField(
            array(
                'fieldName' => 'title',
                'type'      => 'string'
            )
        );
        $metadata->mapField(
            array(
                'fieldName' => 'isbn',
                'type'      => 'string',
            )
        );

        $reflService = new RuntimeReflectionService();

        $metadata->initializeReflection($reflService);
        $metadata->wakeupReflection($reflService);

        return $metadata;
    }
}
