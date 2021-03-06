<?php

namespace RAPL\RAPL;

use Doctrine\Common\Persistence\ObjectManager;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Persister\BasicEntityPersister;
use RAPL\RAPL\Persister\EntityPersister;

class UnitOfWork
{
    /**
     * An entity is in MANAGED state when its persistence is managed by an EntityManager.
     */
    const STATE_MANAGED = 1;

    /**
     * An entity is new if it has just been instantiated (i.e. using the "new" operator)
     * and is not (yet) managed by an EntityManager.
     */
    const STATE_NEW = 2;

    /**
     * A detached entity is an instance with persistent state and identity that is not
     * (or no longer) associated with an EntityManager (and a UnitOfWork).
     */
    const STATE_DETACHED = 3;

    /**
     * A removed entity instance is an instance with a persistent identity,
     * associated with an EntityManager, whose persistent state will be deleted
     * on commit.
     */
    const STATE_REMOVED = 4;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * The entity persister instances used to persist entity instances
     *
     * @var array
     */
    private $persisters = array();

    /**
     * The identity map that holds references to all managed entities that have
     * an identity. The entities are grouped by their class name.
     * Since all classes in a hierarchy must share the same identifier set,
     * we always take the root class name of the hierarchy.
     *
     * @var array
     */
    private $identityMap = array();

    /**
     * Map of all identifiers of managed entities.
     * Keys are object ids (spl_object_hash).
     *
     * @var array
     */
    private $entityIdentifiers = array();

    /**
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param null|object $entity
     */
    public function commit($entity = null)
    {
        // TODO: Implement commit() method.
    }

    /**
     * Persists an entity as part of the current unit of work.
     *
     * @param object $entity The entity to persist.
     */
    public function persist($entity)
    {
        // TODO: Implement persist() method.
    }

    /**
     * Deletes an entity as part of the current unit of work.
     *
     * @param object $entity The entity to remove.
     */
    public function remove($entity)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Merges the state of the given detached entity into this UnitOfWork.
     *
     * @param object $entity
     *
     * @return object The managed copy of the entity.
     */
    public function merge($entity)
    {
        // TODO: Implement merge() method.
    }

    /**
     * Clears the UnitOfWork.
     *
     * @param string|null $entityName if given, only entities of this type will get detached.
     */
    public function clear($entityName = null)
    {
        // TODO: Implement merge() method.
    }

    /**
     * Detaches an entity from the persistence management. It's persistence will
     * no longer be managed by Doctrine.
     *
     * @param object $entity The entity to detach.
     *
     * @return void
     */
    public function detach($entity)
    {
        // TODO: Implement detach() method.
    }

    /**
     * Refreshes the state of the given entity from the database, overwriting
     * any local, unpersisted changes.
     *
     * @param object $entity The entity to refresh.
     *
     * @return void
     */
    public function refresh($entity)
    {
        // TODO: Implement refresh() method.
    }

    /**
     * Gets the EntityPersister for an Entity
     *
     * @param string $entityName
     *
     * @return EntityPersister
     */
    public function getEntityPersister($entityName)
    {
        if (isset($this->persisters[$entityName])) {
            return $this->persisters[$entityName];
        }

        $classMetadata = $this->manager->getClassMetadata($entityName);

        $persister = new BasicEntityPersister($this->manager, $classMetadata);

        $this->persisters[$entityName] = $persister;

        return $persister;
    }

    /**
     * Checks wether the entity is in the identity map
     *
     * @param $entity
     *
     * @return bool
     */
    public function isInIdentityMap($entity)
    {
        $oid = spl_object_hash($entity);

        if (!isset($this->entityIdentifiers[$oid])) {
            return false;
        }

        $classMetadata = $this->manager->getClassMetadata(get_class($entity));
        $idHash        = implode(' ', $this->entityIdentifiers[$oid]);

        return isset($this->identityMap[$classMetadata->getName()][$idHash]);
    }

    /**
     * Adds this entity to the identity map
     *
     * @param object $entity
     *
     * @return bool
     */
    public function addToIdentityMap($entity)
    {
        $classMetadata = $this->manager->getClassMetadata(get_class($entity));
        $idHash        = implode(' ', $this->entityIdentifiers[spl_object_hash($entity)]);

        $className = $classMetadata->getName();

        if (isset($this->identityMap[$className][$idHash])) {
            return false;
        }

        $this->identityMap[$className][$idHash] = $entity;

        return true;
    }

    /**
     * Removes the entity from the identity map
     *
     * @param $entity
     *
     * @return bool
     */
    public function removeFromIdentityMap($entity)
    {
        $oid           = spl_object_hash($entity);
        $classMetadata = $this->manager->getClassMetadata(get_class($entity));
        $idHash        = implode(' ', $this->entityIdentifiers[$oid]);

        $className = $classMetadata->getName();

        if (isset($this->identityMap[$className][$idHash])) {
            unset($this->identityMap[$className][$idHash]);

            return true;
        }

        return false;
    }

    /**
     * Creates an entity and fills it with the provided data
     *
     * @param string $className
     * @param array  $data
     *
     * @return object
     */
    public function createEntity($className, array $data)
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->manager->getClassMetadata($className);

        $idHash = $data['id'];
        $id     = array('id' => $data['id']);

        if (isset($this->identityMap[$className][$idHash])) {
            $entity = $this->identityMap[$className][$idHash];
        } else {
            $entity = $this->newInstance($classMetadata);
            $oid    = spl_object_hash($entity);

            $this->entityIdentifiers[$oid] = $id;

            $this->addToIdentityMap($entity);
        }

        foreach ($data as $field => $value) {
            if ($classMetadata->hasField($field)) {
                $classMetadata->setFieldValue($entity, $field, $value);
            }
        }

        return $entity;
    }

    /**
     * Returns a new instance of the given class.
     *
     * @param ClassMetadata $classMetadata
     *
     * @return mixed
     */
    private function newInstance(ClassMetadata $classMetadata)
    {
        $entity = $classMetadata->newInstance();

        return $entity;
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * @param object $obj
     *
     * @return void
     */
    public function initializeObject($obj)
    {
        //
    }

    /**
     * @param object $object
     *
     * @return boolean
     */
    public function isScheduledForInsert($object)
    {
        // TODO implement
    }

    /**
     * @param object $object
     *
     * @return boolean
     */
    public function isScheduledForDelete($object)
    {
        // TODO implement
    }
}
