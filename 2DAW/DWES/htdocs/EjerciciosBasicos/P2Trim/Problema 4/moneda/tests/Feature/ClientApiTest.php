<?php

namespace Tests\Feature;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_clients()
    {
        Cliente::factory()->count(3)->create();

        $response = $this->getJson('/api/clientes');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_client()
    {
        $data = [
            'name' => 'Test Client',
            'cif' => 'B12345678',
            'currency' => 'USD'
        ];

        $response = $this->postJson('/api/clientes', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Client']);
        
        $this->assertDatabaseHas('clientes', ['cif' => 'B12345678']);
    }

    /** @test */
    public function it_can_show_a_client()
    {
        $cliente = Cliente::factory()->create();

        $response = $this->getJson("/api/clientes/{$cliente->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $cliente->id]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_client()
    {
        $response = $this->getJson('/api/clientes/999');

        $response->assertStatus(404);
    }
}
