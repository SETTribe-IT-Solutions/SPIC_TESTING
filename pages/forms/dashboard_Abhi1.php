<?php
include ('conn.php');

// Fetch the monthly data
$monthlyQuery = "SELECT 
            YEAR(dateTime) AS year,
            MONTH(dateTime) AS month,
            SUM(amount) AS totalAmount
          FROM quotationForm
          GROUP BY YEAR(dateTime), MONTH(dateTime)
          ORDER BY YEAR(dateTime) DESC, MONTH(dateTime)";

$monthlyResult = $conn->query($monthlyQuery);

$months = [];
$amounts = [];

if ($monthlyResult->num_rows > 0) {
    while($row = $monthlyResult->fetch_assoc()) {
        $months[] = date("M", mktime(0, 0, 0, $row['month'], 10));
        $amounts[] = $row['totalAmount'];
    }
} else {
    echo "0 results";
}

// Fetch the yearly data
$yearlyQuery = "SELECT 
            YEAR(dateTime) AS year,
            SUM(amount) AS totalAmount
          FROM quotationForm
          GROUP BY YEAR(dateTime)
          ORDER BY YEAR(dateTime)";

$yearlyResult = $conn->query($yearlyQuery);

$years = [];
$yearAmounts = [];

if ($yearlyResult->num_rows > 0) {
    while($row = $yearlyResult->fetch_assoc()) {
        $years[] = $row['year'];
        $yearAmounts[] = $row['totalAmount'];
    }
} else {
    echo "0 results";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Monthly and Yearly Balance</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Styling for responsive card and charts */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f7fc;
    }

    .card {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      background-color: #fff;
      transition: transform 0.3s ease-in-out;
      opacity: 0;
      transform: translateY(20px);
    }

    .card.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .col-md-6 {
      max-width: 100%;
      margin: 0 auto;
    }

    .card-body {
      padding: 0;
    }

    .d-flex {
      display: flex;
      justify-content: space-between;
    }

    .text-success {
      color: #28a745;
    }

    .text-muted {
      color: #6c757d;
    }

    canvas {
      width: 100% !important;
      height: 550px !important;
    }
  </style>
</head>
<body>

<!-- Monthly Balance Form -->
<div class="col-md-6 stretch-card">
  <div class="card" id="monthly-graph-card">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <p class="card-title">Monthly Balance</p>    
      </div>
      <div class="d-flex align-items-center flex-wrap mb-3">
      </div>
      <canvas id="monthly-balance-chart"></canvas>
    </div>
  </div>
</div>

<br>
<br>

<!-- Yearly Balance Form -->
<div class="col-md-6 stretch-card">
  <div class="card" id="yearly-graph-card">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between flex-wrap">
        <p class="card-title">Yearly Balance</p>
      </div>
      <div class="d-flex align-items-center flex-wrap mb-3">
        <p class="text-muted mb-0">Avg Sessions</p>
      </div>
      <canvas id="yearly-balance-chart"></canvas>
    </div>
  </div>
</div>

<script>
  // Monthly Data
  const months = <?php echo json_encode($months); ?>;
  const amounts = <?php echo json_encode($amounts); ?>;

  // Yearly Data
  const years = <?php echo json_encode($years); ?>;
  const yearAmounts = <?php echo json_encode($yearAmounts); ?>;

  // Monthly Chart Initialization
  const monthlyCtx = document.getElementById('monthly-balance-chart').getContext('2d');
  const monthlyBalanceChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: months, // Dynamic months from PHP
      datasets: [{
        label: 'Amount',
        data: amounts, // Dynamic amounts from PHP
        borderColor: '#28a745',
        borderWidth: 2,
        fill: false,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
      },
      scales: {
        x: {
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          max: Math.max(...amounts) + 500,
          grid: {
            color: '#e0e0e0'
          }
        }
      }
    }
  });

  // Yearly Chart Initialization
  const yearlyCtx = document.getElementById('yearly-balance-chart').getContext('2d');
  const yearlyBalanceChart = new Chart(yearlyCtx, {
    type: 'line',
    data: {
      labels: years, // Dynamic years from PHP
      datasets: [{
        label: 'Amount',
        data: yearAmounts, // Dynamic yearly amounts from PHP
        borderColor: '#17a2b8',
        borderWidth: 2,
        fill: false,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
      },
      scales: {
        x: {
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          max: Math.max(...yearAmounts) + 5000,
          grid: {
            color: '#e0e0e0'
          }
        }
      }
    }
  });

  // Transition Effect for Cards
  window.addEventListener('load', () => {
    const monthlyCard = document.getElementById('monthly-graph-card');
    const yearlyCard = document.getElementById('yearly-graph-card');
    setTimeout(() => {
      monthlyCard.classList.add('visible');
      yearlyCard.classList.add('visible');
    }, 10); // Delay to trigger the effect after load
  });
</script>

</body>
</html>
