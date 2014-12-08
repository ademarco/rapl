<?php

namespace RAPL\Tests\Mocks;

use RAPL\RAPL\EntityManager;
use RAPL\RAPL\Mapping\ClassMetadataFactory;
use RAPL\RAPL\UnitOfWork;

class EntityManagerMock extends EntityManager
{
    /**
     * @param ClassMetadataFactory $factory
     */
    public function setMetadataFactory(ClassMetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function setUnitOfWork(UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }
}
