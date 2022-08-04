<?php

declare(strict_types=1);

namespace Application\Service\User;

use Application\DataTransformer\DataTransformerInterface;
use Application\DataTransformer\User\AbstractUsersDataTransformer;
use Application\Service\AbstractService;
use Application\Service\RequestInterface;
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
 *
 * @template T of AbstractUsersDataTransformer
 * @extends AbstractService<T>
 */
class SearchUserService extends AbstractService
{
    /**
     * Create a new search user service.
     *
     * @param UserRepositoryInterface $userRepository  user repository
     * @param RoleRepositoryInterface $roleRepository  role repository
     * @param T                       $dataTransformer data transformer
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        protected DataTransformerInterface $dataTransformer,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @param SearchUserRequest $request request for service
     */
    public function execute(RequestInterface $request): void
    {
        $spec = AbstractUserSpecification::true();
        $spec = $spec->and($this->createNameSpecation($request));
        $spec = $spec->and($this->createEmailSpecation($request));
        $spec = $spec->and($this->createGenerationsSpecation($request));
        $spec = $spec->and($this->createSexesSpecification($request));
        $spec = $spec->and($this->createRolesSpecification($request));

        $users = $this->userRepository->selectSatisfying($spec);

        $this->dataTransformer->write($users);
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
