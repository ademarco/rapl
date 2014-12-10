<?php

namespace RAPL\RAPL;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use RAPL\RAPL\Repository\DefaultRepositoryFactory;
use RAPL\RAPL\Repository\RepositoryFactory;

class Configuration
{
    /**
     * @var array
     */
    private $entityNamespaces = array();

    /**
     * @var MappingDriver|null
     */
    private $mappingDriver;

    /**
     * @return RepositoryFactory
     */
    public function getRepositoryFactory()
    {
        return new DefaultRepositoryFactory();
    }

    /**
     * Adds a namespace under a certain alias
     *
     * @param string $alias
     * @param string $namespace
     */
    public function addEntityNamespace($alias, $namespace)
    {
        $this->entityNamespaces[$alias] = $namespace;
    }

    /**
     * Resolves a registered namespace alias to the full namespace
     *
     * @param string $alias
     *
     * @return string
     */
    public function getEntityNamespace($alias)
    {
        return trim($this->entityNamespaces[$alias], '\\');
    }

    /**
     * @param MappingDriver $mappingDriver
     */
    public function setMetadataDriver(MappingDriver $mappingDriver)
    {
        $this->mappingDriver = $mappingDriver;
    }

    /**
     * @return MappingDriver|null
     */
    public function getMetadataDriver()
    {
        return $this->mappingDriver;
    }
}
