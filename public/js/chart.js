const ctx = document.getElementById('usersChart');

async function getInformation() {
    const response = await fetch('/stats/users-per-month');
    const result = await response.json();
    return result;
}

if (ctx) {
    getInformation().then(result => {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: result.labels,
                datasets: [{
                    label: 'Usuarios registrados',
                    data: result.data,
                    borderWidth: 2,
                    tension: 0.3,
                       // LÍNEA
            borderColor: '#3b82f6',        // azul
            borderWidth: 2,

            // PUNTOS
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#3b82f6',
            pointRadius: 4,
            pointHoverRadius: 6,

            tension: 0.4
                }]
            },
            options: {
        responsive: true,
        scales: {
            x: {
                grid: {
                    color: '#ffffff33'   // líneas blancas suaves
                },
                ticks: {
                    color: '#ffffff'
                }
            },
            y: {
                grid: {
                    color: '#ffffff33'
                },
                ticks: {
                    color: '#ffffff'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff'
                }
            }
        }
    }
        });
    });
}

