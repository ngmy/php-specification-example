<?php

declare(strict_types=1);

namespace Domain\Model\User\Specification;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use InvalidArgumentException;
use Ngmy\Specification\Support\DoctrineUtils;

/**
 * Email specification.
 */
class EmailSpecification extends AbstractUserSpecification
{
    /**
     * Create a new email specification.
     *
     * @param string $email email address
     */
    public function __construct(private readonly string $email)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($candidate): bool
    {
        if ($result = false === preg_match("/{$this->email}/", $candidate->getEmail(), $matches)) {
            throw new InvalidArgumentException(sprintf('Invalid regular expression pattern: %s', $this->email));
        }

        return (bool) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToEloquent(EloquentBuilder $query): void
    {
        $query->where('email', 'LIKE', "%{$this->email}%");
    }

    /**
     * {@inheritdoc}
     */
    public function applyToDoctrine(DoctrineQueryBuilder $queryBuilder): void
    {
        $queryBuilder->andWhere($queryBuilder->expr()->like(
            DoctrineUtils::getRootAliasedColumnName($queryBuilder, 'email'),
            DoctrineUtils::createUniqueNamedParameter($this, $queryBuilder, "%{$this->email}%"),
        ));
    }
}
