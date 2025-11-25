<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Database;
use Illuminate\Support\Facades\Auth;
use PDO;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = auth()->id();
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Obtener el balance actual
        $stmtBalance = $pdo->prepare("SELECT tipo, monto FROM transacciones WHERE user_id = :user_id");
        $stmtBalance->execute(['user_id' => $userId]);
        $transactionsBalance = $stmtBalance->fetchAll();

        $balance = 0;
        foreach ($transactionsBalance as $transaction) {
            if ($transaction['tipo'] === 'ingreso' || $transaction['tipo'] === 'inversion') {
                $balance += $transaction['monto'];
            } else {
                $balance -= $transaction['monto'];
            }
        }

        // --- Datos para la gráfica mensual diaria (ingresos, gastos, inversión) ---
        $currentMonth = date('m');
        $currentYear = date('Y');

        $stmtMonthlyDaily = $pdo->prepare("SELECT fecha, tipo, monto FROM transacciones WHERE user_id = :user_id AND MONTH(fecha) = :month AND YEAR(fecha) = :year ORDER BY fecha ASC");
        $stmtMonthlyDaily->execute(['user_id' => $userId, 'month' => $currentMonth, 'year' => $currentYear]);
        $monthlyDailyTransactions = $stmtMonthlyDaily->fetchAll(PDO::FETCH_ASSOC);

        $dailyData = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $i);
            $dailyData[$date] = ['ingreso' => 0, 'gasto' => 0, 'inversion' => 0];
        }

        foreach ($monthlyDailyTransactions as $transaction) {
            $date = $transaction['fecha'];
            $tipo = $transaction['tipo'];
            $monto = $transaction['monto'];
            if (isset($dailyData[$date][$tipo])) {
                $dailyData[$date][$tipo] += $monto;
            }
        }

        $monthlyDailyLabels = array_map(function ($date) {
            return date('d', strtotime($date));
        }, array_keys($dailyData));

        $monthlyDailyIngresos = array_column($dailyData, 'ingreso');
        $monthlyDailyGastos = array_column($dailyData, 'gasto');
        $monthlyDailyInversiones = array_column($dailyData, 'inversion');

        // --- Datos para la gráfica anual mensual (ingresos, gastos, inversión) ---
        $stmtAnnualMonthly = $pdo->prepare("SELECT MONTH(fecha) as mes, tipo, SUM(monto) as total FROM transacciones WHERE user_id = :user_id AND YEAR(fecha) = :year GROUP BY MONTH(fecha), tipo ORDER BY mes ASC");
        $stmtAnnualMonthly->execute(['user_id' => $userId, 'year' => $currentYear]);
        $annualMonthlyTransactions = $stmtAnnualMonthly->fetchAll(PDO::FETCH_ASSOC);

        $annualMonthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $annualMonthlyData[$i] = ['ingreso' => 0, 'gasto' => 0, 'inversion' => 0];
        }

        foreach ($annualMonthlyTransactions as $transaction) {
            $mes = (int)$transaction['mes'];
            $tipo = $transaction['tipo'];
            $total = $transaction['total'];
            if (isset($annualMonthlyData[$mes][$tipo])) {
                $annualMonthlyData[$mes][$tipo] += $total;
            }
        }

        $monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $annualMonthlyLabels = array_map(function ($mes) use ($monthNames) {
            return $monthNames[$mes - 1];
        }, array_keys($annualMonthlyData));

        $annualMonthlyIngresos = array_column($annualMonthlyData, 'ingreso');
        $annualMonthlyGastos = array_column($annualMonthlyData, 'gasto');
        $annualMonthlyInversiones = array_column($annualMonthlyData, 'inversion');

        // --- Datos para la gráfica de ingresos por mes (últimos 6 meses) ---
        $ingresosMensuales = [];
        $mesesLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i month"));
            $year = date('Y', strtotime("-$i month"));
            $mesesLabels[] = date('M', strtotime("-$i month")); // e.g., Jan, Feb

            $stmtIngresosMes = $pdo->prepare("SELECT SUM(monto) FROM transacciones WHERE user_id = :user_id AND tipo = 'ingreso' AND MONTH(fecha) = :month AND YEAR(fecha) = :year");
            $stmtIngresosMes->execute(['user_id' => $userId, 'month' => $month, 'year' => $year]);
            $ingresosMensuales[] = (float) $stmtIngresosMes->fetchColumn();
        }

        // --- Datos para la gráfica total anual (ingresos, gastos, inversión) ---
        $stmtTotalAnnual = $pdo->prepare("SELECT YEAR(fecha) as ano, tipo, SUM(monto) as total FROM transacciones WHERE user_id = :user_id GROUP BY YEAR(fecha), tipo ORDER BY ano ASC");
        $stmtTotalAnnual->execute(['user_id' => $userId]);
        $totalAnnualTransactions = $stmtTotalAnnual->fetchAll(PDO::FETCH_ASSOC);

        $totalAnnualData = [];
        foreach ($totalAnnualTransactions as $transaction) {
            $ano = (int)$transaction['ano'];
            $tipo = $transaction['tipo'];
            $total = $transaction['total'];
            if (!isset($totalAnnualData[$ano])) {
                $totalAnnualData[$ano] = ['ingreso' => 0, 'gasto' => 0, 'inversion' => 0];
            }
            if (isset($totalAnnualData[$ano][$tipo])) {
                $totalAnnualData[$ano][$tipo] += $total;
            }
        }

        $totalAnnualLabels = array_keys($totalAnnualData);

        $totalAnnualIngresos = array_column($totalAnnualData, 'ingreso');
        $totalAnnualGastos = array_column($totalAnnualData, 'gasto');
        $totalAnnualInversiones = array_column($totalAnnualData, 'inversion');

        // --- Datos para la gráfica de patrimonio total acumulado ---
        $stmtPatrimonio = $pdo->prepare("SELECT fecha, tipo, monto FROM transacciones WHERE user_id = :user_id ORDER BY fecha ASC");
        $stmtPatrimonio->execute(['user_id' => $userId]);
        $patrimonioTransactions = $stmtPatrimonio->fetchAll(PDO::FETCH_ASSOC);

        $patrimonioLabels = [];
        $patrimonioData = [];
        $inversionAcumuladaData = [];
        $currentPatrimonio = 0;
        $currentInversionAcumulada = 0;

        foreach ($patrimonioTransactions as $transaction) {
            $fecha = $transaction['fecha'];
            $tipo = $transaction['tipo'];
            $monto = $transaction['monto'];

            if ($tipo === 'ingreso' || $tipo === 'inversion') {
                $currentPatrimonio += $monto;
                if ($tipo === 'inversion') {
                    $currentInversionAcumulada += $monto;
                }
            } else {
                $currentPatrimonio -= $monto;
            }

            if (!empty($patrimonioLabels) && end($patrimonioLabels) === $fecha) {
                $patrimonioData[count($patrimonioData) - 1] = $currentPatrimonio;
                $inversionAcumuladaData[count($inversionAcumuladaData) - 1] = $currentInversionAcumulada;
            } else {
                $patrimonioLabels[] = $fecha;
                $patrimonioData[] = $currentPatrimonio;
                $inversionAcumuladaData[] = $currentInversionAcumulada;
            }
        }

        // --- Datos para la gráfica circular de ingresos vs inversiones ---
        $stmtTotalIngresos = $pdo->prepare("SELECT SUM(monto) FROM transacciones WHERE user_id = :user_id AND tipo = 'ingreso'");
        $stmtTotalIngresos->execute(['user_id' => $userId]);
        $totalIngresos = $stmtTotalIngresos->fetchColumn();

        $stmtTotalGastos = $pdo->prepare("SELECT SUM(monto) FROM transacciones WHERE user_id = :user_id AND tipo = 'gasto'");
        $stmtTotalGastos->execute(['user_id' => $userId]);
        $totalGastos = $stmtTotalGastos->fetchColumn();

        $stmtTotalInversiones = $pdo->prepare("SELECT SUM(monto) FROM transacciones WHERE user_id = :user_id AND tipo = 'inversion'");
        $stmtTotalInversiones->execute(['user_id' => $userId]);
        $totalInversiones = $stmtTotalInversiones->fetchColumn();

        // --- Datos para la gráfica de gastos por categoría ---
        $stmtGastosPorCategoria = $pdo->prepare("SELECT c.nombre as categoria, SUM(t.monto) as total_gasto FROM transacciones t JOIN categorias c ON t.category_id = c.id WHERE t.user_id = :user_id AND t.tipo = 'gasto' GROUP BY c.nombre ORDER BY total_gasto DESC");
        $stmtGastosPorCategoria->execute(['user_id' => $userId]);
        $gastosPorCategoriaRaw = $stmtGastosPorCategoria->fetchAll(PDO::FETCH_ASSOC);

        // Transformar datos para coincidir con el formato esperado por JavaScript
        $gastosPorCategoria = [];
        $colors = ['#87CEEB', '#8A2BE2', '#FFC107', '#E91E63', '#4CAF50', '#FF9800', '#9C27B0', '#00BCD4']; // Colores coincidentes con la vista
        foreach ($gastosPorCategoriaRaw as $index => $item) {
            $gastosPorCategoria[] = [
                'amount' => (float) $item['total_gasto'],
                'color' => $colors[$index % count($colors)],
                'categoria' => $item['categoria']
            ];
        }

        $gastosPorCategoriaLabels = array_column($gastosPorCategoria, 'categoria');
        $gastosPorCategoriaData = array_column($gastosPorCategoria, 'amount');

        // Calcular Ahorro Total
        $ahorroTotal = $totalIngresos - $totalGastos;

        return view('home', compact('balance', 'monthlyDailyLabels', 'monthlyDailyIngresos', 'monthlyDailyGastos', 'monthlyDailyInversiones', 'annualMonthlyLabels', 'annualMonthlyIngresos', 'annualMonthlyGastos', 'annualMonthlyInversiones', 'totalAnnualLabels', 'totalAnnualIngresos', 'totalAnnualGastos', 'totalAnnualInversiones', 'patrimonioLabels', 'patrimonioData', 'inversionAcumuladaData', 'totalIngresos', 'totalGastos', 'totalInversiones', 'ahorroTotal', 'currentPatrimonio', 'gastosPorCategoria', 'gastosPorCategoriaLabels', 'gastosPorCategoriaData', 'ingresosMensuales', 'mesesLabels'));
    }
}
