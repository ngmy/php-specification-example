<?php

declare(strict_types=1);

namespace Domain\Model\Role\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Infrastructure\Persistence\Eloquent\Role as EloquentRole;

/**
 * User role specification.
 */
class UserRoleSpecification extends AbstractRoleSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return EloquentRole::SLUG_USER === $candidate->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $queryBuilder): void
    {
        $queryBuilder->where('slug', EloquentRole::SLUG_USER);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere(sprintf("%s.slug = '%s'", $queryBuilder->getRootAliases()[0], EloquentRole::SLUG_USER));
    }
}
