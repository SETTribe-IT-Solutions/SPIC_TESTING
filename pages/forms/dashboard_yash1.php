<?php
include('conn.php');

// Step 1: Create an array for all months in 2025
$months = [];
$startDate = new DateTime("2025-01-01");
$endDate = new DateTime("2025-12-01");

while ($startDate <= $endDate) {
    $monthKey = $startDate->format("M Y");
    $months[$monthKey] = ["customers" => 0, "totalAmount" => 0];
    $startDate->modify("+1 month");
}

// Function to fetch and align data
function fetchData($conn, $sql, $key, &$months) {
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        if (!isset($row['year']) || !isset($row['month'])) {
            continue;
        }

        $monthKey = date("M Y", strtotime("{$row['year']}-{$row['month']}-01"));
        if (isset($months[$monthKey])) {
            $months[$monthKey][$key] = (int)($row['value'] ?? 0);
        }
    }
}

// Fetch Data for Customers (2025 only)
$sqlCustomers = "
    SELECT 
        YEAR(appointmentDate) AS year,
        MONTH(appointmentDate) AS month,
        COUNT(appointmentId) AS value
    FROM appointment
    WHERE YEAR(appointmentDate) = 2025
    GROUP BY YEAR(appointmentDate), MONTH(appointmentDate)
    ORDER BY YEAR(appointmentDate), MONTH(appointmentDate);
";
fetchData($conn, $sqlCustomers, "customers", $months);

// Fetch Data for Total Amount (2025 only)
$sqlAmount = "
   SELECT 
    YEAR(a.appointmentDate) AS year, 
    MONTH(a.appointmentDate) AS month,
    COALESCE(SUM(q.amount), 0) AS value
FROM quotationForm q
JOIN appointment a ON q.appointmentId = a.appointmentId  -- Correct Join
WHERE YEAR(a.appointmentDate) = 2025
GROUP BY YEAR(a.appointmentDate), MONTH(a.appointmentDate)
ORDER BY YEAR(a.appointmentDate), MONTH(a.appointmentDate);
";
fetchData($conn, $sqlAmount, "totalAmount", $months);

$conn->close();

// Step 4: Prepare Data for Chart.js
$monthLabels = array_keys($months);
$customerCounts = array_column($months, "customers");
$totalAmounts = array_column($months, "totalAmount");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: linear-gradient(to bottom, #f0f8ff, #e6f0ff);
             text-align: center;
            margin: 0;
            padding: 20px;
             overflow-x: hidden;
        }
         .dashboard-container {
             display: flex;
             flex-wrap: wrap;
              justify-content: center;
            gap: 20px;
             padding: 20px;
         }
        .card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.7) 0%, rgba(255, 255, 255, 0.4) 100%);
            border-radius: 12px;
            padding: 20px;
             box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 240px;
             text-align: center;
            color: #333;
             position: relative;
            overflow: hidden;
         }
        .card::before,
        .card::after {
             content: "";
            position: absolute;
             border-radius: 12px;
              z-index: -1;
            transition: all 0.4s ease-in-out;
         }
        /* Before Element */
        .card::before {
            top: 5px;
             left: 5px;
             right: 5px;
             bottom: 5px;
            background: rgba(255, 255, 255, 0.7);
              box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
             opacity: 0;
             transform: scale(0.9);
         }

        /* After Element */
        .card::after {
            top: 3px;
             left: 3px;
            right: 3px;
             bottom: 3px;
              background: rgba(255, 255, 255, 0.4);
             opacity: 0;
             transform: scale(0.8);
         }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
         }
        /* Hover on ::before and ::after */
         .card:hover::before,
        .card:hover::after {
            opacity: 1;
             transform: scale(1);
         }
         .card::after {
             box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
          }
        .icon {
            font-size: 2.3rem;
            margin-bottom: 10px;
            color: #286090; /* Updated Icon Color */
         }
         .metric {
           font-size: 1.6rem;
             font-weight: bold;
          }
        .metric-title {
            font-size: 0.9rem;
            text-transform: uppercase;
              color: #555;
              margin-top: 5px;
        }
         @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
                align-items: center;
           }
            .card {
                width: 90%;
             }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <div class="icon"><i class="fas fa-users"></i></div>
            <div class="metric"><?php echo array_sum($customerCounts); ?></div>
            <div class="metric-title">Total Customers</div>
        </div>
        <div class="card">
            <div class="icon"><i class="fas fa-coins"></i></div>
            <div class="metric"><?php echo number_format(array_sum($totalAmounts), 2); ?></div>
            <div class="metric-title">Total Revenue</div>
        </div>
        <div class="card">
            <div class="icon"><i class="fas fa-arrow-trend-up"></i></div>
            <div class="metric"><?php echo max($customerCounts); ?></div>
            <div class="metric-title">Peak Month Customers</div>
        </div>
    </div>
</body>
</html>