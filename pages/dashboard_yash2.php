<?php
include ('conn.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Step 1: Initialize Data Structure
$months = [];
$statuses = ['pending', 'confirmed', 'cancelled', 'completed'];

for ($i = 1; $i <= 12; $i++) {
    $monthKey = date("F", mktime(0, 0, 0, $i, 1));
    $months[$monthKey] = array_fill_keys($statuses, 0);
}

// Step 2: Fetch Appointment Status Counts Per Month
$sqlStatus = "
    SELECT 
        YEAR(appointmentDate) AS year,
        MONTH(appointmentDate) AS month,
        status,
        COUNT(appointmentId) AS value
    FROM appointment
    WHERE YEAR(appointmentDate) = 2025
    AND status IN ('pending', 'confirmed', 'cancelled', 'completed')
    GROUP BY YEAR(appointmentDate), MONTH(appointmentDate), status
    ORDER BY YEAR(appointmentDate), MONTH(appointmentDate);
";

$stmt = $pdo->prepare($sqlStatus);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    $monthKey = date("F", mktime(0, 0, 0, $row['month'], 1));
    $months[$monthKey][$row['status']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status Overview</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 30px;
        }
        .month-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 20px;
        }
        .month-button {
            margin: 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            transition: background 0.3s ease;
        }
        .month-button:hover, .selected {
            background-color: #0056b3;
        }
        .chart-container {
            padding: 20px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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
    scrollbar-width: none; /* Firefox */
}
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Appointment Status Overview (2025)</h2>

    <!-- Month Selection Buttons -->
    <div class="month-buttons">
        <?php foreach (array_keys($months) as $index => $month): ?>
            <button class="month-button" onclick="updateChart(<?php echo $index + 1; ?>)"><?php echo $month; ?></button>
        <?php endforeach; ?>
    </div>

    <!-- Chart Container -->
    <div class="chart-container">
        <canvas id="statusChart"></canvas>
    </div>
</div>

<script>
    let chartData = <?php echo json_encode($months); ?>;
    let selectedMonthIndex = 1;

    let ctx = document.getElementById("statusChart").getContext("2d");
    let statusChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Pending", "Confirmed", "Cancelled", "Completed"],
            datasets: [{
                label: "Number of Appointments",
                data: [],
                backgroundColor: ["#f39c12", "#28a745", "#e74c3c", "#3498db"],
                borderColor: ["#c87f0a", "#1e7d32", "#c0392b", "#2471a3"],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function updateChart(monthIndex) {
        selectedMonthIndex = monthIndex;
        let monthName = Object.keys(chartData)[monthIndex - 1];
        let monthData = chartData[monthName];

        statusChart.data.datasets[0].data = [
            monthData.pending || 0,
            monthData.confirmed || 0,
            monthData.cancelled || 0,
            monthData.completed || 0
        ];
        statusChart.update();

        // Highlight the selected button
        document.querySelectorAll('.month-button').forEach((btn, index) => {
            btn.classList.toggle('selected', index + 1 === monthIndex);
        });
    }

    // Initialize chart with January data
    updateChart(1);
</script>

</body>
</html>
