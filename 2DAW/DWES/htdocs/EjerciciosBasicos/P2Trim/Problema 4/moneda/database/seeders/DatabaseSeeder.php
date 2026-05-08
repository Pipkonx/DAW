<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Cuota;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $cliente1 = Cliente::create([
            'name' => 'John Doe USA',
            'cif' => '12345678A',
            'currency' => 'USD'
        ]);

        $cliente2 = Cliente::create([
            'name' => 'London LTD',
            'cif' => '87654321B',
            'currency' => 'GBP'
        ]);

        Cuota::create([
            'client_id' => $cliente1->id,
            'concept' => 'Cuota Enero',
            'amount' => 100.00,
            'currency' => 'USD',
            'is_paid' => false
        ]);

        Cuota::create([
            'client_id' => $cliente2->id,
            'concept' => 'Mantenimiento Web',
            'amount' => 80.00,
            'currency' => 'GBP',
            'is_paid' => false
        ]);
    }
}
