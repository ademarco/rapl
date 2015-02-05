<?php

namespace RAPL\Tests\Unit\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Mockery\MockInterface;
use RAPL\RAPL\EntityManager;
use RAPL\RAPL\Mapping\ClassMetadataFactory;

class ClassMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFqcnFromAlias()
    {
        $alias = 'Foo:Book';

        $mappingDriver = \Mockery::mock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver');
        $mappingDriver->shouldReceive('loadMetadataForClass');

        $entityManager = $this->createEntityManager($mappingDriver);

        /** @var MockInterface $config */
        $config = $entityManager->getConfiguration();
        $config->shouldReceive('getEntityNamespace')->withArgs(array('Foo'))->andReturn('RAPL\Tests\Fixtures\Entities')
            ->once();

        $classMetadataFactory = new ClassMetadataFactory();
        $classMetadataFactory->setEntityManager($entityManager);

        $classMetadataFactory->getMetadataFor($alias);
    }

    public function testDoLoadMetadataThrowsException()
    {
        $className = 'RAPL\Tests\Fixtures\Entities\Book';

        $mappingDriver = \Mockery::mock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver');
        $mappingDriver->shouldReceive('loadMetadataForClass')->andThrow('ReflectionException');

        $entityManager = $this->createEntityManager($mappingDriver);

        $classMetadataFactory = new ClassMetadataFactory();
        $classMetadataFactory->setEntityManager($entityManager);

        $exceptionMessage = sprintf('An error occured in %s', $className);

        $this->setExpectedException('RAPL\RAPL\Mapping\MappingException', $exceptionMessage);

        $classMetadataFactory->getMetadataFor($className);
    }

    /**
     * @param MappingDriver $mappingDriver
     *
     * @return EntityManager
     */
    protected function createEntityManager($mappingDriver)
    {
        $config = \Mockery::mock('RAPL\RAPL\Configuration');
        $config->shouldReceive('getMetadataDriver')->andReturn($mappingDriver);

        $entityManager = \Mockery::mock('RAPL\RAPL\EntityManager');
        $entityManager->shouldReceive('getConfiguration')->andReturn($config);

        return $entityManager;
    }
}
