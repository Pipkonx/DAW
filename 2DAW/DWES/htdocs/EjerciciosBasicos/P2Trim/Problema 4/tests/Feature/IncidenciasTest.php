<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Task;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidenciasTest extends TestCase
{
    // Usamos RefreshDatabase para que la base de datos de pruebas esté vacía antes de cada test
    use RefreshDatabase;

    /**
     * Prueba que la página principal redirige al login.
     */
    public function test_la_pagina_principal_redirige_al_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302); // 302 es redirección
    }

    /**
     * Prueba que un administrador puede ver la lista de tareas.
     */
    public function test_un_administrador_puede_ver_lista_de_tareas(): void
    {
        // Creamos un usuario administrador
        $admin = User::factory()->create(['role' => 'admin']);

        // Hacemos el login y vamos a la lista de tareas
        $response = $this->actingAs($admin)->get('/tasks');

        // Comprobamos que carga correctamente (código 200) y que aparece el texto "Tarea"
        $response->assertStatus(200);
        $response->assertSee('Tarea');
    }

    /**
     * Prueba que un cliente puede registrar una incidencia desde la parte pública.
     */
    public function test_un_cliente_puede_crear_incidencia_publica(): void
    {
        // Necesitamos una provincia y un cliente en la BD para que funcione
        $provincia = Province::create(['code' => '28', 'name' => 'Madrid']);
        $cliente = Client::create([
            'cif' => 'B12345678',
            'name' => 'Cliente de Prueba',
            'phone' => '912345678',
            'email' => 'test@example.com',
            'monthly_fee' => 100
        ]);

        // Enviamos el formulario por POST (como si hiciéramos click en enviar)
        $response = $this->post('/incidencias', [
            'cif' => 'B12345678',       // Coincide con el cliente
            'phone' => '912345678',     // Coincide con el cliente
            'contact_person' => 'Juan',
            'contact_phone' => '600112233',
            'contact_email' => 'juan@test.com',
            'description' => 'Mi ascensor no funciona',
            'address' => 'Calle Falsa 123',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'province_code' => '28',
        ]);

        // Comprobamos que se ha creado la tarea en la base de datos
        $this->assertDatabaseHas('tasks', [
            'description' => 'Mi ascensor no funciona'
        ]);
        
        // Comprobamos que nos muestra la página de éxito
        $response->assertStatus(200);
    }
}
