<?php

declare(strict_types=1);

namespace Tests\TestCase\Infrastructure\Domain\Model\User;

use Closure;
use Domain\Model\Role\RoleRepositoryInterface;
use Domain\Model\Role\Specification\SlugSpecification;
use Domain\Model\User\Sex;
use Domain\Model\User\Specification\MaximumAgeSpecification;
use Domain\Model\User\Specification\MinimumAgeSpecification;
use Domain\Model\User\Specification\RoleSpecification;
use Domain\Model\User\Specification\SexSpecification;
use Domain\Model\User\User;
use Domain\Model\User\UserRepositoryInterface;
use Ngmy\Specification\SpecificationInterface;
use Tests\TestCase\AbstractTestCase;

abstract class AbstractUserRepositoryTestCase extends AbstractTestCase
{
    /**
     * @return iterable<array<mixed>>
     */
    public function dataProviderTestFindBySpecification(): iterable
    {
        yield 'adult' => [
            'specification' => (function (): SpecificationInterface {
                return new MinimumAgeSpecification(20);
            })(),
            'count' => 4,
        ];

        yield 'NOT adult' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);

                return $adultAgeSpec->not();
            })(),
            'count' => 4,
        ];

        yield 'adult AND male' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);

                return $adultAgeSpec->and($maleSpec);
            })(),
            'count' => 2,
        ];

        yield 'NOT (adult AND male)' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);

                return $adultAgeSpec->and($maleSpec)->not();
            })(),
            'count' => 6,
        ];

        yield 'adult OR male' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);

                return $adultAgeSpec->or($maleSpec);
            })(),
            'count' => 6,
        ];

        yield 'NOT (adult OR male)' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);

                return $adultAgeSpec->or($maleSpec)->not();
            })(),
            'count' => 2,
        ];

        yield '(adult AND male) OR (minor AND female)' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);
                $minorAgeSpec = new MaximumAgeSpecification(19);
                $femaleSpec = new SexSpecification(Sex::Female->value);

                return $adultAgeSpec->and($maleSpec)->or($minorAgeSpec->and($femaleSpec));
            })(),
            'count' => 4,
        ];

        yield 'NOT ((adult AND male) OR (minor AND female))' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);
                $minorAgeSpec = new MaximumAgeSpecification(19);
                $femaleSpec = new SexSpecification(Sex::Female->value);

                return $adultAgeSpec->and($maleSpec)->or($minorAgeSpec->and($femaleSpec))->not();
            })(),
            'count' => 4,
        ];

        yield '(adult OR minor) AND (male OR female)' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);
                $minorAgeSpec = new MaximumAgeSpecification(19);
                $femaleSpec = new SexSpecification(Sex::Female->value);

                return $adultAgeSpec->or($minorAgeSpec)->and($maleSpec->or($femaleSpec));
            })(),
            'count' => 8,
        ];

        yield 'NOT ((adult OR minor) AND (male OR female))' => [
            'specification' => (function (): SpecificationInterface {
                $adultAgeSpec = new MinimumAgeSpecification(20);
                $maleSpec = new SexSpecification(Sex::Male->value);
                $minorAgeSpec = new MaximumAgeSpecification(19);
                $femaleSpec = new SexSpecification(Sex::Female->value);

                return $adultAgeSpec->or($minorAgeSpec)->and($maleSpec->or($femaleSpec))->not();
            })(),
            'count' => 0,
        ];

        yield 'admin role' => [
            'specification' => function (): SpecificationInterface {
                $adminRoleSpec = new SlugSpecification('admin');

                return new RoleSpecification($this->createRoleRepository(), $adminRoleSpec);
            },
            'count' => 4,
        ];

        yield 'member role' => [
            'specification' => function (): SpecificationInterface {
                $memberRoleSpec = new SlugSpecification('member');

                return new RoleSpecification($this->createRoleRepository(), $memberRoleSpec);
            },
            'count' => 4,
        ];

        yield 'admin role AND adult' => [
            'specification' => function (): SpecificationInterface {
                $adminRoleSpec = new SlugSpecification('admin');
                $adminSpec = new RoleSpecification($this->createRoleRepository(), $adminRoleSpec);
                $adultAgeSpec = new MinimumAgeSpecification(20);

                return $adminSpec->and($adultAgeSpec);
            },
            'count' => 2,
        ];

        yield 'member role AND adult' => [
            'specification' => function (): SpecificationInterface {
                $memberRoleSpec = new SlugSpecification('member');
                $userSpec = new RoleSpecification($this->createRoleRepository(), $memberRoleSpec);
                $adultAgeSpec = new MinimumAgeSpecification(20);

                return $userSpec->and($adultAgeSpec);
            },
            'count' => 2,
        ];
    }

    /**
     * @dataProvider dataProviderTestFindBySpecification
     *
     * @param Closure():SpecificationInterface<User>|SpecificationInterface<User> $specification specification
     * @param int<0, max>                                                         $count         count of users satisfying the specification
     */
    public function testFindBySpecification($specification, int $count): void
    {
        $specification = $specification instanceof Closure ? $specification() : $specification;

        $repository = $this->createUserRepository();

        $users = $repository->findBySpecification($specification);

        $this->assertcount($count, $users);
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
