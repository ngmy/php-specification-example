<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\ORM\PersistentCollection;
use Domain\Model\User\Sex;
use Domain\Model\User\User as UserDomainModel;

/**
 * User entity.
 */
class User
{
    /** @var numeric-string ID. */
    public string $id;

    /** @var string Name. */
    public string $name;

    /** @var string Email address. */
    public string $email;

    /** @var int<0, max> Age. */
    public int $age;

    /** @var string Sex. */
    public string $sex;

    /** @var PersistentCollection<array-key, Role> */
    public PersistentCollection $roles;

    /**
     * Convert itself to a domain model.
     *
     * @return UserDomainModel a new user domain model instance
     */
    public function toDomainModel(): UserDomainModel
    {
        /** @var AbstractLazyCollection<array-key, Role> */
        $roles = $this->roles;

        /** @var positive-int[] */
        $roleIds = $roles->map(function (Role $role): int {
            return (int) $role->id;
        })->toArray();

        return new UserDomainModel(
            id: $this->getIntId(),
            name: $this->name,
            email: $this->email,
            age: $this->age,
            sex: Sex::from($this->sex),
            roleIds: $roleIds,
        );
    }

    /**
     * Return the integer ID.
     *
     * @return positive-int
     */
    public function getIntId(): int
    {
        /** @var positive-int */
        return (int) $this->id;
    }
}
