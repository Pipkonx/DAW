<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

/**
 * @class EmpresaSeeder
 * @brief Seeder encargado de generar las empresas de prueba con CIF y sectores realistas.
 */
class EmpresaSeeder extends Seeder
{
    /**
     * @brief Ejecuta el sembrado de datos para la tabla empresas.
     */
    public function run(): void
    {
        $this->crearEmpresasPrueba();
    }

    /**
     * @brief Inserta empresas con datos realistas.
     */
    protected function crearEmpresasPrueba(): void
    {
        $empresas = [
            [
                'nombre' => 'Tech Innovación S.L.',
                'cif' => 'B12345678',
                'sector' => 'Tecnología de la Información',
                'direccion' => 'Parque Tecnológico, Edificio 1',
                'localidad' => 'Málaga',
                'provincia' => 'Málaga',
                'codigo_postal' => '29004',
                'telefono' => '952000001',
                'email' => 'contacto@techinnova.es',
                'web' => 'https://techinnova.es',
                'activa' => true,
            ],
            [
                'nombre' => 'Sistemas Avanzados S.A.',
                'cif' => 'A87654321',
                'sector' => 'Ciberseguridad',
                'direccion' => 'Avenida Principal, 45',
                'localidad' => 'Madrid',
                'provincia' => 'Madrid',
                'codigo_postal' => '28001',
                'telefono' => '910000002',
                'email' => 'info@sisavanzados.com',
                'web' => 'https://sisavanzados.com',
                'activa' => true,
            ],
            [
                'nombre' => 'Soluciones Web Mediterráneo',
                'cif' => 'B55566677',
                'sector' => 'Desarrollo Web',
                'direccion' => 'Calle del Mar, 12',
                'localidad' => 'Valencia',
                'provincia' => 'Valencia',
                'codigo_postal' => '46002',
                'telefono' => '960000003',
                'email' => 'hola@webmed.es',
                'web' => 'https://webmed.es',
                'activa' => true,
            ],
        ];

        foreach ($empresas as $empresa) {
            Empresa::create($empresa);
        }
    }
}
