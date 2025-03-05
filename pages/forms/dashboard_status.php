<?php
header('Content-Type: application/json');
include('conn.php'); // Include database connection

// Fetch data from `appointment` table
$sql = "SELECT 
            LOWER(status) AS status,  
            COUNT(appointmentId) AS value
        FROM appointment
        WHERE YEAR(appointmentDate) = 2025
        AND status IN ('pending', 'confirmed', 'cancelled', 'completed')
        GROUP BY status";

$result = $conn->query($sql);

// Check if query was successful
if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

// Prepare JSON output
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Return JSON response
echo json_encode($data);
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Chart - Appointment Status</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        .card {
            width: 90%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body>

<div class="card">
    <h3>Regional Load - Appointment Status</h3>
    <p class="text-muted">Last update: 2 Hours ago</p>
    <div class="chart-container">
        <canvas id="regional-chart"></canvas>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("fetch_data.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error:", data.error);
                return;
            }

            let labels = ["Pending", "Cancelled", "Completed", "Confirmed"];
            let chartData = { "pending": 0, "cancelled": 0, "completed": 0, "confirmed": 0 };

            // If no data is returned, chartData will remain at default (0 for all statuses)
            data.forEach(entry => {
                if (chartData.hasOwnProperty(entry.status)) {
                    chartData[entry.status] = entry.value;
                }
            });

            // Render Chart
            let ctx = document.getElementById("regional-chart").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Number of Customers",
                        data: labels.map(label => chartData[label.toLowerCase()]),
                        backgroundColor: ["#1cbccd", "#ff4d4d", "#4caf50", "#ffbf36"],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Fetch error:", error));
});
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Chart - Appointment Status</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        .card {
            width: 90%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
        
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<div class="card">
    <h3>Regional Load - Appointment Status</h3>
    <p class="text-muted">Last update: 2 Hours ago</p>
    <div class="chart-container">
        <canvas id="regional-chart"></canvas>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("dashboard_status.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error:", data.error);
                return;
            }

            let labels = ["Pending", "Cancelled", "Completed", "Confirmed"];
            let chartData = { "pending": 0, "cancelled": 0, "completed": 0, "confirmed": 0 };

            // If no data is returned, chartData will remain at default (0 for all statuses)
            data.forEach(entry => {
                if (chartData.hasOwnProperty(entry.status)) {
                    chartData[entry.status] = entry.value;
                }
            });

            // Render Chart
            let ctx = document.getElementById("regional-chart").getContext("2d");
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Number of Customers",
                        data: labels.map(label => chartData[label.toLowerCase()]),
                        backgroundColor: ["#1cbccd", "#ff4d4d", "#4caf50", "#ffbf36"],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        })
        .catch(error => console.error("Fetch error:", error));
});
</script>

</body>
</html>
