<?php

namespace Database\Factories;

use App\Models\Spend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpendFactory extends Factory
{
    protected $model = Spend::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'date' => $this->faker->date(),
        ];
    }
} 