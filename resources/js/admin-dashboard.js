import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const config = window.sabthDashboard;
    if (!config) {
        return;
    }

    Chart.defaults.animation.duration = 300;
    Chart.defaults.font.family = "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif";

    const lineCanvas = document.getElementById('orders-line-chart');
    if (lineCanvas) {
        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: config.line.labels,
                datasets: [{
                    label: 'Заказы',
                    data: config.line.data,
                    borderColor: '#5B3DF5',
                    backgroundColor: 'rgba(91, 61, 245, 0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#5B3DF5',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#667085' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#667085',
                        },
                        grid: { color: 'rgba(23, 32, 51, 0.06)' },
                    },
                },
            },
        });
    }

    const donutCanvas = document.getElementById('orders-donut-chart');
    if (donutCanvas && config.donut.data.length) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: config.donut.labels,
                datasets: [{
                    data: config.donut.data,
                    backgroundColor: config.donut.colors,
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 16,
                            color: '#172033',
                        },
                    },
                },
            },
        });
    }
});
