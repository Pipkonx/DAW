<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamousInvestor;

class FamousInvestorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            'bill-gates' => [
                'name' => 'Bill Gates',
                'cik' => '0001166559',
                'avatar' => 'https://financialmodelingprep.com/image-stock/MSFT.png',
                'description' => "La cartera de inversión de Bill & Melinda Gates Foundation Trust refleja un enfoque estratégico en empresas estables y de alto rendimiento, demostrando su confianza en su valor duradero y potencial de crecimiento a largo plazo. A través del fideicomiso de la Fundación—que gestiona activos de inversión y transfiere los beneficios para lograr objetivos benéficos—Gates hace hincapié en empresas con fundamentos sólidos y modelos de negocio sostenibles.",
                'location' => 'EE.UU.',
                'type' => 'Público'
            ],
            'warren-buffett' => [
                'name' => 'Warren Buffett (Berkshire)',
                'cik' => '0001067983',
                'avatar' => 'https://financialmodelingprep.com/image-stock/BRK-B.png',
                'description' => "Warren Buffett, conocido como el 'Oráculo de Omaha', es el presidente y CEO de Berkshire Hathaway. Su filosofía de inversión se centra en el 'Value Investing', buscando empresas subestimadas por el mercado con ventajas competitivas sólidas (moats) y una gestión excelente.",
                'location' => 'Omaha, EE.UU.',
                'type' => 'Público'
            ],
            'michael-burry' => [
                'name' => 'Michael Burry (Scion)',
                'cik' => '0001649339',
                'avatar' => 'https://ui-avatars.com/api/?name=MB&background=random',
                'description' => "Inmortalizado en 'The Big Short', Michael Burry es un inversor y médico estadounidense que fundó Scion Asset Management. Es célebre por su capacidad para identificar burbujas financieras y activos infravalorados.",
                'location' => 'California, EE.UU.',
                'type' => 'Privado'
            ],
            'cathie-wood' => [
                'name' => 'Cathie Wood (ARK)',
                'cik' => '0001605941',
                'avatar' => 'https://ui-avatars.com/api/?name=CW&background=random',
                'description' => "Fundadora y CEO de ARK Invest, Cathie Wood es una de las voces más influyentes en la inversión en innovación obstructiva. Sus fondos se centran en tecnologías que cambian el mundo.",
                'location' => 'Florida, EE.UU.',
                'type' => 'Público'
            ],
            'bill-ackman' => [
                'name' => 'Bill Ackman (Pershing Square)',
                'cik' => '0001336528',
                'avatar' => 'https://ui-avatars.com/api/?name=BA&background=random',
                'description' => "Bill Ackman es el fundador y CEO de Pershing Square Capital Management. Como inversor activista, se especializa en tomar participaciones significativas en grandes corporaciones para impulsar cambios estructurales.",
                'location' => 'Nueva York, EE.UU.',
                'type' => 'Público'
            ],
            'ray-dalio' => [
                'name' => 'Ray Dalio (Bridgewater)',
                'cik' => '0001006415',
                'avatar' => 'https://ui-avatars.com/api/?name=RD&background=random',
                'description' => "Fundador de Bridgewater Associates, el fondo de cobertura más grande del mundo. Dalio es conocido por sus principios de 'transparencia radical' y su enfoque en ciclos económicos.",
                'location' => 'Connecticut, EE.UU.',
                'type' => 'Privado'
            ],
            'ken-griffin' => [
                'name' => 'Ken Griffin (Citadel)',
                'cik' => '0001423053',
                'avatar' => 'https://ui-avatars.com/api/?name=KG&background=random',
                'description' => "Fundador y CEO de Citadel, una de las firmas de inversión alternativas más exitosas. Griffin es un pionero en el trading cuantitativo y la creación de mercado.",
                'location' => 'Miami, EE.UU.',
                'type' => 'Privado'
            ],
            'jim-simons' => [
                'name' => 'Jim Simons (Renaissance)',
                'cik' => '0001037389',
                'avatar' => 'https://ui-avatars.com/api/?name=JS&background=random',
                'description' => "Matemático y fundador de Renaissance Technologies. Su fondo Medallion es considerado el más exitoso de la historia, basado exclusivamente en modelos algorítmicos complejos.",
                'location' => 'Nueva York, EE.UU.',
                'type' => 'Privado'
            ]
        ];

        foreach ($profiles as $slug => $data) {
            FamousInvestor::updateOrCreate(
                ['slug' => $slug],
                $data
            );
        }
    }
}
