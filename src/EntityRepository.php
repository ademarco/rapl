<?php

namespace RAPL\RAPL;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectRepository;
use RAPL\RAPL\Persister\EntityPersister;

class EntityRepository implements ObjectRepository
{
    /**
     * @var EntityPersister
     */
    protected $persister;

    /**
     * @var ClassMetadata
     */
    protected $classMetadata;

    /**
     * @param EntityPersister $persister
     * @param ClassMetadata   $classMetadata
     */
    public function __construct(EntityPersister $persister, ClassMetadata $classMetadata)
    {
        $this->persister     = $persister;
        $this->classMetadata = $classMetadata;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return object The object.
     */
    public function find($id)
    {
        return $this->persister->loadById(array('id' => $id));
    }

    /**
     * Finds all objects in the repository.
     *
     * @return array The objects.
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->persister->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return object The object.
     */
    public function findOneBy(array $criteria)
    {
        $results = $this->findBy($criteria);

        return isset($results[0]) ? $results[0] : null;
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->classMetadata->getName();
    }
}
