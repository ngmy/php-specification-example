<?php

declare(strict_types=1);

namespace Domain\Model\Role\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Slug specification.
 */
class SlugSpecification extends AbstractRoleSpecification
{
    /**
     * Create a new slug specification.
     *
     * @param string $slug slug
     */
    public function __construct(private readonly string $slug)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getSlug() === $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('slug', $this->slug);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->eq(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'slug'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, $this->slug),
        ));
    }
}
