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
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        /* Main container */
        .container {
            width: 100%;
            height: 100%;
            max-width: 1100px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Heading style */
        .heading {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Card and chart container style */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            margin-top: 20px;
        }

        .col-lg-6.grid-margin.stretch-card {
            margin-top: 10px;
        }

        .card-body {
            padding: 20px;
        }

        /* Responsive handling */
        @media (max-width: 768px) {
            .chart-container {
                padding: 10px;
                width: 100%;
            }

            .heading {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .heading {
                font-size: 1.2rem;
            }
        }

        /* Canvas styling */
        canvas {
            max-width: 100% !important;
            height: auto !important;
        }

        /* Hide the scrollbar for the body */
        body {
            overflow: hidden;
        }

        /* If you want to hide the scrollbar for specific elements */
        .some-element-class {
            overflow: hidden;
        }

        /* For webkit-based browsers (Chrome, Safari) */
        .some-element-class::-webkit-scrollbar {
            display: none;
        }

        /* For Firefox */
        .some-element-class {
            scrollbar-width: none;
        }
    </style>
</head>

<body>
    <!-- Main container div -->
    <div class="container">
    <h2 align="center">Service Types Bar Chart</h2>
    <div class="chart-container">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Canvas to render the Bar Chart -->
                    <canvas id="barChart" width="500" height="200" style="display: block; width: 900px; height: 500px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
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
