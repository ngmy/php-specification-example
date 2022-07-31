<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Age specification.
 */
class AgeSpecification extends AbstractUserSpecification
{
    /**
     * Create a new age specification.
     *
     * @param int<0, max> $age age
     */
    public function __construct(private readonly int $age)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getAge() === $this->age;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('age', $this->age);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->eq(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'age'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, $this->age),
        ));
    }
}
