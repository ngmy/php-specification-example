<?php

declare(strict_types=1);

namespace Domain\Model\Role;

use Ngmy\Specification\SpecificationInterface;

/**
 * Role repository interface.
 */
interface RoleRepositoryInterface
{
    /**
     * Find roles satisfying a specification.
     *
     * @param SpecificationInterface<Role> $spec specification
     *
     * @return Role[] roles satisfying the specification
     */
    public function selectSatisfying(SpecificationInterface $spec): array;
}
