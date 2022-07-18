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
    public function selectSatisfying(SpecificationInterface $spec): array;
}
