<script>
    // Chart Initialization for Dashboard
    (function() {
        if (typeof Chart === 'undefined') return;

        const months = <?php echo json_encode(array_column($chart_data ?? [], 'month') ?: ['Jan', 'Feb', 'Mar']); ?>;
        const counts = <?php echo json_encode(array_column($chart_data ?? [], 'count') ?: [0, 0, 0]); ?>;
        const catLabels = <?php echo json_encode(array_column($category_data ?? [], 'category') ?: ['Belum Ada Data']); ?>;
        const catCounts = <?php echo json_encode(array_column($category_data ?? [], 'count') ?: [1]); ?>;

        const regCtx = document.getElementById('registrationChart')?.getContext('2d');
        if (regCtx) {
            new Chart(regCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pendaftaran',
                        data: counts,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, border: { display: false }, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { border: { display: false }, grid: { display: false } }
                    }
                }
            });
        }

        const catCtx = document.getElementById('categoryChart')?.getContext('2d');
        if (catCtx) {
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catCounts,
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#7c3aed', '#ef4444', '#64748b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20, font: { family: 'Plus Jakarta Sans', weight: '600' } } }
                    },
                    cutout: '75%'
                }
            });
        }
    })();
</script>
