<?php

namespace RAPL\RAPL\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use InvalidArgumentException;

class ClassMetadata implements ClassMetadataInterface
{
    const EMBED_ONE = 1;

    const EMBED_MANY = 2;

    const REFERENCE_ONE = 3;

    const REFERENCE_MANY = 4;

    /**
     * The name of the entity class.
     *
     * @var string
     */
    private $name;

    /**
     * The namespace the entity class is contained in.
     *
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $fieldMappings = array();

    /**
     * An array of field names, used to look up field names from serialized names.
     * Keys are column names and values are field names.
     *
     * @var array
     */
    private $fieldNames = array();

    /**
     * The association mappings of the class.
     * Keys are field names and values are mapping definitions.
     *
     * @var array
     */
    private $associationMappings = array();

    /**
     * The field names of all fields that are part of the identifier / primary key.
     *
     * @var array
     */
    private $identifierFieldNames = array();

    /**
     * The ReflectionClass instance of the mapped class.
     *
     * @var \ReflectionClass
     */
    private $reflClass;

    /**
     * The ReflectionProperty instances of the mapped class.
     *
     * @var \ReflectionProperty[]
     */
    private $reflFields = array();

    /**
     * The prototype from which new instances of the mapped class are created.
     *
     * @var object
     */
    private $prototype;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $routes = array();

    /**
     * The envelope(s) where the result is wrapped into
     *
     * @var array
     */
    private $envelopes = array();

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->name = $className;
    }

    /**
     * Gets the fully-qualified class name of this persistent class.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the mapped identifier field name.
     *
     * The returned structure is an array of the identifier field names.
     *
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifierFieldNames;
    }

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        if (!$this->reflClass) {
            $this->reflClass = new \ReflectionClass($this->name);
        }

        return $this->reflClass;
    }

    /**
     * Checks if the given field name is a mapped identifier for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isIdentifier($fieldName)
    {
        return in_array($fieldName, $this->identifierFieldNames);
    }

    /**
     * Checks if the given field is a mapped property for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]);
    }

    /**
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasEmbed($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]['embedded']);
    }

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasAssociation($fieldName)
    {
        return $this->hasEmbed($fieldName);
    }

    /**
     * Checks whether the class has a mapped embedded document for the specified field
     * and if yes, checks whether it is a single-valued association (to-one).
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function isSingleValuedEmbed($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]['association']) &&
        $this->fieldMappings[$fieldName]['association'] === self::EMBED_ONE;
    }

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return $this->isSingleValuedEmbed($fieldName);
    }

    /**
     * Checks whether the class has a mapped embedded document for the specified field
     * and if yes, checks whether it is a collection-valued association (to-many).
     *
     * @param string $fieldName
     *
     * @return boolean TRUE if the association exists and is collection-valued, FALSE otherwise.
     */
    public function isCollectionValuedEmbed($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]['association']) &&
        $this->fieldMappings[$fieldName]['association'] === self::EMBED_MANY;
    }

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return $this->isCollectionValuedEmbed($fieldName);
    }

    /**
     * A numerically indexed list of field names of this persistent class.
     *
     * This array includes identifier fields if present on this class.
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->fieldMappings);
    }

    /**
     * Gets the field name for a serialized name.
     * If no field name can be found the serialized name is returned.
     *
     * @param string $serializedName
     *
     * @return string
     */
    public function getFieldName($serializedName)
    {
        return isset($this->fieldNames[$serializedName]) ? $this->fieldNames[$serializedName] : $serializedName;
    }

    /**
     * Gets the mapping of a (regular) field that holds some data but not a
     * reference to another object.
     *
     * @param string $fieldName The field name.
     *
     * @return array The field mapping.
     *
     * @throws MappingException
     */
    public function getFieldMapping($fieldName)
    {
        if (!isset($this->fieldMappings[$fieldName])) {
            throw MappingException::mappingNotFound($this->name, $fieldName);
        }

        return $this->fieldMappings[$fieldName];
    }

    /**
     * Returns an array of identifier field names numerically indexed.
     *
     * @return array
     */
    public function getIdentifierFieldNames()
    {
        return $this->identifierFieldNames;
    }

    /**
     * Returns a numerically indexed list of association names of this persistent class.
     *
     * This array includes identifier associations if present on this class.
     *
     * @return array
     */
    public function getAssociationNames()
    {
        return array_keys($this->associationMappings);
    }

    /**
     * Returns a type name of this field.
     *
     * This type names can be implementation specific but should at least include the php types:
     * integer, string, boolean, float/double, datetime.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getTypeOfField($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]) ? $this->fieldMappings[$fieldName]['type'] : null;
    }

    /**
     * Returns the target class name of the given association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationTargetClass($assocName)
    {
        if (!isset($this->associationMappings[$assocName])) {
            throw new InvalidArgumentException(
                sprintf("Association name expected, '%s' is not an association.", $assocName)
            );
        }

        return $this->associationMappings[$assocName]['targetEntity'];
    }

    /**
     * Checks if the association is the inverse side of a bidirectional association.
     *
     * @param string $assocName
     *
     * @return boolean
     */
    public function isAssociationInverseSide($assocName)
    {
        // TODO: Implement isAssociationInverseSide() method.
    }

    /**
     * Returns the target field of the owning side of the association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        // TODO: Implement getAssociationMappedByTargetField() method.
    }

    /**
     * Returns the identifier of this object as an array with field name as key.
     *
     * Has to return an empty array if no identifier isset.
     *
     * @param object $object
     *
     * @return array
     */
    public function getIdentifierValues($object)
    {
        $fieldName = $this->identifierFieldNames[0];
        $value     = $this->reflFields[$fieldName]->getValue($object);

        return array($fieldName => $value);
    }

    /**
     * Adds a mapped field to the class
     *
     * @param array $mapping The field mapping
     */
    public function mapField(array $mapping)
    {
        $this->validateAndCompleteFieldMapping($mapping);

        $this->fieldMappings[$mapping['fieldName']] = $mapping;

        if (isset($mapping['association'])) {
            $this->associationMappings[$mapping['fieldName']] = $mapping;
        }
    }

    /**
     * @param array $mapping
     *
     * @throws MappingException
     */
    public function mapEmbedOne(array $mapping)
    {
        $this->validateAndCompleteAssociationMapping($mapping);

        $mapping['embedded'] = true;
        $mapping['type']     = 'one';

        $this->mapField($mapping);
    }

    /**
     * @param array $mapping
     *
     * @throws MappingException
     */
    private function validateAndCompleteFieldMapping(array &$mapping)
    {
        if (!isset($mapping['fieldName']) || strlen($mapping['fieldName']) == 0) {
            throw MappingException::missingFieldName($this->name);
        }

        if (!isset($mapping['type'])) {
            // Default to string
            $mapping['type'] = 'string';
        }

        if (!isset($mapping['serializedName'])) {
            $mapping['serializedName'] = $mapping['fieldName'];
        }

        $this->fieldNames[$mapping['serializedName']] = $mapping['fieldName'];

        if (isset($mapping['id']) && $mapping['id'] === true) {
            if (!in_array($mapping['fieldName'], $this->identifierFieldNames)) {
                $this->identifierFieldNames[] = $mapping['fieldName'];
            }
        }

        if (isset($mapping['embedded']) && $mapping['type'] === 'one') {
            $mapping['association'] = self::EMBED_ONE;
        }
    }

    /**
     * @param array $mapping
     *
     * @throws MappingException
     */
    private function validateAndCompleteAssociationMapping(array &$mapping)
    {
        if (!isset($mapping['targetEntity'])) {
            throw MappingException::missingTargetEntity($mapping['fieldName']);
        }
    }

    /**
     * Creates a new instance of the mapped class, without invoking the constructor.
     *
     * @return object
     */
    public function newInstance()
    {
        if ($this->prototype === null) {
            $this->prototype = unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->name), $this->name));
        }

        return clone $this->prototype;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @param $type
     * @param array $element
     */
    public function setRoute($type, array $element)
    {
        if (isset($element['route'])) {
            $this->routes[$type] = $element['route'];
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasRoute($type)
    {
        return isset($this->routes[$type]);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    public function getRoute($type)
    {
        if ($this->hasRoute($type)) {
            return $this->routes[$type];
        }

        return null;
    }

    /**
     * @param $type
     * @return array
     */
    public function getEnvelopes($type)
    {
        if ($this->hasEnvelope($type)) {
            return $this->envelopes[$type];
        }

        return array();
    }

    /**
     * @param $type
     * @param array $element
     */
    public function setEnvelopes($type, array $element)
    {
        if (isset($element['envelopes'])) {
            $this->envelopes[$type] = $element['envelopes'];
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasEnvelope($type)
    {
        return isset($this->envelopes[$type]);
    }

    /**
     * Sets the specified field to the specified value on the given entity.
     *
     * @param object $entity
     * @param string $field
     * @param mixed  $value
     *
     * @return void
     */
    public function setFieldValue($entity, $field, $value)
    {
        $this->reflFields[$field]->setValue($entity, $value);
    }

    /**
     * Initializes a new ClassMetadata instance that will hold the object-relational mapping
     * metadata of the class with the given name.
     *
     * @param ReflectionService $reflService The reflection service.
     *
     * @return void
     */
    public function initializeReflection($reflService)
    {
        $this->reflClass = $reflService->getClass($this->name);
        $this->namespace = $reflService->getClassNamespace($this->name);

        if ($this->reflClass) {
            $this->name = $this->rootEntityName = $this->reflClass->getName();
        }
    }

    /**
     * Restores some state that can not be serialized/unserialized.
     *
     * @param ReflectionService $reflService
     *
     * @return void
     */
    public function wakeupReflection($reflService)
    {
        // Restore ReflectionClass and properties
        $this->reflClass = $reflService->getClass($this->name);

        foreach ($this->fieldMappings as $field => $mapping) {
            $this->reflFields[$field] = $reflService->getAccessibleProperty($this->name, $field);
        }
    }
}
