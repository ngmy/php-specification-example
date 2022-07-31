<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use InvalidArgumentException;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Name specification.
 */
class NameSpecification extends AbstractUserSpecification
{
    /**
     * Create a new name specification.
     *
     * @param string $name name
     */
    public function __construct(private readonly string $name)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        if ($result = false === preg_match("/{$this->name}/", $candidate->getName(), $matches)) {
            throw new InvalidArgumentException(sprintf('Invalid regular expression pattern: %s', $this->name));
        }

        return (bool) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('name', 'LIKE', "%{$this->name}%");
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->like(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'name'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, "%{$this->name}%"),
        ));
    }
}
