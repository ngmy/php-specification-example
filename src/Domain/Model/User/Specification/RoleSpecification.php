<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\SlugSpecification;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Infrastructure\Persistence\Doctrine\Entity\Role;
use Infrastructure\Persistence\Doctrine\Entity\RoleUser;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Role specification.
 */
class RoleSpecification extends AbstractUserSpecification
{
    /**
     * Create a new role user specification.
     *
     * @param RoleRepositoryInterface $roleRepository role repository
     * @param SlugSpecification       $roleSlugSpec   role name specification
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly SlugSpecification $roleSlugSpec,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        $role = $this->roleRepository->findOneBySpecification($this->roleSlugSpec);

        if (null === $role) {
            return false;
        }

        foreach ($role->getUsers() as $user) {
            if ($user->equals($candidate)) {
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
            $this->roleSlugSpec->applyToEloquent($query);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $parameters = $queryBuilder->getParameters();

        $entityManager = $queryBuilder->getEntityManager();
        $subQueryBuilder = $entityManager->createQueryBuilder();
        $subQueryBuilder
            ->select(DoctrineUtils::getUniqueAlias($this, 'r'))
            ->from(Role::class, DoctrineUtils::getUniqueAlias($this, 'r'))
            ->innerJoin(RoleUser::class, DoctrineUtils::getUniqueAlias($this, 'ru'), Join::WITH, $subQueryBuilder->expr()->eq(
                DoctrineUtils::getUniqueAliasedColumnName($this, 'id', 'r'),
                DoctrineUtils::getUniqueAliasedColumnName($this, 'roleId', 'ru'),
            ))
            ->andWhere($subQueryBuilder->expr()->eq(
                DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'id'),
                DoctrineUtils::getUniqueAliasedColumnName($this, 'userId', 'ru'),
            ))
        ;
        $subQueryBuilder->setParameters($parameters);

        $this->roleSlugSpec->applyToDoctrine($subQueryBuilder);

        $queryBuilder->andWhere($queryBuilder->expr()->exists($subQueryBuilder));
    }
}
