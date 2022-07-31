<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\User;

use Doctrine\ORM\EntityManager;
use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Infrastructure\Persistence\Doctrine\Entity\User as EntityUser;
use Ngmy\Specification\SpecificationInterface;

/**
 * Implementation of the `UserRepositoryInterface` interface with Doctrine.
 */
class DoctrineUserRepository implements UserRepositoryInterface
{
    /**
     * @param EntityManager $entityManager entity manager
     */
    public function __construct(private EntityManager $entityManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function selectSatisfying(SpecificationInterface $spec): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')->from(EntityUser::class, 'u');
        $spec->applyToDoctrine($queryBuilder);

        /** @var EntityUser[] */
        $entities = $queryBuilder->getQuery()->getResult();

        return array_map(function (EntityUser $entityUser): User {
            return $entityUser->toDomainModel();
        }, $entities);
    }
}
