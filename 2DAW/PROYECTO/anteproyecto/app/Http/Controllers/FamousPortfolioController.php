<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\MarketDataService;
use App\Models\FamousInvestor;
use Inertia\Inertia;

class FamousPortfolioController extends Controller
{
    protected $marketDataService;

    // Mapeo de perfiles famosos
    protected $profiles = [
        'bill-gates' => [
            'name' => 'Bill Gates',
            'cik' => '0001166559',
            'avatar' => 'https://financialmodelingprep.com/image-stock/MSFT.png',
            'description' => "La cartera de inversión de Bill & Melinda Gates Foundation Trust refleja un enfoque estratégico en empresas estables y de alto rendimiento, demostrando su confianza en su valor duradero y potencial de crecimiento a largo plazo. A través del fideicomiso de la Fundación—que gestiona activos de inversión y transfiere los beneficios para lograr objetivos benéficos—Gates hace hincapié en empresas con fundamentos sólidos y modelos de negocio sostenibles. Sus inversiones suelen dirigirse a sectores esenciales para el desarrollo mundial y las infraestructuras, lo que subraya su compromiso tanto con el rendimiento financiero como con un impacto social más amplio.",
            'location' => 'EE.UU.',
            'type' => 'Público'
        ],
        'warren-buffett' => [
            'name' => 'Warren Buffett (Berkshire)',
            'cik' => '0001067983',
            'avatar' => 'https://financialmodelingprep.com/image-stock/BRK-B.png',
            'description' => "Warren Buffett, conocido como el 'Oráculo de Omaha', es el presidente y CEO de Berkshire Hathaway. Su filosofía de inversión se centra en el 'Value Investing', buscando empresas subestimadas por el mercado con ventajas competitivas sólidas (moats) y una gestión excelente. Es famoso por su paciencia y por mantener posiciones durante décadas.",
            'location' => 'Omaha, EE.UU.',
            'type' => 'Público'
        ],
        'michael-burry' => [
            'name' => 'Michael Burry (Scion)',
            'cik' => '0001649339',
            'avatar' => 'https://ui-avatars.com/api/?name=MB&background=random',
            'description' => "Inmortalizado en 'The Big Short', Michael Burry es un inversor y médico estadounidense que fundó Scion Asset Management. Es célebre por su capacidad para identificar burbujas financieras y activos infravalorados. Su enfoque es profundamente analítico, basado en el estudio exhaustivo de informes financieros y datos macroeconómicos.",
            'location' => 'California, EE.UU.',
            'type' => 'Privado'
        ],
        'cathie-wood' => [
            'name' => 'Cathie Wood (ARK)',
            'cik' => '0001605941',
            'avatar' => 'https://ui-avatars.com/api/?name=CW&background=random',
            'description' => "Fundadora y CEO de ARK Invest, Cathie Wood es una de las voces más influyentes en la inversión en innovación obstructiva. Sus fondos se centran en tecnologías que cambian el mundo: inteligencia artificial, robótica, almacenamiento de energía, secuenciación de ADN y tecnología blockchain.",
            'location' => 'Florida, EE.UU.',
            'type' => 'Público'
        ],
        'bill-ackman' => [
            'name' => 'Bill Ackman (Pershing Square)',
            'cik' => '0001336528',
            'avatar' => 'https://ui-avatars.com/api/?name=BA&background=random',
            'description' => "Bill Ackman es el fundador y CEO de Pershing Square Capital Management. Como inversor activista, se especializa en tomar participaciones significativas en grandes corporaciones para impulsar cambios estructurales y operativos que maximicen el valor para el accionista. Su enfoque es audaz y a menudo polémico.",
            'location' => 'Nueva York, EE.UU.',
            'type' => 'Público'
        ],
        'ray-dalio' => [
            'name' => 'Ray Dalio (Bridgewater)',
            'cik' => '0001006415',
            'avatar' => 'https://ui-avatars.com/api/?name=RD&background=random',
            'description' => "Fundador de Bridgewater Associates, el fondo de cobertura más grande del mundo. Dalio es conocido por sus principios de 'transparencia radical' y su enfoque en ciclos económicos y gestión de riesgos mediante el 'All Weather Portfolio'.",
            'location' => 'Connecticut, EE.UU.',
            'type' => 'Privado'
        ],
        'ken-griffin' => [
            'name' => 'Ken Griffin (Citadel)',
            'cik' => '0001423053',
            'avatar' => 'https://ui-avatars.com/api/?name=KG&background=random',
            'description' => "Fundador y CEO de Citadel, una de las firmas de inversión alternativas más exitosas. Griffin es un pionero en el trading cuantitativo y la creación de mercado, destacando por su enfoque matemático y tecnológico en las finanzas.",
            'location' => 'Miami, EE.UU.',
            'type' => 'Privado'
        ],
        'jim-simons' => [
            'name' => 'Jim Simons (Renaissance)',
            'cik' => '0001037389',
            'avatar' => 'https://ui-avatars.com/api/?name=JS&background=random',
            'description' => "Matemático y fundador de Renaissance Technologies. Su fondo Medallion es considerado el más exitoso de la historia, basado exclusivamente en modelos algorítmicos complejos aplicados a los mercados financieros.",
            'location' => 'Nueva York, EE.UU.',
            'type' => 'Privado'
        ]
    ];

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    public function show($slug)
    {
        // 1. Intentar obtener el inversor de la base de datos con sus relaciones
        $investor = FamousInvestor::where('slug', $slug)
            ->with([
                'holdings',
                'trades' => function ($q) {
                    $q->latest('filling_date')->take(20);
                }
            ])
            ->first();

        if (!$investor) {
            // Fallback al array estático si no existe en DB
            if (!isset($this->profiles[$slug])) {
                abort(404);
            }
            $profileData = $this->profiles[$slug];
        } else {
            // Usar datos de la DB enriquecidos por los estáticos si faltan campos
            $profileData = array_merge($this->profiles[$slug] ?? [], $investor->toArray());
        }

        $holdings = $investor ? $investor->holdings : [];
        $history = $investor ? $investor->trades : [];

        // Calcular estadísticas básicas
        $positionsCount = count($holdings);
        $lastReportDate = $investor && $investor->last_synced_at
            ? Carbon::parse($investor->last_synced_at)->format('d/m/Y')
            : 'Reciente';

        return Inertia::render('FamousPortfolios/Show', [
            'profile' => $profileData,
            'holdings' => $holdings,
            'history' => $history,
            'stats' => [
                'posiciones' => $positionsCount,
                'last_report' => $lastReportDate,
            ]
        ]);
    }
}
