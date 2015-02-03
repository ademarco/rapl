<?php

namespace RAPL\RAPL\Serializer;

use RAPL\RAPL\EntityManagerInterface;
use RAPL\RAPL\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ClassMetadata          $classMetadata
     */
    public function __construct(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->manager       = $entityManager;
        $this->classMetadata = $classMetadata;

        $normalizers      = array(new GetSetMethodNormalizer());
        $encoders         = array(new JsonEncoder());
        $this->serializer = new SymfonySerializer($normalizers, $encoders);
    }

    /**
     * Deserializes the data
     *
     * Decodes the serialized data, and then hydrates the entities.
     *
     * @param string $data
     * @param string $type Entity type, either 'resource' or 'collection'
     *
     * @return array
     */
    public function deserialize($data, $type = 'collection')
    {
        $data     = $this->decode($data);
        $elements = $this->unwrap($data, $type);
        $elements = ($type == 'collection') ? $elements : array($elements);

        $entities = array();

        foreach ($elements as $elementData) {
            $elementData = $this->mapFromSerialized($elementData);

            $this->hydrateSingleEntity($elementData, $entities);
        }

        return $entities;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function mapFromSerialized(array $data)
    {
        $mappedEntityData = array();

        foreach ($data as $serializedName => $value) {
            if ($this->classMetadata->hasField($this->classMetadata->getFieldName($serializedName))) {
                $fieldName    = $this->classMetadata->getFieldName($serializedName);
                $fieldMapping = $this->classMetadata->getFieldMapping($fieldName);

                if (isset($fieldMapping['association'])) {
                    $embedded = array();

                    $associationMetadata   = $this->manager->getClassMetadata($fieldMapping['targetEntity']);
                    $associationSerializer = new Serializer($this->manager, $associationMetadata);

                    if ($fieldMapping['association'] === ClassMetadata::EMBED_ONE) {
                        if (is_array($value)) {
                            $associationData = $associationSerializer->mapFromSerialized($value);
                            $associationSerializer->hydrateSingleEntity($associationData, $embedded);

                            $value = reset($embedded);
                        } else {
                            $value = null;
                        }
                    }
                } else {
                    switch ($fieldMapping['type']) {
                        case 'string':
                            if (!is_null($value)) {
                                $value = (string) $value;
                            }
                            break;

                        case 'integer':
                            if (!is_null($value)) {
                                $value = (int) $value;
                            }
                            break;

                        case 'boolean':
                            if (!is_null($value)) {
                                $value = (bool) $value;
                            }
                            break;

                        case 'datetime':
                            if (!is_null($value)) {
                                $value = new \DateTime($value);
                            }
                            break;

                        default:
                            $value = null;
                    }
                }

                $mappedEntityData[$fieldName] = $value;
            }
        }

        return $mappedEntityData;
    }

    /**
     * @param array $data
     * @param array $result
     */
    private function hydrateSingleEntity(array $data, array &$result)
    {
        $entity   = $this->manager->getUnitOfWork()->createEntity($this->classMetadata->getName(), $data);
        $result[] = $entity;
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function decode($data)
    {
        return $this->serializer->decode($data, $this->classMetadata->getFormat());
    }

    /**
     * Unwraps the data from containers
     *
     * @param array $data
     * @param string $type Entity type, either 'resource' or 'collection'
     *
     * @return array
     */
    private function unwrap(array $data, $type = 'collection')
    {
        $containers = $this->classMetadata->getEnvelopes($type);

        foreach ($containers as $container) {
            if (isset($data[$container])) {
                $data = $data[$container];
            }
        }
        return $data;
    }
}
