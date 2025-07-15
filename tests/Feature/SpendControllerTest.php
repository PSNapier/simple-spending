<?php

namespace Tests\Feature;

use App\Models\Spend;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpendControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_user_spendings()
    {
        $user = User::factory()->create();
        $spend = Spend::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/spend');
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $spend->id]);
    }

    public function test_store_creates_spending()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Groceries',
            'amount' => 50.25,
            'date' => now()->toDateString(),
        ];

        $response = $this->actingAs($user)->post('/spend', $data);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Groceries']);
        $this->assertDatabaseHas('spend', ['name' => 'Groceries', 'user_id' => $user->id]);
    }

    public function test_update_modifies_spending()
    {
        $user = User::factory()->create();
        $spend = Spend::factory()->create(['user_id' => $user->id]);
        $data = ['name' => 'Updated', 'amount' => 99, 'date' => now()->toDateString()];

        $response = $this->actingAs($user)->put("/spend/{$spend->id}", $data);
        $response->assertStatus(200)->assertJsonFragment(['name' => 'Updated']);
        $this->assertDatabaseHas('spend', ['id' => $spend->id, 'name' => 'Updated']);
    }

    public function test_destroy_deletes_spending()
    {
        $user = User::factory()->create();
        $spend = Spend::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/spend/{$spend->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('spend', ['id' => $spend->id]);
    }
} 