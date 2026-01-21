<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador para Filament
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Crear categorías
        $categories = ['Tecnología', 'Deportes', 'Cultura', 'Salud', 'Ciencia'];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }

        // Crear posts de prueba para verificar paginación
        $allCategories = Category::all();
        for ($i = 1; $i <= 50; $i++) {
            Post::create([
                'title' => "Post de ejemplo $i",
                'content' => "Contenido detallado del post número $i. Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                'category_id' => $allCategories->random()->id,
            ]);
        }
    }
}
