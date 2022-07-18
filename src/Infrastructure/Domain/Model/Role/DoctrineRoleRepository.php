<?php

declare(strict_types=1);

namespace Infrastructure\Domain\Model\Role;

use Doctrine\ORM\EntityManager;
use Domain\Model\Role\Role;
use Domain\Model\Role\RoleRepositoryInterface;
use Infrastructure\Persistence\Doctrine\Entity\Role as EntityRole;
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
    public function selectSatisfying(SpecificationInterface $spec): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query->select('u')->from(EntityRole::class, 'u');
        $spec->applyToDoctrine($query);

        /** @var EntityRole[] */
        $entities = $query->getQuery()->getResult();

        return array_map(function (EntityRole $entityRole): Role {
            return $entityRole->toDomainModel();
        }, $entities);
    }
}
