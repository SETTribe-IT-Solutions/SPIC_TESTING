<?php
include('conn.php');

// Default: fetch data for current month
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = date('Y');

// Query to fetch weekly data for the selected month
$weeklyQuery = "SELECT 
            YEAR(dateTime) AS year,
            WEEK(dateTime, 1) AS week,
            MIN(dateTime) AS first_day_of_week,
            SUM(amount) AS totalAmount
          FROM quotationForm
          WHERE MONTH(dateTime) = $month AND YEAR(dateTime) = $year
          GROUP BY YEAR(dateTime), WEEK(dateTime, 1)
          ORDER BY first_day_of_week ASC";

$result = $conn->query($weeklyQuery);

$weeks = [];
$amounts = [];
$weekCount = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $weekCount++;
      if ($weekCount > 5){
         continue;
      }

      $weeks[] = "Week " . $weekCount;
      $amounts[] = $row['totalAmount'];
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Balance Chart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 407px;
        }
        body {
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card p-3">
                    <div class="card-body">
                        <form action="" method="GET">
                            <!-- Dropdown for selecting month inside the form -->
                            <div class="mb-3">
                                <label for="month-dropdown" class="form-label">Select Month</label>
                                <select id="month-dropdown" name="month" class="form-select" onchange="this.form.submit()">
                                    <option value="1" <?= $month == 1 ? 'selected' : '' ?>>January</option>
                                    <option value="2" <?= $month == 2 ? 'selected' : '' ?>>February</option>
                                    <option value="3" <?= $month == 3 ? 'selected' : '' ?>>March</option>
                                    <option value="4" <?= $month == 4 ? 'selected' : '' ?>>April</option>
                                    <option value="5" <?= $month == 5 ? 'selected' : '' ?>>May</option>
                                    <option value="6" <?= $month == 6 ? 'selected' : '' ?>>June</option>
                                    <option value="7" <?= $month == 7 ? 'selected' : '' ?>>July</option>
                                    <option value="8" <?= $month == 8 ? 'selected' : '' ?>>August</option>
                                    <option value="9" <?= $month == 9 ? 'selected' : '' ?>>September</option>
                                    <option value="10" <?= $month == 10 ? 'selected' : '' ?>>October</option>
                                    <option value="11" <?= $month == 11 ? 'selected' : '' ?>>November</option>
                                    <option value="12" <?= $month == 12 ? 'selected' : '' ?>>December</option>
                                </select>
                            </div>
                        </form>

                        <div class="d-flex align-items-center justify-content-between">
                            <p class="card-title fw-bold">Weekly Balance</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="fw-bold mb-0 me-3">₹<?= number_format(array_sum($amounts), 2) ?></h5>
                        </div>
                        <div class="chart-container">
                            <canvas id="balance-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var weeks = <?= json_encode($weeks) ?>;
        var amounts = <?= json_encode($amounts) ?>;
        
        var ctx = document.getElementById('balance-chart').getContext('2d');
        var balanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [{
                    label: 'Total Amount',
                    data: amounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMin: 1000,
                        suggestedMax: 3000
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>