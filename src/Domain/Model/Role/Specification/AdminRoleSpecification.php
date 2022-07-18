<?php

declare(strict_types=1);

namespace Domain\Model\Role\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Infrastructure\Persistence\Eloquent\Role as EloquentRole;

/**
 * Admin role specification.
 */
class AdminRoleSpecification extends AbstractRoleSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return EloquentRole::SLUG_ADMIN === $candidate->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('slug', EloquentRole::SLUG_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere(sprintf("%s.slug = '%s'", $queryBuilder->getRootAliases()[0], EloquentRole::SLUG_ADMIN));
    }
}
