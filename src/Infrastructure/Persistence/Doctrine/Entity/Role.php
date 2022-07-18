<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Doctrine\Entity;

use Domain\Model\Role\Role as RoleDomainModel;

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

    /**
     * Convert itself to a domain model.
     *
     * @return RoleDomainModel a new role domain model instance
     */
    public function toDomainModel(): RoleDomainModel
    {
        return new RoleDomainModel(
            id: $this->getIntId(),
            name: $this->name,
            slug: $this->slug,
            description: $this->description,
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
