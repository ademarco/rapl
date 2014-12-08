<?php

namespace RAPL\RAPL\Persister;

interface EntityPersister
{
    /**
     * Loads an entity by a list of field criteria.
     *
     * @param array       $criteria The criteria by which to load the entity.
     * @param object|null $entity   The entity to load the data into. If not specified, a new entity is created.
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function load(array $criteria, $entity = null);

    /**
     * Loads an entity by identifier.
     *
     * @param array       $identifier The entity identifier.
     * @param object|null $entity     The entity to load the data into. If not specified, a new entity is created.
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function loadById(array $identifier, $entity = null);

    /**
     * Loads a list of entities by a list of field criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array
     */
    public function loadAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null);
}
