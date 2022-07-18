<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Domain\Model\User\Sex;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Female user specification.
 */
class FemaleUserSpecification extends AbstractUserSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getSex()->value === Sex::Female->value;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('sex', Sex::Female->value);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere(sprintf("%s.sex = '%s'", $queryBuilder->getRootAliases()[0], Sex::Female->value));
    }
}
