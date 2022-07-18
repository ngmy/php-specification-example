<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Doctrine\Entity;

/**
 * Role user entity.
 */
class RoleUser
{
    public string $id;

    public string $roleId;

    public string $userId;
}
