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
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
  /* General Styles */

    .metric { font-size: 28px; font-weight: bold; color: #2c3e50; }
    .metric-title { font-size: 16px; color: #7f8c8d; }
    .chart-container { 
        padding: 20px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
       
    }
    #dataSelector {

  max-width: 175px; /* Optional: set max width for large screens */
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 0.25rem;
margin-left: 80%;
}

@media (max-width: 768px) {
  #dataSelector {
  
    max-width:170px ; /* Optional: set a max width for tablets */
    font-size: 0.9rem; /* Smaller font size */
  
  }
}

@media (max-width: 576px) {
  #dataSelector {
   
    max-width: 100px; /* Optional: set a max width for phones */
    font-size: 0.8rem; /* Even smaller font size */
 margin-right: 90%;
 font-size: 0.5rem;
  }
}
/* Basic styling */
.form-select {
    background: #ffffff; /* Clean white background */
    border: 2px solid #223e9c; /* Initial border */
    border-radius: 10px; /* Rounded corners */
    padding: 10px 15px; /* Padding for text */
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease-in-out; /* Smooth transition for interactions */
    color: #223e9c; /* Default text color */
    outline: none; /* Remove outline on focus */
}

/* Hover and focus effect */
.form-select:hover, .form-select:focus {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); /* Light shadow effect */
    border-color: #1a2f7a; /* Darker border on hover/focus */
    transform: translateY(-2px); /* Slight lift on hover */
}

/* Change text color to white when the select is hovered or focused */
.form-select:hover, .form-select:focus {
    color: #ffffff;
    background-color: #223e9c; /* Background color on hover/focus */
}

/* Option styling */
.form-select option {
    background-color: #ffffff;
    color: #223e9c;
}

/* Smooth transition for option selection */
.form-select option:hover {
    background-color: #1a2f7a; /* Darken the background on hover */
    color: #ffffff; /* Change text color on hover */
}
/* Hide the scrollbar for the whole body */
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
    scrollbar-width: none; /* Firefox */
}
  </style>
</head>
<body>



    <!-- Metrics Section -->
 
    <!-- Combined Chart -->
    <div class="chart-container">
    <h4 class="text-center">Monthly Data Overview</h4>
        <select id="dataSelector" class="form-select">
    <option value="customers">Number of Customers</option>
    <option value="revenue">Total Revenue</option>
</select>

            <br>
        <canvas id="combined-chart"></canvas>
        
    </div>
</div>

<script>
  var ctx = document.getElementById("combined-chart").getContext("2d");

  var combinedChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: <?php echo json_encode($monthLabels); ?>,
      datasets: [
        {
          label: "Number of Customers",
          data: <?php echo json_encode($customerCounts); ?>,
          backgroundColor: "rgba(41, 128, 185, 0.7)",
          borderColor: "rgba(41, 128, 185, 1)",
          borderWidth: 2
        },
        {
          label: "Total Revenue",
          data: <?php echo json_encode($totalAmounts); ?>,
          backgroundColor: "rgba(142, 68, 173, 0.7)",
          borderColor: "rgba(142, 68, 173, 1)",
          borderWidth: 2,
          hidden: true // Initially hidden
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        x: { stacked: false },
        y: { beginAtZero: true }
      }
    }
  });

  // Dropdown Change Event
  document.getElementById('dataSelector').addEventListener('change', function() {
      let selectedValue = this.value;
      
      if (selectedValue === "customers") {
          combinedChart.data.datasets[0].hidden = false;  // Show Customers
          combinedChart.data.datasets[1].hidden = true;   // Hide Revenue
      } else {
          combinedChart.data.datasets[0].hidden = true;   // Hide Customers
          combinedChart.data.datasets[1].hidden = false;  // Show Revenue
      }
      
      combinedChart.update();
  });
</script>

</body>
</html>
