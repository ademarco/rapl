<?php

namespace RAPL\RAPL\Mapping;

use RAPL\RAPL\RAPLException;

class MappingException extends \Exception implements RAPLException
{
    /**
     * @param string $className
     * @param string $fieldName
     *
     * @return MappingException
     */
    public static function mappingNotFound($className, $fieldName)
    {
        return new self("No mapping found for field '$fieldName' on class '$className'.");
    }

    /**
     * @param string $entity
     *
     * @return MappingException
     */
    public static function missingFieldName($entity)
    {
        return new self(
            sprintf("The field or association mapping misses the 'fieldName' attribute in entity '%s'.", $entity)
        );
    }

    /**
     * @param $fieldName
     *
     * @return MappingException
     */
    public static function missingTargetEntity($fieldName)
    {
        return new self(sprintf("The association mapping '%s' misses the 'targetEntity' attribute.", $fieldName));
    }

    /**
     * Exception for reflection exceptions - adds the entity name,
     * because there might be long classnames that will be shortened
     * within the stacktrace.
     *
     * @param string               $entity The entity's name
     * @param \ReflectionException $exception
     *
     * @return MappingException
     */
    public static function reflectionFailure($entity, \ReflectionException $exception)
    {
        return new self(sprintf('An error occured in %s', $entity), 0, $exception);
    }
}
