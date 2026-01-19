// Renderizado de gráficos con Chart.js

function labelsMes() {
  return ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
}

export function renderCharts(monthly, summary) {
  const ingresosMensuales = [];
  const gastosMensuales = [];
  for (let m = 1; m <= 12; m++) {
    const d = monthly[m] || { ingresos: 0, gastos: 0 };
    ingresosMensuales.push(d.ingresos);
    gastosMensuales.push(d.gastos);
  }

  const ctxBar = document.getElementById('chartMensual');
  if (ctxBar) {
    // Destruir gráfico previo si existe para evitar solapamiento
    const chartExistente = Chart.getChart(ctxBar);
    if (chartExistente) chartExistente.destroy();

    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: labelsMes(),
        datasets: [
          { label: 'Ingresos', data: ingresosMensuales, backgroundColor: 'rgba(37, 99, 235, 0.6)' },
          { label: 'Gastos', data: gastosMensuales, backgroundColor: 'rgba(239, 68, 68, 0.6)' },
        ]
      },
      options: { responsive: true }
    });
  }

  const ctxPie = document.getElementById('chartAhorro');
  if (ctxPie) {
    const chartExistente = Chart.getChart(ctxPie);
    if (chartExistente) chartExistente.destroy();

    new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: ['Ahorro', 'Gastos'],
        datasets: [{ data: [summary.ahorro || 0, summary.gastos || 0], backgroundColor: ['#10b981', '#ef4444'] }]
      },
      options: { responsive: true }
    });
  }
}
