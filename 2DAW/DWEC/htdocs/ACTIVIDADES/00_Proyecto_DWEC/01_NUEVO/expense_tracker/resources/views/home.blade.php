@extends('layouts.app')

@section('content')
    <div>
        <!-- Top Navigation Bar -->
        <header
            >
            <div>
                <div>
                    <div>
                        <svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.25 14.25L7.5 13l1.41-1.41L10.75 13.5l4.84-4.84L17 10.07l-6.25 6.18z">
                            </path>
                        </svg>
                    </div>
                    <h1>Finanzas</h1>
                </div>
                <div>
                    <a href="{{ route('transacciones.create') }}"
                        >
                        <span>Añadir Transacción</span>
                    </a>
                    <button
                        >
                        <span>settings</span>
                    </button>
                    <div
                        data-alt="User profile picture"
                        >
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main>
            <div>
                @if (session('status'))
                    <div
                        role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div>
                    <!-- Stats Cards -->
                    <div
                        >
                        <p>Ingresos Totales
                        </p>
                        <p>{{ number_format($totalIngresos, 2, ',', '.') }} €</p>
                        <p>+5.2%</p>
                    </div>
                    <div
                        >
                        <p>Gastos Totales
                        </p>
                        <p>{{ number_format($totalGastos, 2, ',', '.') }} €</p>
                        <p>+8.1%</p>
                    </div>
                    <div
                        >
                        <p>Ahorro Total</p>
                        <p>{{ number_format($ahorroTotal, 2, ',', '.') }} €</p>
                        <p>44% de Ingresos
                        </p>
                    </div>
                    <div
                        >
                        <p>Inversiones
                            Totales</p>
                        <p>{{ number_format($totalInversiones, 2, ',', '.') }} €
                        </p>
                        <p>+12.5%</p>
                    </div>
                </div>
                <!-- Charts Grid -->
                <div>
                    <!-- Ingresos por Mes -->
                    <div
                        >
                        <div>
                            <div>
                                <p>Ingresos por Mes</p>
                                <p>Últimos 6 meses</p>
                            </div>
                            @php
                                $totalIngresosMensuales = array_sum($ingresosMensuales);
                            @endphp
                            <p>{{ number_format($totalIngresosMensuales, 2, ',', '.') }} €</p>
                        </div>
                        <div
                            >
                            @php
                                $maxIngreso = max($ingresosMensuales) ?: 1;
                            @endphp
                            @foreach($ingresosMensuales as $index => $ingreso)
                                @php
                                    $height = ($ingreso / $maxIngreso) * 100;
                                    $isMax = $ingreso == $maxIngreso;
                                    $colorClass = $isMax ? 'bg-primary' : 'bg-primary/20';
                                    // Limit height to 90% like in reference
                                    $height = $height * 0.9;
                                @endphp
                                <div></div>
                            @endforeach
                        </div>
                        <div>
                            @foreach($mesesLabels as $label)
                                <p>
                                    {{ $label }}</p>
                            @endforeach
                        </div>
                    </div>
                    <!-- Ahorro Acumulado -->
                    <div
                        >
                        <div>
                            <p>Ahorro Acumulado</p>
                            <p>Este Año</p>
                        </div>
                        <p>{{ number_format($ahorroTotal, 2, ',', '.') }}
                            €</p>
                        <div>
                            <!-- Placeholder for dynamic SVG Line Chart - Using static for now as implementing dynamic SVG path in pure Blade is complex without JS helper, but structure matches -->
                            <svg fill="none" height="100%" preserveAspectRatio="none" viewBox="0 0 478 150" width="100%"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25"
                                    stroke="#13ec5b" stroke-linecap="round" stroke-width="3"></path>
                                <defs>
                                    <linearGradient gradientUnits="userSpaceOnUse" id="paint0_linear_area" x1="236" x2="236"
                                        y1="1" y2="149">
                                        <stop stop-color="#13ec5b" stop-opacity="0.3"></stop>
                                        <stop offset="1" stop-color="#13ec5b" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                    </div>
                    <!-- Gastos por categoría -->
                    <div
                        >
                        <div>
                            <p>Gastos por Categoría</p>
                            <p>Este Mes</p>
                        </div>
                        <div>
                            <div>
                                <svg viewBox="0 0 36 36">
                                    <circle cx="18" cy="18"
                                        fill="none" r="15.9155" stroke-width="4"></circle>
                                    @php
                                        $total = array_reduce($gastosPorCategoria, function ($carry, $item) {
                                            return $carry + $item['amount']; }, 0);
                                        $offset = 25; // Initial offset
                                        $colors = ['text-sky-400', 'text-indigo-400', 'text-amber-400', 'text-rose-400'];
                                    @endphp
                                    @foreach($gastosPorCategoria as $index => $item)
                                        @php
                                            $percentage = $total > 0 ? ($item['amount'] / $total) * 100 : 0;
                                            $dashArray = "{$percentage}, 100";
                                            $color = $colors[$index % count($colors)];
                                        @endphp
                                        <circle cx="18" cy="18" fill="none" r="15.9155"
                                            stroke-dasharray="{{ $dashArray }}" stroke-dashoffset="{{ $offset }}"
                                            stroke-width="4"></circle>
                                        @php
                                            $offset -= $percentage;
                                        @endphp
                                    @endforeach
                                </svg>
                                <div>
                                    <span>€</span>
                                    <span>Total
                                        Gastos</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            @foreach($gastosPorCategoria as $index => $item)
                                @php
                                    $bgColors = ['bg-sky-400', 'bg-indigo-400', 'bg-amber-400', 'bg-rose-400'];
                                    $bgColor = $bgColors[$index % count($bgColors)];
                                @endphp
                                <div>
                                    <div></div><span>
                                        {{ $item['category'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection