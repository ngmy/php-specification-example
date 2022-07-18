<?php

declare(strict_types=1);

namespace Tests\TestCase\Infrastructure\Domain\Model\User;

use Doctrine\ORM\EntityManager;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\User\UserRepositoryInterface;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Domain\Model\Role\DoctrineRoleRepository;
use Infrastructure\Domain\Model\User\DoctrineUserRepository;
use Infrastructure\Persistence\Doctrine\EntityManagerFactory;

/**
 * @internal
 * @coversDefaultClass \Infrastructure\Domain\Model\User\DoctrineUserRepository
 */
class DoctrineUserRepositoryTest extends AbstractUserRepositoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUserRepository(): UserRepositoryInterface
    {
        $entityManager = $this->createEntitymanager();

        return new DoctrineUserRepository($entityManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoleRepository(): RoleRepositoryInterface
    {
        $entityManager = $this->createEntitymanager();

        return new DoctrineRoleRepository($entityManager);
    }

    /**
     * Create entity manager.
     */
    private function createEntitymanager(): EntityManager
    {
        /** @var Connection */
        $connection = DB::connection();
        $connection = $connection->getDoctrineConnection();

        return (new EntityManagerFactory())->create($connection);
    }
}
