<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Model\User\Sex;
use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Role as EloquentRole;
use Infrastructure\Persistence\Eloquent\User as EloquentUser;

class DatabaseSeeder extends Seeder
{
    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        /** @var EloquentRole Admin role. */
        $eloquentAdminRole = EloquentRole::factory()
            ->create([
                'name' => 'Administrator',
                'slug' => 'admin',
            ])
        ;

        /** @var EloquentRole Member role. */
        $eloquentMemberRole = EloquentRole::factory()
            ->create([
                'name' => 'Member',
                'slug' => 'member',
            ])
        ;

        /** @var EloquentUser Adult, male, and admin role user. */
        $eloquentUser1 = EloquentUser::factory()
            ->create([
                'name' => 'John Doe 1',
                'email' => 'john.doe.1@example.com',
                'age' => 20,
                'sex' => Sex::Male->value,
            ])
        ;

        /** @var EloquentUser Adult, female, and admin role user. */
        $eloquentUser2 = EloquentUser::factory()
            ->create([
                'name' => 'Jane Doe 1',
                'email' => 'jane.doe.1@example.com',
                'age' => 20,
                'sex' => Sex::Female->value,
            ])
        ;

        /** @var EloquentUser Minor, male, and admin role user. */
        $eloquentUser3 = EloquentUser::factory()
            ->create([
                'name' => 'John Doe 2',
                'email' => 'john.doe.2@example.com',
                'age' => 19,
                'sex' => Sex::Male->value,
            ])
        ;

        /** @var EloquentUser Minor, female, and admin role user. */
        $eloquentUser4 = EloquentUser::factory()
            ->create([
                'name' => 'Jane Doe 2',
                'email' => 'jane.doe.2@example.com',
                'age' => 19,
                'sex' => Sex::Female->value,
            ])
        ;

        /** @var EloquentUser Adult, male, and member role user. */
        $eloquentUser5 = EloquentUser::factory()
            ->create([
                'name' => 'John Doe 3',
                'email' => 'john.doe.3@example.com',
                'age' => 20,
                'sex' => Sex::Male->value,
            ])
        ;

        /** @var EloquentUser Adult, female, and member role user. */
        $eloquentUser6 = EloquentUser::factory()
            ->create([
                'name' => 'Jane Doe 3',
                'email' => 'jane.doe.3@example.com',
                'age' => 20,
                'sex' => Sex::Female->value,
            ])
        ;

        /** @var EloquentUser Minor, male, and member role user. */
        $eloquentUser7 = EloquentUser::factory()
            ->create([
                'name' => 'John Doe 4',
                'email' => 'john.doe.4@example.com',
                'age' => 19,
                'sex' => Sex::Male->value,
            ])
        ;

        /** @var EloquentUser Minor, female, and member role user. */
        $eloquentUser8 = EloquentUser::factory()
            ->create([
                'name' => 'Jane Doe 4',
                'email' => 'jane.doe.4@example.com',
                'age' => 19,
                'sex' => Sex::Female->value,
            ])
        ;

        $eloquentAdminRole
            ->users()
            ->attach([
                $eloquentUser1->id,
                $eloquentUser2->id,
                $eloquentUser3->id,
                $eloquentUser4->id,
            ])
        ;

        $eloquentMemberRole
            ->users()
            ->attach([
                $eloquentUser5->id,
                $eloquentUser6->id,
                $eloquentUser7->id,
                $eloquentUser8->id,
            ])
        ;
    }
}
