<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminOperarioSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
        if (!User::where('email', 'operario@example.com')->exists()) {
            User::create([
                'name' => 'Operario',
                'email' => 'operario@example.com',
                'password' => Hash::make('password'),
                'role' => 'operario',
            ]);
        }
    }
}

