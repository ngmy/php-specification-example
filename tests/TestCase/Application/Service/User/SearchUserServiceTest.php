<?php

declare(strict_types=1);

namespace Tests\TestCase\Application\Service\User;

use Application\DataTransformer\User\AbstractUsersDataTransformer;
use Application\DataTransformer\User\UsersArrayDataTransformer;
use Application\DataTransformer\User\UsersJsonDataTransformer;
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
use InvalidArgumentException;
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
        $eloquentRepositoriesSet = [
            'user' => function (): UserRepositoryInterface {
                return $this->createEloquentUserRepository();
            },
            'role' => function (): RoleRepositoryInterface {
                return $this->createEloquentRoleRepository();
            },
        ];

        $doctrineRepositoriesSet = [
            'user' => function (): UserRepositoryInterface {
                return $this->createDoctrineUserRepository();
            },
            'role' => function (): RoleRepositoryInterface {
                return $this->createDoctrineRoleRepository();
            },
        ];

        $repositoriesSets = [
            'eloquent' => $eloquentRepositoriesSet,
            'doctrine' => $doctrineRepositoriesSet,
        ];

        $dataTransformers = [
            'array' => new UsersArrayDataTransformer(),
            'json' => new UsersJsonDataTransformer(),
        ];

        foreach ($repositoriesSets as $repositoriesSetKey => $repositoriesSet) {
            foreach ($dataTransformers as $dataTransformerKey => $dataTransformer) {
                yield "no search condition with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(),
                    'count' => 8,
                ];

                yield "name = 'John Doe 1' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        name: 'John Doe 1',
                    ),
                    'count' => 1,
                ];

                yield "name = 'John Doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        name: 'John Doe',
                    ),
                    'count' => 4,
                ];

                yield "name = 'Jane Doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        name: 'Jane Doe',
                    ),
                    'count' => 4,
                ];

                yield "name = 'Doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        name: 'Doe',
                    ),
                    'count' => 8,
                ];

                yield "email = 'john.doe.1@example.com' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        email: 'john.doe.1@example.com',
                    ),
                    'count' => 1,
                ];

                yield "email = 'john.doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        email: 'john.doe',
                    ),
                    'count' => 4,
                ];

                yield "name = 'jane.doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        email: 'jane.doe',
                    ),
                    'count' => 4,
                ];

                yield "name = 'doe' with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        email: 'doe',
                    ),
                    'count' => 8,
                ];

                yield "sexes = ['male'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        sexes: ['male'],
                    ),
                    'count' => 4,
                ];

                yield "sexes = ['female'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        sexes: ['female'],
                    ),
                    'count' => 4,
                ];

                yield "sexes = ['male', 'female'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        sexes: ['male', 'female'],
                    ),
                    'count' => 8,
                ];

                yield "generations = [10] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        generations: [10],
                    ),
                    'count' => 4,
                ];

                yield "generations = [20] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        generations: [20],
                    ),
                    'count' => 4,
                ];

                yield "generations = [10, 20] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        generations: [10, 20],
                    ),
                    'count' => 8,
                ];

                yield "roles = ['admin'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        roles: ['admin'],
                    ),
                    'count' => 4,
                ];

                yield "roles = ['user'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        roles: ['user'],
                    ),
                    'count' => 4,
                ];

                yield "roles = ['admin', 'user'] with {$repositoriesSetKey} repositories set and {$dataTransformerKey} data transformer" => [
                    'userRepository' => $repositoriesSet['user'],
                    'roleRepository' => $repositoriesSet['role'],
                    'dataTransformer' => $dataTransformer,
                    'request' => new SearchUserRequest(
                        roles: ['admin', 'user'],
                    ),
                    'count' => 8,
                ];
            }
        }
    }

    /**
     * @dataProvider dataProviderTestExecute
     *
     * @param Closure():UserRepositoryInterface $userRepository  user repository
     * @param Closure():RoleRepositoryInterface $roleRepository  role repository
     * @param AbstractUsersDataTransformer      $dataTransformer data transformer
     * @param SearchUserRequest                 $request         search request
     * @param int<0, max>                       $count           count of users in search results
     */
    public function testExecute(Closure $userRepository, Closure $roleRepository, AbstractUsersDataTransformer $dataTransformer, SearchUserRequest $request, int $count): void
    {
        if ($dataTransformer instanceof UsersArrayDataTransformer) {
            /** @var SearchUserService<UsersArrayDataTransformer> */
            $service = new SearchUserService($userRepository(), $roleRepository(), $dataTransformer);

            $service->execute($request);

            $dataTransformer = $service->getDataTransformer();

            $users = $dataTransformer->read();
        } elseif ($dataTransformer instanceof UsersJsonDataTransformer) {
            /** @var SearchUserService<UsersJsonDataTransformer> */
            $service = new SearchUserService($userRepository(), $roleRepository(), $dataTransformer);

            $service->execute($request);

            $dataTransformer = $service->getDataTransformer();

            $users = $dataTransformer->read();

            /** @var list<array{id: positive-int, name: string, email: string, age: int<0, max>, sex: string}> */
            $users = json_decode($users, true);
        } else {
            throw new InvalidArgumentException(sprintf('Invalid data transformer: %s', get_class($dataTransformer)));
        }

        $this->assertCount($count, $users);
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
