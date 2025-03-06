<?php
include('conn.php');

// Fetch all unique service types dynamically from the database
$serviceTypesQuery = "SELECT DISTINCT ServiceType FROM appointment";
$serviceTypesResult = $conn->query($serviceTypesQuery);

$serviceTypes = [];
while ($row = $serviceTypesResult->fetch_assoc()) {
    $types = explode(',', $row['ServiceType']); // Handle multiple services per appointment
    foreach ($types as $type) {
        $trimmedType = trim($type);
        if (!empty($trimmedType) && !in_array($trimmedType, $serviceTypes)) {
            $serviceTypes[] = $trimmedType;
        }
    }
}

// Initialize data storage
$labels = [];
$dataCount = [];
$dataPercentage = [];
$totalAppointments = 0;

// Generate random colors for each service
$colors = [];
foreach ($serviceTypes as $serviceType) {
    $query = "SELECT COUNT(*) as ServiceCount FROM appointment WHERE FIND_IN_SET('$serviceType', ServiceType)";
    $result = $conn->query($query);
    $count = $result && $result->num_rows > 0 ? $result->fetch_assoc()['ServiceCount'] : 0;
    
    $totalAppointments += $count;
    $labels[] = $serviceType;
    $dataCount[] = $count;
    $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // Generate a random hex color
}

// Calculate percentages
foreach ($dataCount as $count) {
    $dataPercentage[] = $totalAppointments > 0 ? ($count / $totalAppointments) * 100 : 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Pie Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            overflow: hidden;
        }
        .chart-container {
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary" id="toggleChartBtn">Show Percentage</button>
                        </div>
                        <h4 class="card-title text-center" id="chartTitle">Number of Services per Type</h4>
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels = <?php echo json_encode($labels); ?>;
        const dataCount = <?php echo json_encode($dataCount); ?>;
        const dataPercentage = <?php echo json_encode($dataPercentage); ?>;
        const backgroundColors = <?php echo json_encode($colors); ?>;
        
        const ctx = document.getElementById('pieChart').getContext('2d');
        let showPercentage = false;
        
        let currentChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataCount,
                    backgroundColor: backgroundColors,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        document.getElementById('toggleChartBtn').addEventListener('click', function() {
            showPercentage = !showPercentage;
            currentChart.data.datasets[0].data = showPercentage ? dataPercentage : dataCount;
            document.getElementById('chartTitle').innerText = showPercentage ? "Service Type Ratio (%)" : "Number of Services per Type";
            this.innerText = showPercentage ? "Show Count" : "Show Percentage";
            currentChart.update();
        });
    </script>
</body>
</html>
