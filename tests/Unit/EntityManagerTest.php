<?php

namespace RAPL\Tests\Unit;

use Doctrine\Common\EventManager;
use RAPL\RAPL\Configuration;
use RAPL\RAPL\Connection\Connection;
use RAPL\RAPL\EntityManager;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\Tests\Mocks\EntityManagerMock;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $connection = new Connection('http://example.com/api');
        $config     = new Configuration();
        $manager    = new EntityManager($connection, $config);

        $this->assertSame($config, $manager->getConfiguration());
    }

    public function testFind()
    {
        $id     = 'http://example.com/objects/1';
        $object = new \stdClass();

        $repositoryMock = \Mockery::mock('RAPL\RAPL\ResourceRepository');
        $repositoryMock->shouldReceive('find')->withArgs(array($id))->andReturn($object)->once();

        $repositoryFactoryMock = \Mockery::mock('RAPL\RAPL\Repository\RepositoryFactory');
        $repositoryFactoryMock->shouldReceive('getRepository')->andReturn($repositoryMock)->once();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $configMock->shouldReceive('getRepositoryFactory')->andReturn($repositoryFactoryMock)->once();

        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManager($connection, $configMock);
        $actual     = $manager->find('SomeClass', $id);

        $this->assertSame($object, $actual);
    }

    public function testPersist()
    {
        $object = new \stdClass();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('persist')->withArgs(array($object))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->persist($object);
    }

    public function testRemove()
    {
        $object = new \stdClass();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('remove')->withArgs(array($object))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->remove($object);
    }

    public function testMerge()
    {
        $object        = new \stdClass();
        $managedObject = new \stdClass();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('merge')->withArgs(array($object))->andReturn($managedObject)->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $actual = $manager->merge($object);

        $this->assertSame($managedObject, $actual);
    }

    public function testClear()
    {
        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('clear')->withArgs(array(null))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->clear();
    }

    public function testClearSpecificEntity()
    {
        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('clear')->withArgs(array('EntityName'))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->clear('EntityName');
    }

    public function testDetach()
    {
        $object = new \stdClass();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('detach')->withArgs(array($object))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->detach($object);
    }

    public function testRefresh()
    {
        $object = new \stdClass();

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('refresh')->withArgs(array($object))->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->refresh($object);
    }

    public function testFlush()
    {
        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $unitOfWorkMock = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $unitOfWorkMock->shouldReceive('commit')->once();

        $manager->setUnitOfWork($unitOfWorkMock);

        $manager->flush();
    }

    public function testGetRepository()
    {
        $className = 'SomeClass';

        $repositoryFactoryMock = \Mockery::mock('RAPL\RAPL\Repository\RepositoryFactory');

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $configMock->shouldReceive('getRepositoryFactory')->andReturn($repositoryFactoryMock)->once();
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManager($connection, $configMock);

        $repositoryMock = \Mockery::mock('RAPL\RAPL\ResourceRepository');

        $repositoryFactoryMock->shouldReceive('getRepository')->withArgs(array($manager, $className))->andReturn(
            $repositoryMock
        )->once();

        $actual = $manager->getRepository($className);
        $this->assertSame($repositoryMock, $actual);
    }

    public function testGetMetadataFactory()
    {
        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManager($connection, $configMock);

        $this->assertInstanceOf('RAPL\RAPL\Mapping\ClassMetadataFactory', $manager->getMetadataFactory());
    }

    public function testGetClassMetadata()
    {
        $className = 'SomeClass';

        $configMock = \Mockery::mock('RAPL\RAPL\Configuration');
        $connection = new Connection('http://example.com/api');
        $manager    = new EntityManagerMock($connection, $configMock);

        $classMetadata = new ClassMetadata($className);

        $classMetadataFactoryMock = \Mockery::mock('RAPL\RAPL\Mapping\ClassMetadataFactory');
        $classMetadataFactoryMock->shouldReceive('getMetadataFor')->withArgs(array($className))->andReturn(
            $classMetadata
        )->once();
        $manager->setMetadataFactory($classMetadataFactoryMock);

        $this->assertSame($classMetadata, $manager->getClassMetadata($className));
    }

    public function testInitializeObject()
    {
        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');
        $config     = \Mockery::mock('RAPL\RAPL\Configuration');
        $unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $obj        = new \stdClass();

        $manager = new EntityManagerMock($connection, $config);
        $unitOfWork->shouldReceive('initializeObject')->withArgs(array($obj))->once();

        $manager->setUnitOfWork($unitOfWork);

        $manager->initializeObject($obj);
    }

    public function testContains()
    {
        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');
        $config     = \Mockery::mock('RAPL\RAPL\Configuration');
        $unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');
        $obj        = new \stdClass();

        $manager = new EntityManagerMock($connection, $config);
        $unitOfWork->shouldReceive('isScheduledForInsert')->withArgs(array($obj))->andReturn(false)->once();
        $unitOfWork->shouldReceive('isInIdentityMap')->withArgs(array($obj))->andReturn(true)->once();
        $unitOfWork->shouldReceive('isScheduledForDelete')->withArgs(array($obj))->andReturn(false)->once();

        $manager->setUnitOfWork($unitOfWork);

        $this->assertTrue($manager->contains($obj));
    }

    public function testGetConnection()
    {
        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');
        $config     = \Mockery::mock('RAPL\RAPL\Configuration');

        $manager = new EntityManager($connection, $config);

        $this->assertSame($connection, $manager->getConnection());
    }

    public function testGetConfiguration()
    {
        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');
        $config     = \Mockery::mock('RAPL\RAPL\Configuration');

        $manager = new EntityManager($connection, $config);

        $this->assertSame($config, $manager->getConfiguration());
    }

    public function testGetUnitOfWork()
    {
        $connection = \Mockery::mock('RAPL\RAPL\Connection\Connection');
        $config     = \Mockery::mock('RAPL\RAPL\Configuration');
        $unitOfWork = \Mockery::mock('RAPL\RAPL\UnitOfWork');

        $manager = new EntityManagerMock($connection, $config);
        $manager->setUnitOfWork($unitOfWork);

        $this->assertSame($unitOfWork, $manager->getUnitOfWork());
    }
}
