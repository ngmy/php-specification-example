<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\UserRoleSpecification;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Infrastructure\Persistence\Doctrine\Entity\Role;
use Infrastructure\Persistence\Doctrine\Entity\RoleUser;

/**
 * User role user specification.
 */
class UserRoleUserSpecification extends AbstractUserSpecification
{
    /**
     * Create a new user role user specification.
     *
     * @param RoleRepositoryInterface $roleRepository role repository
     * @param UserRoleSpecification   $userRoleSpec   user role specification
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly UserRoleSpecification $userRoleSpec,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        $userRoles = $this->roleRepository->selectSatisfying($this->userRoleSpec);
        foreach ($userRoles as $userRole) {
            if (in_array($userRole->getId(), $candidate->getRoleIds())) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->whereHas('roles', function (EloquentBuilder $query): void {
            $this->userRoleSpec->applyToEloquent($query);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $entityManager = $queryBuilder->getEntityManager();
        $subQueryBuilder = $entityManager->createQueryBuilder();
        $subQueryBuilder
            ->select('r')
            ->from(Role::class, 'r')
            ->innerJoin(RoleUser::class, 'ru', Join::WITH, 'r.id = ru.roleId')
            ->andWhere(sprintf('%s.id = ru.userId', $queryBuilder->getRootAliases()[0]))
        ;
        $this->userRoleSpec->applyToDoctrine($subQueryBuilder);
        $queryBuilder->andWhere($queryBuilder->expr()->exists($subQueryBuilder));
    }
}
