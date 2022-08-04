<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Model\User\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Persistence\Eloquent\User as EloquentUser;

/**
 * @extends Factory<EloquentUser>
 */
class UserFactory extends Factory
{
    protected $model = EloquentUser::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'age' => $this->faker->numberBetween(0, 100),
            'sex' => $this->faker->randomElement([Sex::Male->value, Sex::Female->value]),
        ];
    }
}
