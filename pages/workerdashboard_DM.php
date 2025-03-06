<?php
include('conn.php');

// Get the maximum number of workers in any preparedBy field
$maxWorkersQuery = "SELECT MAX(LENGTH(preparedBy) - LENGTH(REPLACE(preparedBy, ',', '')) + 1) AS max_workers FROM quotationForm";
$maxWorkersResult = mysqli_query($conn, $maxWorkersQuery);
$maxWorkersRow = mysqli_fetch_assoc($maxWorkersResult);
$maxWorkers = (int)$maxWorkersRow['max_workers'];

// Build the dynamic union for n values
$unionSelects = [];
for ($i = 1; $i <= $maxWorkers; $i++) {
    $unionSelects[] = "SELECT $i AS n";
}
$unionSelectString = implode(" UNION ALL ", $unionSelects);

// SQL Query to count each worker separately
$query = "SELECT 
    TRIM(LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(q.preparedBy, ',', n.n), ',', -1))) AS worker_name,
    COUNT(*) AS worker_count
FROM quotationForm q
INNER JOIN ($unionSelectString) n
    ON LENGTH(q.preparedBy) - LENGTH(REPLACE(q.preparedBy, ',', '')) >= n.n - 1
GROUP BY worker_name
HAVING worker_name <> ''";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['worker_name'];
    $data[] = $row['worker_count'];
}

$labels_json = json_encode($labels);
$data_json = json_encode($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Status Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            justify-content: center;
            overflow: hidden; /* Prevent scrolling */
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            height: 100%;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        .non-scrollable {
    overflow: hidden;
}

/* Hide scrollbars for webkit-based browsers (Chrome, Safari) */
.non-scrollable::-webkit-scrollbar {
    display: none;
}
        canvas {
            width: 100% ;
            height: 100% ;
            max-height: 500px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            h4 {
                font-size: 16px;
            }
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h4 style="margin-bottom: 20px; color: #333;">Workers Status</h4>
        <canvas id="pieChart"></canvas>
        <?php if (empty($labels) || empty($data)) echo "<p style='color: red; text-align: center;'>No data available to display.</p>"; ?>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = <?php echo $labels_json; ?>;
            const data = <?php echo $data_json; ?>;

            var ctx = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            "#FF4000", "#FFCE56", "#4CAF50", "#36A2EB", "#9966FF",
                            "#FF6384", "#A52A2A", "#008000", "#0000FF", "#FFFF00",
                            "#800080", "#00FFFF", "#C0C0C0"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    var index = tooltipItem.dataIndex;
                                    return `${labels[index]}: Count ${data[index]}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
