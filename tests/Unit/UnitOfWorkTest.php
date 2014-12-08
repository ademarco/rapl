<?php

namespace RAPL\Tests\Unit;

use RAPL\RAPL\Connection\Connection;
use RAPL\RAPL\UnitOfWork;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
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
}
