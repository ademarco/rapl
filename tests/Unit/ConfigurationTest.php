<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRepositoryFactory()
    {
        $configuration = new Configuration();

        $actual = $configuration->getRepositoryFactory();
        $this->assertInstanceOf('RAPL\RAPL\Repository\RepositoryFactory', $actual);
    }

    public function testSetGetEntityNamespace()
    {
        $configuration = new Configuration();

        $configuration->addEntityNamespace('TestNamespace', __NAMESPACE__);
        $this->assertSame(__NAMESPACE__, $configuration->getEntityNamespace('TestNamespace'));
    }

    public function testSetGetMappingDriver()
    {
        $configuration = new Configuration();

        $mappingDriverMock = \Mockery::mock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver');

        $configuration->setMetadataDriver($mappingDriverMock);

        $actual = $configuration->getMetadataDriver();

        $this->assertSame($mappingDriverMock, $actual);
    }
}
