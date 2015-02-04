<?php

namespace RAPL\RAPL\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;
use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends FileDriver
{
    const DEFAULT_FILE_EXTENSION = '.rapl.yml';

    /**
     * @param string|array|FileLocator $locator
     * @param string                   $fileExtension
     */
    public function __construct($locator, $fileExtension = self::DEFAULT_FILE_EXTENSION)
    {
        parent::__construct($locator, $fileExtension);
    }

    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string        $className
     * @param ClassMetadata $metadata
     *
     * @return void
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /** @var $metadata \RAPL\RAPL\Mapping\ClassMetadata */
        $element = $this->getElement($className);

        if (isset($element['format'])) {
            $metadata->setFormat($element['format']);
        }

        if (isset($element['resource'])) {
            $metadata->setRoute('resource', $element['resource']);
            $metadata->setEnvelopes('resource', $element['resource']);
        }

        if (isset($element['collection'])) {
            $metadata->setRoute('collection', $element['collection']);
            $metadata->setEnvelopes('collection', $element['collection']);
        }

        if (isset($element['identifiers'])) {
            foreach ($element['identifiers'] as $fieldName => $fieldElement) {
                $metadata->mapField(
                    array(
                        'fieldName'      => $fieldName,
                        'type'           => (isset($fieldElement['type'])) ? $fieldElement['type'] : null,
                        'serializedName' => (isset($fieldElement['serializedName'])) ? (string) $fieldElement['serializedName'] : null,
                        'id'             => true
                    )
                );
            }
        }

        if (isset($element['fields'])) {
            foreach ($element['fields'] as $fieldName => $mapping) {
                $metadata->mapField(
                    array(
                        'fieldName'      => $fieldName,
                        'type'           => (isset($mapping['type'])) ? $mapping['type'] : null,
                        'serializedName' => (isset($mapping['serializedName'])) ? (string) $mapping['serializedName'] : null
                    )
                );
            }
        }

        if (isset($element['embedOne'])) {
            foreach ($element['embedOne'] as $fieldName => $embedElement) {
                $metadata->mapEmbedOne(
                    array(
                        'targetEntity'   => (string) $embedElement['targetEntity'],
                        'fieldName'      => $fieldName,
                        'serializedName' => (isset($embedElement['serializedName'])) ? (string) $embedElement['serializedName'] : null
                    )
                );
            }
        }
    }

    /**
     * Loads a mapping file with the given name and returns a map
     * from class/entity names to their corresponding file driver elements.
     *
     * @param string $file The mapping file to load.
     *
     * @return array
     */
    protected function loadMappingFile($file)
    {
        return Yaml::parse($file);
    }
}
