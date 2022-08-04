<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\User;

use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Persistence\Eloquent\User as EloquentUser;
use Ngmy\Specification\SpecificationInterface;

/**
 * Implementation of the `UserRepositoryInterface` interface with Eloquent.
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findBySpecification(SpecificationInterface $spec): array
    {
        $query = EloquentUser::query()->with('roles');
        $spec->applyToEloquent($query);

        /** @var Collection<array-key, EloquentUser> */
        $eloquentUsers = $query->get();

        /** @var User[] */
        $users = $eloquentUsers
            ->map(function (EloquentUser $eloquentUser): User {
                return $eloquentUser->toDomainModel();
            })
            ->toArray()
        ;

        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySpecification(SpecificationInterface $spec): ?User
    {
        $query = EloquentUser::query()->with('roles');
        $spec->applyToEloquent($query);

        /** @var null|EloquentUser */
        $eloquentUser = $query->first();

        if (null === $eloquentUser) {
            return null;
        }

        return $eloquentUser->toDomainModel();
    }
}
