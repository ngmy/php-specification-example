<?php

declare(strict_types=1);

namespace Tests\TestCase\Infrastructure\Domain\Model\User;

use Closure;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\AdminRoleSpecification;
use Domain\Model\Role\Specification\UserRoleSpecification;
use Domain\Model\User\Specification\AdminRoleUserSpecification;
use Domain\Model\User\Specification\AdultUserSpecification;
use Domain\Model\User\Specification\FemaleUserSpecification;
use Domain\Model\User\Specification\MaleUserSpecification;
use Domain\Model\User\Specification\MinorUserSpecification;
use Domain\Model\User\Specification\UserRoleUserSpecification;
use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Ngmy\Specification\SpecificationInterface;
use Tests\TestCase\AbstractTestCase;

abstract class AbstractUserRepositoryTestCase extends AbstractTestCase
{
    /**
     * @return iterable<array<mixed>>
     */
    public function dataProviderTestSelectSatisfying(): iterable
    {
        yield 'Adult' => [
            'Specification' => new AdultUserSpecification(),
            'Count' => 4,
        ];

        yield 'NOT Adult' => [
            'Specification' => (new AdultUserSpecification())->not(),
            'Count' => 4,
        ];

        yield 'Adult AND Male' => [
            'Specification' => (new AdultUserSpecification())->and(new MaleUserSpecification()),
            'Count' => 2,
        ];

        yield 'NOT (Adult AND Male)' => [
            'Specification' => (new AdultUserSpecification())->and(new MaleUserSpecification())->not(),
            'Count' => 6,
        ];

        yield 'Adult OR Male' => [
            'Specification' => (new AdultUserSpecification())->or(new MaleUserSpecification()),
            'Count' => 6,
        ];

        yield 'NOT (Adult OR Male)' => [
            'Specification' => (new AdultUserSpecification())->or(new MaleUserSpecification())->not(),
            'Count' => 2,
        ];

        yield '(Adult AND Male) OR (Minor AND Female)' => [
            'Specification' => (new AdultUserSpecification())->and(new MaleUserSpecification())
                ->or((new MinorUserSpecification())->and(new FemaleUserSpecification())),
            'Count' => 4,
        ];

        yield 'NOT ((Adult AND Male) OR (Minor AND Female))' => [
            'Specification' => (new AdultUserSpecification())->and(new MaleUserSpecification())
                ->or((new MinorUserSpecification())->and(new FemaleUserSpecification()))->not(),
            'Count' => 4,
        ];

        yield '(Adult OR Minor) AND (Male OR Female)' => [
            'Specification' => (new AdultUserSpecification())->or(new MinorUserSpecification())
                ->and((new MaleUserSpecification())->or(new FemaleUserSpecification())),
            'Count' => 8,
        ];

        yield 'NOT ((Adult OR Minor) AND (Male OR Female))' => [
            'Specification' => (new AdultUserSpecification())->or(new MinorUserSpecification())
                ->and((new MaleUserSpecification())->or(new FemaleUserSpecification()))->not(),
            'Count' => 0,
        ];

        yield 'Admin role' => [
            'Specification' => function (): SpecificationInterface {
                return new AdminRoleUserSpecification($this->createRoleRepository(), new AdminRoleSpecification());
            },
            'Count' => 4,
        ];

        yield 'User role' => [
            'Specification' => function (): SpecificationInterface {
                return new UserRoleUserSpecification($this->createRoleRepository(), new UserRoleSpecification());
            },
            'Count' => 4,
        ];

        yield 'Admin role AND Adult' => [
            'Specification' => function (): SpecificationInterface {
                return (new AdminRoleUserSpecification($this->createRoleRepository(), new AdminRoleSpecification()))->and(new AdultUserSpecification());
            },
            'Count' => 2,
        ];

        yield 'User role AND Adult' => [
            'Specification' => function (): SpecificationInterface {
                return (new UserRoleUserSpecification($this->createRoleRepository(), new UserRoleSpecification()))->and(new AdultUserSpecification());
            },
            'Count' => 2,
        ];
    }

    /**
     * @dataProvider dataProviderTestSelectSatisfying
     *
     * @param Closure():SpecificationInterface<User>|SpecificationInterface<User> $specification specification
     * @param int<0, max>                                                         $count         count of users satisfying the specification
     */
    public function testSelectSatisfying($specification, int $count): void
    {
        $specification = $specification instanceof Closure ? $specification() : $specification;

        $repository = $this->createUserRepository();

        $users = $repository->selectSatisfying($specification);

        $this->assertCount($count, $users);
        $this->assertEntityIsSatisfiedBySpecification($users, $specification);
    }

    abstract protected function createUserRepository(): UserRepositoryInterface;

    abstract protected function createRoleRepository(): RoleRepositoryInterface;

    /**
     * Assert that the users satisfying the specifications.
     *
     * @param array<User>                  $users         users
     * @param SpecificationInterface<User> $specification specification
     */
    protected function assertEntityIsSatisfiedBySpecification(array $users, SpecificationInterface $specification): void
    {
        foreach ($users as $user) {
            $this->assertTrue($specification->isSatisfiedBy($user));
        }
    }
}
