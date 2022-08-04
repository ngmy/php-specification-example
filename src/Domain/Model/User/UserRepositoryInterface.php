<?php

declare(strict_types=1);

namespace Domain\Model\User;

use Ngmy\Specification\SpecificationInterface;

/**
 * User repository interface.
 */
interface UserRepositoryInterface
{
    /**
     * Find users satisfying a specification.
     *
     * @param SpecificationInterface<User> $spec specification
     *
     * @return User[] users satisfying the specification
     */
    public function findBySpecification(SpecificationInterface $spec): array;

    /**
     * Find one user satisfying a specification.
     *
     * @param SpecificationInterface<User> $spec specification
     *
     * @return null|User one user satisfying the specification
     */
    public function findOneBySpecification(SpecificationInterface $spec): ?User;
}
