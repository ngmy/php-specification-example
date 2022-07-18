<?php

declare(strict_types=1);

namespace Tests\TestCase\Infrastructure\Domain\Model\User;

use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\User\UserRepositoryInterface;
use Infrastructure\Domain\Model\Role\EloquentRoleRepository;
use Infrastructure\Domain\Model\User\EloquentUserRepository;

/**
 * @internal
 * @coversDefaultClass \Infrastructure\Domain\Model\User\EloquentUserRepository
 */
class EloquentUserRepositoryTest extends AbstractUserRepositoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUserRepository(): UserRepositoryInterface
    {
        return new EloquentUserRepository();
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoleRepository(): RoleRepositoryInterface
    {
        return new EloquentRoleRepository();
    }
}
