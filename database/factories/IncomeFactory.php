<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2, 1, 5000),
            'date' => $this->faker->date(),
            'mods' => $this->faker->boolean(50) ? [
                ['type' => 'percent', 'value' => 10, 'description' => 'bonus'],
                ['type' => 'fixed', 'value' => -100, 'description' => 'tax penalty'],
            ] : null,
        ];
    }
} 