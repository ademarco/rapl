<?php

namespace RAPL\RAPL\Persister;

use Guzzle\Http\Exception\ClientErrorResponseException;
use RAPL\RAPL\Connection\ConnectionInterface;
use RAPL\RAPL\EntityManagerInterface;
use RAPL\RAPL\Mapping\ClassMetadata;
use RAPL\RAPL\Serializer\Serializer;
use RAPL\RAPL\UriBuilder;

class BasicEntityPersister implements EntityPersister
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var UriBuilder
     */
    private $uriBuilder;

    /**
     * @param EntityManagerInterface $manager
     * @param ClassMetadata          $classMetadata
     */
    public function __construct(EntityManagerInterface $manager, ClassMetadata $classMetadata)
    {
        $this->manager       = $manager;
        $this->connection    = $manager->getConnection();
        $this->classMetadata = $classMetadata;

        $this->serializer = new Serializer($manager, $classMetadata);

        $this->uriBuilder = new UriBuilder($classMetadata);
    }

    /**
     * Loads an entity by a list of field criteria.
     *
     * @param array       $criteria The criteria by which to load the entity.
     * @param object|null $entity   The entity to load the data into. If not specified, a new entity is created.
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function load(array $criteria, $entity = null)
    {
        $uri     = $this->getUri($criteria);
        $request = $this->connection->createRequest('GET', $uri);

        try {
            $response = $this->connection->sendRequest($request);
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return null;
            } else {
                throw $e;
            }
        }

        $entities = $this->serializer->deserialize($response->getBody(true));

        return $entities ? $entities[0] : null;
    }

    /**
     * Loads an entity by identifier.
     *
     * @param array       $identifier The entity identifier.
     * @param object|null $entity     The entity to load the data into. If not specified, a new entity is created.
     *
     * @return object|null The loaded and managed entity instance or NULL if the entity can not be found.
     */
    public function loadById(array $identifier, $entity = null)
    {
        return $this->load($identifier, $entity);
    }

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
    public function loadAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $uri      = $this->getUri($criteria);
        $request  = $this->connection->createRequest('GET', $uri);
        $response = $this->connection->sendRequest($request);

        return $this->serializer->deserialize($response->getBody(true));
    }

    /**
     * Returns an URI based on a set of criteria
     *
     * @param array $criteria
     *
     * @return string
     */
    private function getUri(array $criteria)
    {
        return $this->uriBuilder->createUri($criteria);
    }
}
