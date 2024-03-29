<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\Role;

use Domain\Model\Role\Role;
use Domain\Model\Role\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Persistence\Eloquent\Role as EloquentRole;
use Ngmy\Specification\SpecificationInterface;

/**
 * Implementation of the `RoleRepositoryInterface` interface with Eloquent.
 */
class EloquentRoleRepository implements RoleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findBySpecification(SpecificationInterface $spec): array
    {
        $query = EloquentRole::query()->with('users');
        $spec->applyToEloquent($query);

        /** @var Collection<array-key, EloquentRole> */
        $eloquentRoles = $query->get();

        /** @var Role[] */
        $roles = $eloquentRoles
            ->map(function (EloquentRole $eloquentRole): Role {
                return $eloquentRole->toDomainModel();
            })
            ->toArray()
        ;

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySpecification(SpecificationInterface $spec): ?Role
    {
        $query = EloquentRole::query()->with('users');
        $spec->applyToEloquent($query);

        /** @var null|EloquentRole */
        $eloquentRole = $query->first();

        if (null === $eloquentRole) {
            return null;
        }

        return $eloquentRole->toDomainModel();
    }
}
