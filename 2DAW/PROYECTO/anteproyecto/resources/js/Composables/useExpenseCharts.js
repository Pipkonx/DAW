import { computed } from 'vue';
import { formatCurrency } from '@/Utils/formatting';

/**
 * Encapsulates chart configuration logic for Expenses Dashboard
 * @param {Object} props - Component props containing chart data
 */
export function useExpenseCharts(props) {
    
    // Trend Chart (Line Chart)
    const trendChartData = computed(() => ({
        labels: props.charts.trend.labels,
        datasets: [
            {
                label: 'Saldo Acumulado',
                data: props.charts.trend.balance,
                borderColor: '#3b82f6', // Blue 500
                backgroundColor: (context) => {
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
                    return gradient;
                },
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                tension: 0.4,
                fill: true,
            }
        ]
    }));

    const trendChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (context) => `Saldo: ${formatCurrency(context.parsed.y)}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { font: { size: 10 }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 10 }
            }
        }
    };

    // Category Chart (Doughnut)
    const categoryChartData = computed(() => {
        const colors = ['#f43f5e', '#fb923c', '#fbbf24', '#a3e635', '#34d399', '#22d3ee', '#818cf8', '#e879f9'];
        return {
            labels: props.charts.categories.labels,
            datasets: [{
                data: props.charts.categories.data,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        };
    });

    const categoryChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, font: { size: 11 } } }
        },
        cutout: '70%'
    };

    // Monthly Chart (Bar Chart)
    const monthlyChartData = computed(() => ({
        labels: props.charts.monthly.labels,
        datasets: [
            {
                label: 'Ingresos',
                data: props.charts.monthly.income,
                backgroundColor: '#10b981',
                borderRadius: 4,
            },
            {
                label: 'Gastos',
                data: props.charts.monthly.expense,
                backgroundColor: '#f43f5e',
                borderRadius: 4,
            },
            {
                label: 'Ahorro',
                data: props.charts.monthly.savings,
                backgroundColor: '#3b82f6',
                borderRadius: 4,
            }
        ]
    }));

    const monthlyChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', align: 'end' },
            tooltip: {
                callbacks: {
                    label: (context) => `${context.dataset.label}: ${formatCurrency(context.parsed.y)}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { font: { size: 10 }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#94a3b8' }
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
