<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\ORM\PersistentCollection;
use Domain\Model\Role\Role as RoleDomainModel;
use Domain\Model\User\User as UserDomainModel;

/**
 * Role entity.
 */
class Role
{
    /** @var numeric-string ID. */
    public string $id;

    /** @var string Name. */
    public string $name;

    /** @var string Slug. */
    public string $slug;

    /** @var string Description. */
    public string $description;

    /** @var PersistentCollection<array-key, User> */
    public PersistentCollection $users;

    /**
     * Convert itself to a domain model.
     *
     * @return RoleDomainModel a new role domain model instance
     */
    public function toDomainModel(): RoleDomainModel
    {
        /** @var AbstractLazyCollection<array-key, User> */
        $users = $this->users;

        /** @var UserDomainModel[] */
        $userDomainModels = $users->map(function (User $user): UserDomainModel {
            return $user->toDomainModel();
        })->toArray();

        return new RoleDomainModel(
            id: $this->getIntId(),
            name: $this->name,
            slug: $this->slug,
            description: $this->description,
            users: $userDomainModels,
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
