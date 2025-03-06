<?php
include('conn.php');

// Fetch data from the appointment table and aggregate by service type
$query = "
   SELECT 
    ServiceType,
    COUNT(*) AS CustomerCount,
    ROUND((COUNT() * 100.0 / (SELECT COUNT() FROM appointment)), 2) AS ServicePercentage
FROM appointment
GROUP BY ServiceType;
";

$result = $conn->query($query);

// Prepare data for Chart.js
$labels = [];
$data = [];
$customerCounts = []; // Store number of customers
$colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#8E44AD', '#F39C12']; // Colors for the chart

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['ServiceType']; // Extracting unique services
        $data[] = $row['ServicePercentage']; // Percentage values
        $customerCounts[] = $row['CustomerCount']; // Store customer count
    }
}

// Convert to JSON for JavaScript
$jsonLabels = json_encode($labels);
$jsonData = json_encode($data);
$jsonCustomerCounts = json_encode($customerCounts);
$jsonColors = json_encode($colors);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Type Pie Chart</title>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include chartjs-plugin-datalabels for showing data labels -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styling for the chart container */
        .chart-container {
            position: relative;
            height: 400px; /* Adjust height */
        }
        /* Hide the scrollbar for the whole body */

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Service Type Ratio</h4>
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // PHP data converted to JavaScript variables
        const labels = <?php echo $jsonLabels; ?>;
        const data = <?php echo $jsonData; ?>;
        const customerCounts = <?php echo $jsonCustomerCounts; ?>;
        const backgroundColors = <?php echo $jsonColors; ?>;

        // Initialize Chart.js
        const ctx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#121212', 
                        formatter: (value, context) => {
                            const index = context.dataIndex;
                            return labels[index] + ': ' + customerCounts[index] + ' Customers (' + value.toFixed(2) + '%)'; 
                        },
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>