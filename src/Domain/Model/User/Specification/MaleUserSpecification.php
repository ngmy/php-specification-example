<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Domain\Model\User\Sex;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Male user specification.
 */
class MaleUserSpecification extends AbstractUserSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        return $candidate->getSex()->value === Sex::Male->value;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('sex', Sex::Male->value);
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere(sprintf("%s.sex = '%s'", $queryBuilder->getRootAliases()[0], Sex::Male->value));
    }
}
