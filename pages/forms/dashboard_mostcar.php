<?php
// Include database connection
include('conn.php');

// SQL query to fetch the most serviced car companies and models
$sql = "SELECT companyName, carModel, COUNT(*) as serviceCount 
        FROM detailsOfCarsAndOwner
        GROUP BY companyName, carModel 
        ORDER BY serviceCount DESC 
        LIMIT 10";

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Initialize arrays for company names and service counts
$companies = [];
$counts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $companies[] = $row['companyName'] . ' ' . $row['carModel']; // Concatenating Company and Model
        $counts[] = $row['serviceCount'];
    }
} else {
    $companies[] = "No data found";
    $counts[] = 0;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Service Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
       body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .chart-container {
            width: 90%;
            max-width: 800px;
            height: 450px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
       .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background-color: #223e9c;
            color: #fff;
            border-bottom: 1px solid #223e9c;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }
        .card-body {
            padding: 20px;
        }
        .chart-title {
             font-size: 2rem;
             font-weight: bold;
             margin-bottom: 20px;
             text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Top 10 Car Services</h4>
            </div>
            <div class="card-body">
                 <h2 class="chart-title">Top 10 Car Services</h2>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('barChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($companies, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>,
                    datasets: [{
                        label: 'Number of Services',
                        data: <?php echo json_encode($counts, JSON_NUMERIC_CHECK); ?>,
                        backgroundColor: '#223e9c',
                        borderColor: '#223e9c',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000, // Animation duration
                        easing: 'easeOutQuart', // Smooth easing
                        x: { from: 0 } // Moves bars from left to right
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                    // scales: {
                    //     x: {
                    //         beginAtZero: true,
                    //         title: { display: true, text: 'Number of Services', color: '#343a40' }
                    //     },
                    //     y: {
                    //         title: { display: true, text: 'Car Models', color: '#343a40' },
                    //         ticks: {
                    //             font: {
                    //                 weight: 'bold', // Makes car models bold
                    //                 size: 14 // Increases font size for readability
                    //             },
                    //             align: 'center', // Centers text alignment
                    //         }
                    //     }
                    // }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>