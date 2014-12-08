<?php

namespace RAPL\RAPL\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use RAPL\RAPL\EntityManagerInterface;
use RAPL\RAPL\EntityRepository;

class DefaultRepositoryFactory implements RepositoryFactory
{
    /**
     * @var ObjectRepository[]
     */
    private $repositories;

    /**
     * Gets the repository for a class
     *
     * @param EntityManagerInterface $manager
     * @param string                 $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository(EntityManagerInterface $manager, $entityName)
    {
        $className = $manager->getClassMetadata($entityName)->getName();

        if (isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }

        return $this->repositories[$className] = $this->createRepository($manager, $entityName);
    }

    /**
     * Creates a new repository instance for an entity class
     *
     * @param EntityManagerInterface $manager
     * @param string                 $entityName
     *
     * @return EntityRepository
     */
    protected function createRepository(EntityManagerInterface $manager, $entityName)
    {
        $classMetadata = $manager->getClassMetadata($entityName);

        return new EntityRepository($manager->getUnitOfWork()->getEntityPersister($classMetadata->getName()), $classMetadata);
    }
}
