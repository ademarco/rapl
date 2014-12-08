<?php

namespace RAPL\RAPL\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use RAPL\RAPL\EntityManagerInterface;

interface RepositoryFactory
{
    /**
     * Gets the repository for an entity class.
     *
     * @param EntityManagerInterface $manager
     * @param string                 $entityName The name of the entity.
     *
     * @return ObjectRepository
     */
    public function getRepository(EntityManagerInterface $manager, $entityName);
}
