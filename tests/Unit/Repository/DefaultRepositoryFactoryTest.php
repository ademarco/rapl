<?php

namespace RAPL\Tests\Unit\Repository;

use RAPL\RAPL\Repository\DefaultRepositoryFactory;

class DefaultRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRepository()
    {
        $className = 'SomeClass';

        $metadata = \Mockery::mock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->shouldReceive('getName')->andReturn($className);

        $persister = \Mockery::mock('RAPL\RAPL\Persister\EntityPersister');

        $unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWork->shouldReceive('getEntityPersister')->withArgs(array($className))->andReturn($persister)->once();

        $manager = \Mockery::mock('RAPL\RAPL\EntityManagerInterface');
        $manager->shouldReceive('getClassMetadata')->withArgs(array($className))->andReturn($metadata);
        $manager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork)->once();

        $factory = new DefaultRepositoryFactory();

        $actual = $factory->getRepository($manager, $className);
        $this->assertInstanceOf('Doctrine\Common\Persistence\ObjectRepository', $actual);

        // test that we receive the same instance again
        $actual2 = $factory->getRepository($manager, $className);
        $this->assertSame($actual, $actual2);
    }
}
