<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

/**
 * Factory class for the Doctrine entity manager.
 */
class EntityManagerFactory
{
    /**
     * Create a new entity manager from a connection.
     *
     * @param Connection $connection connection
     *
     * @return EntityManager a new entity manager
     */
    public function create(Connection $connection): EntityManager
    {
        $config = new Configuration();
        $config->setProxyDir('Proxies');
        $config->setProxyNamespace('App\Proxies');

        $driverImpl = new XmlDriver([__DIR__.'/Mapping']);
        $config->setMetadataDriverImpl($driverImpl);

        return EntityManager::create($connection, $config);
    }
}
