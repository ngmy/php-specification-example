<?php

declare(strict_types=1);

namespace Application\Service\User;

use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\SlugSpecification;
use Domain\Model\User\Specification\AbstractUserSpecification;
use Domain\Model\User\Specification\EmailSpecification;
use Domain\Model\User\Specification\MaximumAgeSpecification;
use Domain\Model\User\Specification\MinimumAgeSpecification;
use Domain\Model\User\Specification\NameSpecification;
use Domain\Model\User\Specification\RoleSpecification;
use Domain\Model\User\Specification\SexSpecification;
use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Ngmy\Specification\SpecificationInterface;

/**
 * Search user service.
 */
class SearchUserService
{
    /**
     * Create a new search user service.
     *
     * @param UserRepositoryInterface $userRepository user repository
     * @param RoleRepositoryInterface $roleRepository role repository
     */
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly RoleRepositoryInterface $roleRepository)
    {
    }

    /**
     * Execute service.
     *
     * @return iterable<array-key, User>
     */
    public function execute(SearchUserRequest $request): iterable
    {
        $spec = AbstractUserSpecification::true();
        $spec = $spec->and($this->createNameSpecation($request));
        $spec = $spec->and($this->createEmailSpecation($request));
        $spec = $spec->and($this->createGenerationsSpecation($request));
        $spec = $spec->and($this->createSexesSpecification($request));
        $spec = $spec->and($this->createRolesSpecification($request));

        return $this->userRepository->selectSatisfying($spec);
    }

    /**
     * @return SpecificationInterface<User>
     */
    private function createNameSpecation(SearchUserRequest $request): SpecificationInterface
    {
        return null === $request->name ? AbstractUserSpecification::true() : new NameSpecification($request->name);
    }

    /**
     * @return SpecificationInterface<User>
     */
    private function createEmailSpecation(SearchUserRequest $request): SpecificationInterface
    {
        return null === $request->email ? AbstractUserSpecification::true() : new EmailSpecification($request->email);
    }

    /**
     * @return SpecificationInterface<User>
     */
    private function createGenerationsSpecation(SearchUserRequest $request): SpecificationInterface
    {
        $generationsSpec = AbstractUserSpecification::true();

        foreach ($request->generations as $i => $generation) {
            $minimumAgeSpec = new MinimumAgeSpecification($generation);
            $maximumAgeSpec = new MaximumAgeSpecification($generation + 10 - 1);
            $generationSpec = $minimumAgeSpec->and($maximumAgeSpec);

            if (0 === $i) {
                $generationsSpec = $generationsSpec->and($generationSpec);

                continue;
            }

            $generationsSpec = $generationsSpec->or($generationSpec);
        }

        return $generationsSpec;
    }

    /**
     * @return SpecificationInterface<User>
     */
    private function createSexesSpecification(SearchUserRequest $request): SpecificationInterface
    {
        $sexesSpec = AbstractUserSpecification::true();

        foreach ($request->sexes as $i => $sex) {
            $sexSpec = new SexSpecification($sex);

            if (0 === $i) {
                $sexesSpec = $sexesSpec->and($sexSpec);

                continue;
            }

            $sexesSpec = $sexesSpec->or($sexSpec);
        }

        return $sexesSpec;
    }

    /**
     * @return SpecificationInterface<User>
     */
    private function createRolesSpecification(SearchUserRequest $request): SpecificationInterface
    {
        $rolesSpec = AbstractUserSpecification::true();

        foreach ($request->roles as $i => $role) {
            $roleSlugSpec = new SlugSpecification($role);
            $roleSpec = new RoleSpecification($this->roleRepository, $roleSlugSpec);

            if (0 === $i) {
                $rolesSpec = $rolesSpec->and($roleSpec);

                continue;
            }

            $rolesSpec = $rolesSpec->or($roleSpec);
        }

        return $rolesSpec;
    }
}
