<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Sex specification.
 */
class SexSpecification extends AbstractUserSpecification
{
    /**
     * Create a new sex specification.
     *
     * @param string $sex sex
     */
    public function __construct(private string $sex)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getSex()->value === $this->sex;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('sex', $this->sex);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->eq(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'sex'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, $this->sex),
        ));
    }
}
