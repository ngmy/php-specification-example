<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\User;

use Doctrine\ORM\EntityManager;
use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Infrastructure\Persistence\Doctrine\Entity\User as DoctrineUser;
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
    public function findBySpecification(SpecificationInterface $spec): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')->from(DoctrineUser::class, 'u');
        $spec->applyToDoctrine($queryBuilder);

        /** @var DoctrineUser[] */
        $doctrineUsers = $queryBuilder->getQuery()->getResult();

        return array_map(function (DoctrineUser $doctrineUser): User {
            return $doctrineUser->toDomainModel();
        }, $doctrineUsers);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySpecification(SpecificationInterface $spec): ?User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')->from(DoctrineUser::class, 'u');
        $spec->applyToDoctrine($queryBuilder);

        /** @var null|DoctrineUser */
        $doctrineUser = $queryBuilder->getQuery()->getSingleResult();

        if (null === $doctrineUser) {
            return null;
        }

        return $doctrineUser->toDomainModel();
    }
}
