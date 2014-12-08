<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\EntityRepository;
use RAPL\Tests\Fixtures\Entities\Book;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $object = new Book();

        $persister  = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');
        $persister->shouldReceive('loadById')->withArgs(array(array('id' => 3)))->andReturn($object);

        $metadata   = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $repository = new EntityRepository($persister, $metadata);

        $this->assertSame($object, $repository->find(3));
    }

    public function testFindAll()
    {
        $persister  = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');
        $metadata   = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $repository = new EntityRepository($persister, $metadata);

        $result = array(
            new Book()
        );

        $persister->shouldReceive('loadAll')->withArgs(array(array(), null, null, null))->andReturn($result)->once();

        $actual = $repository->findAll();

        $this->assertSame($result, $actual);
    }

    public function testFindBy()
    {
        $persister  = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');
        $metadata   = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $repository = new EntityRepository($persister, $metadata);

        $result = array(
            new Book()
        );

        $criteria = array('id' => 3);
        $orderBy  = array('name' => 'asc');
        $limit    = 10;
        $offset   = 20;

        $persister->shouldReceive('loadAll')->withArgs(array($criteria, $orderBy, $limit, $offset))->andReturn($result)
            ->once();

        $actual = $repository->findBy($criteria, $orderBy, $limit, $offset);

        $this->assertSame($result, $actual);
    }

    public function testFindOneBy()
    {
        $persister  = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');
        $metadata   = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $repository = new EntityRepository($persister, $metadata);

        $object = new Book();

        $result = array(
            $object
        );

        $criteria = array('id' => 3);

        $persister->shouldReceive('loadAll')->withArgs(array($criteria, null, null, null))->andReturn($result)->once();

        $actual = $repository->findOneBy($criteria);

        $this->assertSame($object, $actual);
    }

    public function testGetClassName()
    {
        $className = 'FooBar';

        $persister = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');
        $metadata  = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $metadata->shouldReceive('getName')->andReturn($className)->once();

        $repository = new EntityRepository($persister, $metadata);

        $this->assertSame($className, $repository->getClassName());
    }
}
