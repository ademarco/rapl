<?php

namespace RAPL\RAPL;

use Doctrine\Common\Persistence\ObjectManager;
use RAPL\RAPL\Connection\ConnectionInterface;

interface EntityManagerInterface extends ObjectManager
{
    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * @return Configuration
     */
    public function getConfiguration();

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork();
}
