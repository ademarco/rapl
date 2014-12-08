<?php

namespace RAPL\RAPL\Mapping;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use RAPL\RAPL\EntityManager;

class ClassMetadataFactory extends AbstractClassMetadataFactory
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * Lazy initialization of this stuff, especially the metadata driver,
     * since these are not needed at all when a metadata cache is active.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->initialized = true;
    }

    /**
     * Gets the fully qualified class-name from the namespace alias.
     *
     * @param string $namespaceAlias
     * @param string $simpleClassName
     *
     * @return string
     */
    protected function getFqcnFromAlias($namespaceAlias, $simpleClassName)
    {
        return $this->manager->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
    }

    /**
     * Returns the mapping driver implementation.
     *
     * @return \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     */
    protected function getDriver()
    {
        return $this->manager->getConfiguration()->getMetadataDriver();
    }

    /**
     * Wakes up reflection after ClassMetadata gets unserialized from cache.
     *
     * @param ClassMetadataInterface $class
     * @param ReflectionService      $reflService
     *
     * @return void
     */
    protected function wakeupReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
        /* @var $class ClassMetadata */
        $class->wakeupReflection($reflService);
    }

    /**
     * Initializes Reflection after ClassMetadata was constructed.
     *
     * @param ClassMetadataInterface $class
     * @param ReflectionService      $reflService
     *
     * @return void
     */
    protected function initializeReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
        /* @var $class ClassMetadata */
        $class->initializeReflection($reflService);
    }

    /**
     * Checks whether the class metadata is an entity.
     *
     * This method should return false for mapped superclasses or embedded classes.
     *
     * @param ClassMetadataInterface $class
     *
     * @return boolean
     */
    protected function isEntity(ClassMetadataInterface $class)
    {
        // TODO: Implement isEntity() method.
    }

    /**
     * Actually loads the metadata from the underlying metadata.
     *
     * @param ClassMetadata      $classMetadata
     * @param ClassMetadata|null $parent
     * @param bool               $rootEntityFound
     * @param array              $nonSuperclassParents All parent class names
     *                                                 that are not marked as mapped superclasses.
     *
     * @throws MappingException
     *
     * @return void
     */
    protected function doLoadMetadata($classMetadata, $parent, $rootEntityFound, array $nonSuperclassParents)
    {
        try {
            $this->getDriver()->loadMetadataForClass($classMetadata->getName(), $classMetadata);
        } catch (\ReflectionException $e) {
            throw MappingException::reflectionFailure($classMetadata->getName(), $e);
        }
    }

    /**
     * Creates a new ClassMetadata instance for the given class name.
     *
     * @param string $className
     *
     * @return ClassMetadata
     */
    protected function newClassMetadataInstance($className)
    {
        return new ClassMetadata($className);
    }
}
