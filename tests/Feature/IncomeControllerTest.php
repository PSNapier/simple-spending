<?php

namespace Tests\Feature;

use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_user_incomes()
    {
        $user = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/income');
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $income->id]);
    }

    public function test_store_creates_income_with_mods()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Salary',
            'amount' => 1000.00,
            'date' => now()->toDateString(),
            'mods' => [
                ['type' => 'percent', 'value' => 10, 'description' => 'bonus'],
                ['type' => 'fixed', 'value' => -100, 'description' => 'tax penalty'],
            ],
        ];

        $response = $this->actingAs($user)->post('/income', $data);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Salary']);
        $this->assertDatabaseHas('income', ['name' => 'Salary', 'user_id' => $user->id]);
    }

    public function test_update_modifies_income_and_mods()
    {
        $user = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);
        $data = [
            'name' => 'Updated Income',
            'amount' => 2000,
            'date' => now()->toDateString(),
            'mods' => [
                ['type' => 'percent', 'value' => 5, 'description' => 'raise'],
            ],
        ];

        $response = $this->actingAs($user)->put("/income/{$income->id}", $data);
        $response->assertStatus(200)->assertJsonFragment(['name' => 'Updated Income']);
        $this->assertDatabaseHas('income', ['id' => $income->id, 'name' => 'Updated Income']);
    }

    public function test_destroy_deletes_income()
    {
        $user = User::factory()->create();
        $income = Income::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/income/{$income->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('income', ['id' => $income->id]);
    }
} 