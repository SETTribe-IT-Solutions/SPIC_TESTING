<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Types Bar Chart</title>
    <!-- Importing Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Chart container style */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50vh;
        }

        /* Card and container for chart styling */
        .col-lg-6.grid-margin.stretch-card {
            margin-top: 20px;
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Service Types Bar Chart</h4>
                <!-- Canvas to render the Bar Chart -->
                <canvas id="barChart" width="150" height="50" style="display: block; width: 300px; height: 150px;" class="chartjs-render-monitor"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Fetch the data from the PHP script
        fetch('fetch-appointments.php')
    .then(response => response.json())
    .then(data => {
        if (data.length === 0) {
            console.error("No data received.");
            return;
        }

        // Extract labels (ServiceType) and data (service_count)
        const labels = data.map(item => item.ServiceType);
        const serviceCounts = data.map(item => parseInt(item.service_count));

        // Dynamically generate colors if there are many services
        const backgroundColors = labels.map((_, i) => `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.2)`);
        const borderColors = backgroundColors.map(color => color.replace('0.2', '1')); // Adjust border opacity

        // Create the bar chart using Chart.js
        const ctx = document.getElementById('barChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Service Types Count',
                    data: serviceCounts,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })
    .catch(error => console.error('Error fetching data:', error));
    </script>
</body>

</html>
