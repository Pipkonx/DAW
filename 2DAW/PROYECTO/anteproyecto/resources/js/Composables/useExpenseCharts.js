import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';

/**
 * Encapsulates chart configuration logic for Expenses Dashboard
 * @param {Object} props - Component props containing chart data
 */
export function useExpenseCharts(props) {
    
    // Trend Chart (Line Chart -> Area Chart Premium)
    const trendChartData = computed(() => ({
        labels: props.charts.trend.labels,
        datasets: [
            {
                label: 'Saldo Acumulado',
                data: props.charts.trend.balance,
                borderColor: '#6366f1', // Indigo 500
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 6,
                tension: 0.5,
                fill: true,
            }
        ]
    }));

    const trendChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 20 },
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 12,
                cornerRadius: 10,
                callbacks: {
                    label: (context) => ` Saldo: ${formatCurrency(context.parsed.y)}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                ticks: { font: { size: 10, weight: '500' }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10, weight: '500' }, color: '#94a3b8', maxTicksLimit: 10 }
            }
        }
    };

    // Category Chart (Doughnut Moderno - Paleta Azul y Ordenado)
    const categoryChartData = computed(() => {
        const labels = props.charts.categories?.labels || [];
        const data = props.charts.categories?.data || [];
        
        // Crear array de objetos para ordenar
        const combined = labels.map((label, i) => ({
            label: label,
            value: data[i] || 0
        })).sort((a, b) => b.value - a.value);

        const colors = [
            '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', 
            '#eab308', '#10b981', '#14b8a6', '#0ea5e9'
        ];

        return {
            labels: combined.map(c => c.label),
            datasets: [{
                data: combined.map(c => c.value),
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 15,
                borderRadius: 8,
                spacing: 3
            }]
        };
    });


    const categoryChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 20 },
        plugins: {
            legend: { 
                position: 'right', 
                labels: { 
                    usePointStyle: true, 
                    pointStyle: 'circle',
                    font: { size: 11, weight: '500' },
                    color: '#64748b',
                    padding: 15
                } 
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 12,
                cornerRadius: 10
            }
        },
        cutout: '75%'
    };

    // Monthly Chart (Bar Chart Premium - Paleta Azul Contrastada)
    const monthlyChartData = computed(() => ({
        labels: props.charts.monthly.labels,
        datasets: [
            {
                label: 'Ingresos',
                data: props.charts.monthly.income,
                backgroundColor: '#0ea5e9', // Sky 500 (Vibrante)
                borderRadius: 6,
                hoverBackgroundColor: '#0284c7'
            },
            {
                label: 'Gastos',
                data: props.charts.monthly.expense,
                backgroundColor: '#1e1b4b', // Indigo 900 (Contraste oscuro)
                borderRadius: 6,
                hoverBackgroundColor: '#312e81'
            },
            {
                label: 'Ahorro',
                data: props.charts.monthly.savings,
                backgroundColor: '#6366f1', // Indigo 500
                borderRadius: 6,
                hoverBackgroundColor: '#4f46e5'
            }
        ]
    }));


    const monthlyChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 20 },
        plugins: {
            legend: { 
                position: 'top', 
                align: 'end',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 15,
                    font: { size: 11, weight: '500' },
                    color: '#64748b'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                padding: 12,
                cornerRadius: 10,
                callbacks: {
                    label: (context) => ` ${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(148, 163, 184, 0.05)', drawBorder: false },
                ticks: { font: { size: 10, weight: '500' }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10, weight: '500' }, color: '#94a3b8' }
            }
        }
    };


    return {
        trendChartData,
        trendChartOptions,
        categoryChartData,
        categoryChartOptions,
        monthlyChartData,
        monthlyChartOptions
    };
}
