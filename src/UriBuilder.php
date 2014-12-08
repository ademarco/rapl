<?php

namespace RAPL\RAPL;

use RAPL\RAPL\Mapping\ClassMetadata;

class UriBuilder
{
    /**
     * @var ClassMetadata
     */
    protected $classMetadata;

    /**
     * @param ClassMetadata $classMetadata
     */
    public function __construct(ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }

    /**
     * @param array $criteria
     *
     * @return string
     */
    public function createUri(array $criteria)
    {
        if (count($criteria) === 1 && array_key_exists('id', $criteria)) {
            $uri = $this->classMetadata->getRoute('resource');

            foreach ($criteria as $field => $value) {
                $uri = str_replace(sprintf('{%s}', $field), $value, $uri);
            }
        } else {
            $uri = $this->classMetadata->getRoute('collection');
        }

        return $uri;
    }
}
