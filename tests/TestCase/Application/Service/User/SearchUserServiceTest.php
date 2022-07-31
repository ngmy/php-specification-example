<?php

declare(strict_types=1);

namespace Tests\TestCase\Application\Service\User;

use Application\Service\User\SearchUserRequest;
use Application\Service\User\SearchUserService;
use Closure;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\User\UserRepositoryInterface;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Domain\Model\Role\DoctrineRoleRepository;
use Infrastructure\Domain\Model\Role\EloquentRoleRepository;
use Infrastructure\Domain\Model\User\DoctrineUserRepository;
use Infrastructure\Domain\Model\User\EloquentUserRepository;
use Infrastructure\Persistence\Doctrine\EntityManagerFactory as DoctrineEntityManagerFactory;
use Tests\TestCase\AbstractTestCase;

/**
 * @internal
 * @coversDefaultClass \Application\Service\User\SearchUserService
 */
class SearchUserServiceTest extends AbstractTestCase
{
    /**
     * @return iterable<array<mixed>>
     */
    public function dataProviderTestExecute(): iterable
    {
        $eloquentRepositories = [
            function (): UserRepositoryInterface {
                return $this->createEloquentUserRepository();
            },
            function (): RoleRepositoryInterface {
                return $this->createEloquentRoleRepository();
            },
        ];

        $doctrineRepositories = [
            function (): UserRepositoryInterface {
                return $this->createDoctrineUserRepository();
            },
            function (): RoleRepositoryInterface {
                return $this->createDoctrineRoleRepository();
            },
        ];

        foreach ([$eloquentRepositories, $doctrineRepositories] as $key => $repositories) {
            yield "no search condition with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(),
                'count' => 8,
            ];

            yield "name = 'John Doe 1' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    name: 'John Doe 1',
                ),
                'count' => 1,
            ];

            yield "name = 'John Doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    name: 'John Doe',
                ),
                'count' => 4,
            ];

            yield "name = 'Jane Doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    name: 'Jane Doe',
                ),
                'count' => 4,
            ];

            yield "name = 'Doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    name: 'Doe',
                ),
                'count' => 8,
            ];

            yield "email = 'john.doe.1@example.com' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    email: 'john.doe.1@example.com',
                ),
                'count' => 1,
            ];

            yield "email = 'john.doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    email: 'john.doe',
                ),
                'count' => 4,
            ];

            yield "name = 'jane.doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    email: 'jane.doe',
                ),
                'count' => 4,
            ];

            yield "name = 'doe' with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    email: 'doe',
                ),
                'count' => 8,
            ];

            yield "sexes = ['male'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    sexes: ['male'],
                ),
                'count' => 4,
            ];

            yield "sexes = ['female'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    sexes: ['female'],
                ),
                'count' => 4,
            ];

            yield "sexes = ['male', 'female'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    sexes: ['male', 'female'],
                ),
                'count' => 8,
            ];

            yield "generations = [10] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    generations: [10],
                ),
                'count' => 4,
            ];

            yield "generations = [20] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    generations: [20],
                ),
                'count' => 4,
            ];

            yield "generations = [10, 20] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    generations: [10, 20],
                ),
                'count' => 8,
            ];

            yield "generations = [0] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    generations: [0],
                ),
                'count' => 0,
            ];

            yield "roles = ['admin'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    roles: ['admin'],
                ),
                'count' => 4,
            ];

            yield "roles = ['user'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    roles: ['user'],
                ),
                'count' => 4,
            ];

            yield "roles = ['admin', 'user'] with {$key}" => [
                'userRepository' => $repositories[0],
                'roleRepository' => $repositories[1],
                'request' => new SearchUserRequest(
                    roles: ['admin', 'user'],
                ),
                'count' => 8,
            ];
        }
    }

    /**
     * @dataProvider dataProviderTestExecute
     *
     * @param Closure():UserRepositoryInterface $userRepository user repository
     * @param Closure():RoleRepositoryInterface $roleRepository role repository
     * @param SearchUserRequest                 $request        search request
     * @param int<0, max>                       $count          count of users in search results
     */
    public function testExecute(Closure $userRepository, Closure $roleRepository, SearchUserRequest $request, int $count): void
    {
        $service = new SearchUserService($userRepository(), $roleRepository());

        $users = $service->execute($request);

        $this->assertcount($count, $users);
    }

    private function createEloquentUserRepository(): UserRepositoryInterface
    {
        return new EloquentUserRepository();
    }

    private function createEloquentRoleRepository(): RoleRepositoryInterface
    {
        return new EloquentRoleRepository();
    }

    private function createDoctrineUserRepository(): UserRepositoryInterface
    {
        $entityManager = $this->createDoctrineEntitymanager();

        return new DoctrineUserRepository($entityManager);
    }

    private function createDoctrineRoleRepository(): RoleRepositoryInterface
    {
        $entityManager = $this->createDoctrineEntitymanager();

        return new DoctrineRoleRepository($entityManager);
    }

    private function createDoctrineEntitymanager(): DoctrineEntityManager
    {
        /** @var Connection */
        $connection = DB::connection();
        $connection = $connection->getDoctrineConnection();

        return (new DoctrineEntityManagerFactory())->create($connection);
    }
}
