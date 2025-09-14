document.addEventListener("DOMContentLoaded", () => {
    const rows = window.sensorData || [];
    if (rows.length === 0) return;

    // format timestamp -> YYYY-MM-DD HH:mm:ss
    function formatTime(ts) {
        const d = new Date(ts);
        if (isNaN(d)) return ts; // fallback kalau gagal
        return d.getFullYear() + "-" +
            String(d.getMonth() + 1).padStart(2, "0") + "-" +
            String(d.getDate()).padStart(2, "0") + " " +
            String(d.getHours()).padStart(2, "0") + ":" +
            String(d.getMinutes()).padStart(2, "0") + ":" +
            String(d.getSeconds()).padStart(2, "0");
    }

    // ambil label waktu sudah diformat
    const labels = rows.map(r => formatTime(r.time)).reverse();

    // ambil data beberapa sensor
    const temp1 = rows.map(r => r["pt-100-temperature-1"] ?? null).reverse();
    const temp2 = rows.map(r => r["pt-100-temperature-2"] ?? null).reverse();

    // helper buat chart
    function makeChart(ctxId, label, data, color) {
        const ctx = document.getElementById(ctxId);
        if (!ctx) return;

        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: color.replace("1)", "0.2)"),
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    x: {
                        ticks: { maxRotation: 45, minRotation: 45 }
                    },
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    }

    // bikin chart
    makeChart("chart-temp-1", "PT-100 Temp 1 (°C)", temp1, "rgba(75, 192, 192, 1)");
    makeChart("chart-temp-2", "PT-100 Temp 2 (°C)", temp2, "rgba(255, 99, 132, 1)");
});
