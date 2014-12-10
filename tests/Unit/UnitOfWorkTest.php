<?php

namespace RAPL\Tests\Unit;

use Mockery\MockInterface;
use RAPL\RAPL\Connection\Connection;
use RAPL\RAPL\EntityManager;
use RAPL\RAPL\UnitOfWork;
use RAPL\Tests\Fixtures\Entities\Author;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager|MockInterface
     */
    protected $manager;

    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    protected function setUp()
    {
        $this->manager = \Mockery::mock('RAPL\RAPL\EntityManager');

        $this->unitOfWork = new UnitOfWork($this->manager);
    }

    public function testGetEntityPersister()
    {
        $className     = 'RAPL\Tests\Entities\Library\Book';
        $classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');

        $connection = new Connection('http://example.com');

        $manager = \Mockery::mock('RAPL\RAPL\EntityManager');
        $manager->shouldReceive('getClassMetadata')->withArgs(array($className))->andReturn($classMetadata)->once();
        $manager->shouldReceive('getConnection')->andReturn($connection)->once();

        $unitOfWork = new UnitOfWork($manager);

        $actual = $unitOfWork->getEntityPersister($className);
        $this->assertInstanceOf('RAPL\RAPL\Persister\EntityPersister', $actual);

        $second = $unitOfWork->getEntityPersister($className);
        $this->assertSame($actual, $second);
    }

    public function testIdentityMap()
    {
        $className = 'RAPL\Tests\Entities\Library\Author';

        $classMetadata = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadata');
        $classMetadata->shouldReceive('newInstance')->andReturn(new Author());
        $classMetadata->shouldReceive('hasField')->withArgs(array('id'))->andReturn(true);
        $classMetadata->shouldReceive('hasField')->withArgs(array('name'))->andReturn(true);
        $classMetadata->shouldReceive('setFieldValue');
        $classMetadata->shouldReceive('getName')->andReturn($className);

        $this->manager->shouldReceive('getClassMetadata')->andReturn($classMetadata);

        $data = array(
            'id' => 123,
            'name' => 'Foo Bar'
        );

        $entityA = $this->unitOfWork->createEntity($className, $data);
        $entityB = new Author();

        $this->assertTrue($this->unitOfWork->isInIdentityMap($entityA));
        $this->assertFalse($this->unitOfWork->isInIdentityMap($entityB));

        $this->assertFalse($this->unitOfWork->addToIdentityMap($entityA));

        $this->assertTrue($this->unitOfWork->removeFromIdentityMap($entityA));
        $this->assertFalse($this->unitOfWork->isInIdentityMap($entityA));
        $this->assertFalse($this->unitOfWork->removeFromIdentityMap($entityA));
    }
}
