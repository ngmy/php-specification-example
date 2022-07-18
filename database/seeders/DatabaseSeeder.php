<?php

declare(strict_types=1);

namespace Database\Seeders;

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
                'name' => EloquentRole::NAME_ADMIN,
                'slug' => EloquentRole::SLUG_ADMIN,
            ])
        ;

        /** @var EloquentRole User role. */
        $eloquentUserRole = EloquentRole::factory()
            ->create([
                'name' => EloquentRole::NAME_USER,
                'slug' => EloquentRole::SLUG_USER,
            ])
        ;

        /** @var EloquentUser Adult, male, and admin role user. */
        $eloquentUser1 = EloquentUser::factory()
            ->create([
                'age' => 20,
                'sex' => EloquentUser::SEX_MALE,
            ])
        ;
        $eloquentUser1
            ->roles()
            ->attach($eloquentAdminRole->id)
        ;

        /** @var EloquentUser Adult, female, and admin role user. */
        $eloquentUser2 = EloquentUser::factory()
            ->create([
                'age' => 20,
                'sex' => EloquentUser::SEX_FEMALE,
            ])
        ;
        $eloquentUser2
            ->roles()
            ->attach($eloquentAdminRole->id)
        ;

        /** @var EloquentUser Minor, male, and admin role user. */
        $eloquentUser3 = EloquentUser::factory()
            ->create([
                'age' => 19,
                'sex' => EloquentUser::SEX_MALE,
            ])
        ;
        $eloquentUser3
            ->roles()
            ->attach($eloquentAdminRole->id)
        ;

        /** @var EloquentUser Minor, female, and admin role user. */
        $eloquentUser4 = EloquentUser::factory()
            ->create([
                'age' => 19,
                'sex' => EloquentUser::SEX_FEMALE,
            ])
        ;
        $eloquentUser4
            ->roles()
            ->attach($eloquentAdminRole->id)
        ;

        /** @var EloquentUser Adult, male, and user role user. */
        $eloquentUser5 = EloquentUser::factory()
            ->create([
                'age' => 20,
                'sex' => EloquentUser::SEX_MALE,
            ])
        ;
        $eloquentUser5
            ->roles()
            ->attach($eloquentUserRole->id)
        ;

        /** @var EloquentUser Adult, female, and user role user. */
        $eloquentUser6 = EloquentUser::factory()
            ->create([
                'age' => 20,
                'sex' => EloquentUser::SEX_FEMALE,
            ])
        ;
        $eloquentUser6
            ->roles()
            ->attach($eloquentUserRole->id)
        ;

        /** @var EloquentUser Minor, male, and user role user. */
        $eloquentUser7 = EloquentUser::factory()
            ->create([
                'age' => 19,
                'sex' => EloquentUser::SEX_MALE,
            ])
        ;
        $eloquentUser7
            ->roles()
            ->attach($eloquentUserRole->id)
        ;

        /** @var EloquentUser Minor, female, and user role user. */
        $eloquentUser8 = EloquentUser::factory()
            ->create([
                'age' => 19,
                'sex' => EloquentUser::SEX_FEMALE,
            ])
        ;
        $eloquentUser8
            ->roles()
            ->attach($eloquentUserRole->id)
        ;
    }
}
