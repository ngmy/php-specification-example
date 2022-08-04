<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\Role;

use Doctrine\ORM\EntityManager;
use Domain\Model\Role\Role;
use Domain\Model\Role\RoleRepositoryInterface;
use Infrastructure\Persistence\Doctrine\Entity\Role as DoctrineRole;
use Ngmy\Specification\SpecificationInterface;

/**
 * Implementation of the `RoleRepositoryInterface` interface with Doctrine.
 */
class DoctrineRoleRepository implements RoleRepositoryInterface
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
        $query = $this->entityManager->createQueryBuilder();
        $query->select('u')->from(DoctrineRole::class, 'u');
        $spec->applyToDoctrine($query);

        /** @var DoctrineRole[] */
        $doctrineRoles = $query->getQuery()->getResult();

        return array_map(function (DoctrineRole $doctrineRole): Role {
            return $doctrineRole->toDomainModel();
        }, $doctrineRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySpecification(SpecificationInterface $spec): ?Role
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('u')->from(DoctrineRole::class, 'u');
        $spec->applyToDoctrine($query);

        /** @var null|DoctrineRole */
        $doctrineRole = $query->getQuery()->getSingleResult();

        if (null === $doctrineRole) {
            return null;
        }

        return $doctrineRole->toDomainModel();
    }
}
