<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Persistence\Eloquent\Role as EloquentRole;

/**
 * @extends Factory<EloquentRole>
 */
class RoleFactory extends Factory
{
    protected $model = EloquentRole::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([EloquentRole::NAME_ADMIN, EloquentRole::NAME_USER]),
            'slug' => $this->faker->unique()->randomElement([EloquentRole::SLUG_ADMIN, EloquentRole::SLUG_USER]),
            'description' => $this->faker->text(),
        ];
    }
}
