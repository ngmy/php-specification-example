<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\AdminRoleSpecification;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Infrastructure\Persistence\Doctrine\Entity\Role;
use Infrastructure\Persistence\Doctrine\Entity\RoleUser;

/**
 * Admin role user specification.
 */
class AdminRoleUserSpecification extends AbstractUserSpecification
{
    /**
     * Create a new admin role user specification.
     *
     * @param RoleRepositoryInterface $roleRepository role repository
     * @param AdminRoleSpecification  $adminRoleSpec  admin role specification
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly AdminRoleSpecification $adminRoleSpec,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        $adminRoles = $this->roleRepository->selectSatisfying($this->adminRoleSpec);
        foreach ($adminRoles as $adminRole) {
            if (in_array($adminRole->getId(), $candidate->getRoleIds())) {
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
            $this->adminRoleSpec->applyToEloquent($query);
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
        $this->adminRoleSpec->applyToDoctrine($subQueryBuilder);
        $queryBuilder->andWhere($queryBuilder->expr()->exists($subQueryBuilder));
    }
}
