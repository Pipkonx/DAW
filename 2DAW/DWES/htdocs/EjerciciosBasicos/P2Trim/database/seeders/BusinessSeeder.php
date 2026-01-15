<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Province;
use App\Models\User;
use App\Models\Client;
use App\Models\Fee;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Provincias (mantener las existentes o recrear si es migrate:fresh)
        $provinces = [
            ['code' => '01', 'name' => 'Álava'],
            ['code' => '02', 'name' => 'Albacete'],
            ['code' => '03', 'name' => 'Alicante/Alacant'],
            ['code' => '04', 'name' => 'Almería'],
            ['code' => '05', 'name' => 'Ávila'],
            ['code' => '06', 'name' => 'Badajoz'],
            ['code' => '07', 'name' => 'Balears, Illes'],
            ['code' => '08', 'name' => 'Barcelona'],
            ['code' => '09', 'name' => 'Burgos'],
            ['code' => '10', 'name' => 'Cáceres'],
            ['code' => '11', 'name' => 'Cádiz'],
            ['code' => '12', 'name' => 'Castellón/Castelló'],
            ['code' => '13', 'name' => 'Ciudad Real'],
            ['code' => '14', 'name' => 'Córdoba'],
            ['code' => '15', 'name' => 'Coruña, A'],
            ['code' => '16', 'name' => 'Cuenca'],
            ['code' => '17', 'name' => 'Girona'],
            ['code' => '18', 'name' => 'Granada'],
            ['code' => '19', 'name' => 'Guadalajara'],
            ['code' => '20', 'name' => 'Gipuzkoa'],
            ['code' => '21', 'name' => 'Huelva'],
            ['code' => '22', 'name' => 'Huesca'],
            ['code' => '23', 'name' => 'Jaén'],
            ['code' => '24', 'name' => 'León'],
            ['code' => '25', 'name' => 'Lleida'],
            ['code' => '26', 'name' => 'Rioja, La'],
            ['code' => '27', 'name' => 'Lugo'],
            ['code' => '28', 'name' => 'Madrid'],
            ['code' => '29', 'name' => 'Málaga'],
            ['code' => '30', 'name' => 'Murcia'],
            ['code' => '31', 'name' => 'Navarra'],
            ['code' => '32', 'name' => 'Ourense'],
            ['code' => '33', 'name' => 'Asturias'],
            ['code' => '34', 'name' => 'Palencia'],
            ['code' => '35', 'name' => 'Palmas, Las'],
            ['code' => '36', 'name' => 'Pontevedra'],
            ['code' => '37', 'name' => 'Salamanca'],
            ['code' => '38', 'name' => 'Santa Cruz de Tenerife'],
            ['code' => '39', 'name' => 'Cantabria'],
            ['code' => '40', 'name' => 'Segovia'],
            ['code' => '41', 'name' => 'Sevilla'],
            ['code' => '42', 'name' => 'Soria'],
            ['code' => '43', 'name' => 'Tarragona'],
            ['code' => '44', 'name' => 'Teruel'],
            ['code' => '45', 'name' => 'Toledo'],
            ['code' => '46', 'name' => 'Valencia/València'],
            ['code' => '47', 'name' => 'Valladolid'],
            ['code' => '48', 'name' => 'Bizkaia'],
            ['code' => '49', 'name' => 'Zamora'],
            ['code' => '50', 'name' => 'Zaragoza'],
            ['code' => '51', 'name' => 'Ceuta'],
            ['code' => '52', 'name' => 'Melilla'],
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate(['code' => $province['code']], $province);
        }

        // Usuarios Administradores
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'dni' => '11111111A',
                'name' => 'Administrador Sistema',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'hire_date' => '2025-01-01',
                'phone' => '600000001',
                'address' => 'Calle Principal 1, Madrid'
            ]
        );

        // Usuarios Operarios
        $operator1 = User::firstOrCreate(
            ['email' => 'juan@example.com'],
            [
                'dni' => '22222222B',
                'name' => 'Juan Operario',
                'password' => Hash::make('user123'),
                'role' => 'operator',
                'is_active' => true,
                'hire_date' => '2025-02-15',
                'phone' => '600000002',
                'address' => 'Avenida Secundaria 10, Madrid'
            ]
        );

        $operator2 = User::firstOrCreate(
            ['email' => 'ana@example.com'],
            [
                'dni' => '33333333C',
                'name' => 'Ana Técnica',
                'password' => Hash::make('user123'),
                'role' => 'operator',
                'is_active' => true,
                'hire_date' => '2025-03-01',
                'phone' => '600000003',
                'address' => 'Plaza Mayor 5, Getafe'
            ]
        );

        // Clientes
        $client1 = Client::firstOrCreate(
            ['cif' => 'B12345678'],
            [
                'name' => 'Tecnologías Avanzadas S.L.',
                'phone' => '912345678',
                'email' => 'contacto@tecavanzadas.com',
                'bank_account' => 'ES1234567890123456789012',
                'country' => 'España',
                'currency' => 'EUR',
                'monthly_fee' => 150.00,
                'is_active' => true
            ]
        );

        $client2 = Client::firstOrCreate(
            ['cif' => 'A87654321'],
            [
                'name' => 'Suministros Globales S.A.',
                'phone' => '918765432',
                'email' => 'info@suministrosglobales.es',
                'bank_account' => 'ES9876543210987654321098',
                'country' => 'España',
                'currency' => 'EUR',
                'monthly_fee' => 299.99,
                'is_active' => true
            ]
        );

        // Cuotas (Fees)
        Fee::firstOrCreate(
            ['client_id' => $client1->id, 'concept' => 'Mantenimiento Mensual Enero 2026'],
            [
                'emission_date' => '2026-01-01',
                'amount' => 150.00,
                'is_paid' => 'S',
                'payment_date' => '2026-01-05',
                'notes' => 'Pagado por transferencia'
            ]
        );

        Fee::firstOrCreate(
            ['client_id' => $client2->id, 'concept' => 'Instalación Servidor'],
            [
                'emission_date' => '2026-01-10',
                'amount' => 500.00,
                'is_paid' => 'N',
                'notes' => 'Pendiente de confirmación bancaria'
            ]
        );

        // Tareas (Tasks)
        Task::firstOrCreate(
            ['description' => 'Reparación de red en oficina principal'],
            [
                'client_id' => $client1->id,
                'contact_person' => 'Carlos Pérez',
                'contact_phone' => '611223344',
                'contact_email' => 'cperez@tecavanzadas.com',
                'address' => 'Calle Falsa 123',
                'city' => 'Madrid',
                'postal_code' => '28001',
                'province_code' => '28',
                'status' => 'done',
                'operator_id' => $operator1->id,
                'completion_date' => '2026-01-12',
                'previous_notes' => 'El router parece estar fallando',
                'posterior_notes' => 'Se cambió el router por uno nuevo. Red funcionando correctamente.'
            ]
        );

        Task::firstOrCreate(
            ['description' => 'Actualización de software contable'],
            [
                'client_id' => $client2->id,
                'contact_person' => 'Marta Gómez',
                'contact_phone' => '622334455',
                'contact_email' => 'mgomez@suministrosglobales.es',
                'address' => 'Avenida de la Constitución 45',
                'city' => 'Leganés',
                'postal_code' => '28911',
                'province_code' => '28',
                'status' => 'pending',
                'operator_id' => $operator2->id,
                'previous_notes' => 'Requiere backup previo'
            ]
        );

        // Nuevas Tareas adicionales
        Task::firstOrCreate(
            ['description' => 'Revisión periódica de equipos'],
            [
                'client_id' => $client1->id,
                'contact_person' => 'Carlos Pérez',
                'contact_phone' => '611223344',
                'contact_email' => 'cperez@tecavanzadas.com',
                'address' => 'Calle Falsa 123',
                'city' => 'Madrid',
                'postal_code' => '28001',
                'province_code' => '28',
                'status' => 'pending',
                'operator_id' => $operator1->id,
                'previous_notes' => 'Revisar especialmente los portátiles del departamento técnico.'
            ]
        );

        Task::firstOrCreate(
            ['description' => 'Migración a la nube'],
            [
                'client_id' => $client2->id,
                'contact_person' => 'Marta Gómez',
                'contact_phone' => '622334455',
                'contact_email' => 'mgomez@suministrosglobales.es',
                'address' => 'Avenida de la Constitución 45',
                'city' => 'Leganés',
                'postal_code' => '28911',
                'province_code' => '28',
                'status' => 'pending',
                'operator_id' => $operator1->id,
                'previous_notes' => 'El cliente quiere mover su base de datos a AWS.'
            ]
        );

        Task::firstOrCreate(
            ['description' => 'Sustitución de monitor averiado'],
            [
                'client_id' => $client1->id,
                'contact_person' => 'Elena Rivas',
                'contact_phone' => '644556677',
                'contact_email' => 'erivas@tecavanzadas.com',
                'address' => 'Calle Falsa 123',
                'city' => 'Madrid',
                'postal_code' => '28001',
                'province_code' => '28',
                'status' => 'done',
                'operator_id' => $operator2->id,
                'completion_date' => '2026-01-14',
                'previous_notes' => 'Monitor Dell 24 pulgadas parpadea.',
                'posterior_notes' => 'Se sustituyó por un monitor nuevo de 27 pulgadas. El antiguo se envió a reciclar.'
            ]
        );

        Task::firstOrCreate(
            ['description' => 'Instalación de antivirus corporativo'],
            [
                'client_id' => $client2->id,
                'contact_person' => 'Jorge Sanz',
                'contact_phone' => '699887766',
                'contact_email' => 'jsanz@suministrosglobales.es',
                'address' => 'Avenida de la Constitución 45',
                'city' => 'Leganés',
                'postal_code' => '28911',
                'province_code' => '28',
                'status' => 'pending',
                'operator_id' => $operator2->id,
                'previous_notes' => 'Instalar en los 15 equipos de administración.'
            ]
        );
    }
}
