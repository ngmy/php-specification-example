<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Maximum age specification.
 */
class MaximumAgeSpecification extends AbstractUserSpecification
{
    /**
     * Create a new maximum age specification.
     *
     * @param int<0, max> $threshold threshold
     */
    public function __construct(private readonly int $threshold)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getAge() <= $this->threshold;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('age', '<=', $this->threshold);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->lte(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'age'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, $this->threshold),
        ));
    }
}
