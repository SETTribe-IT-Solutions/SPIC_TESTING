<?php
include ('conn.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Spica Admin</title>

    <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">

    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
     <!-- base:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
 
</head>

<body>
 
  <?php


// SQL query to count the number of appointmentIds
$sql = "SELECT COUNT(appointmentId) AS totalCustomers FROM appointment"; // Change your_table_name to the actual table name
$result = $conn->query($sql);

// SQL query to count 'pending' status in the status column
$sql_pending = "SELECT COUNT(status) AS pending_count FROM appointment WHERE status = 'Pending'";
$result_pending = $conn->query($sql_pending);
$row_pending = $result_pending->fetch_assoc();
$pending_count = $row_pending['pending_count'];
$totalCustomers = 0; // Default value

if ($result->num_rows > 0) {
    // Fetch the result and assign the count value
    $row = $result->fetch_assoc();
    $totalCustomers = $row['totalCustomers'];
}

// SQL query to fetch total amounts per month
$sql_total_cost = "SELECT YEAR(datetime) AS year, MONTH(datetime) AS month, SUM(amount) AS totalAmount
                   FROM appointment
                   GROUP BY YEAR(datetime), MONTH(datetime)
                   ORDER BY YEAR(datetime) DESC, MONTH(datetime) DESC";
$result_total_cost = $conn->query($sql_total_cost); // Corrected this line

$months = [];
$totalAmounts = [];

// Fetch the data
if ($result_total_cost->num_rows > 0) {
  while ($row = $result_total_cost->fetch_assoc()) {
    // Prepare the data for chart
    $months[] = $row['month'] . '-' . $row['year']; // Combine month and year for display
    $totalAmounts[] = $row['totalAmount']; // Total amount for the month
  }
}
$conn->close();
?>




<div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
    <p class="card-title"></p>
    <p class="text-muted">Detaiils of sales </p>
    <div class="row mb-3">
      <div class="col-md-7">
        <div class="d-flex justify-content-between traffic-status">
        
  <div class="item">
  <p class="mb-">Total customers</p>
  <h5 class="font-weight-bold mb-0">
    <?php echo number_format($totalCustomers); ?> <!-- Display the count here -->
  </h5>
  <div class="color-border"></div>
  <div class="item">
  <p class="mb-">Total Car Count</p>
  <h5 class="font-weight-bold mb-0">
    <?php echo number_format($totalCustomers); ?> <!-- Display the count here -->
  </h5>
  <div class="color-border"></div>
</div>
<div class="item">
  <p class="mb-">Total Cars pending  Count</p>
  <h5 class="font-weight-bold mb-0">
    <?php echo number_format($pending_count); ?> <!-- Display the count here -->
  </h5>

  <div class="item">
  <p class="mb-">Total collection</p>
  <h5 class="font-weight-bold mb-0">
    <?php echo number_format($totalAmount); ?> <!-- Display the count here -->
  </h5>


  <div class="color-border"></div>
</div>

      </div>
      <div class="col-md-5">
        <ul class="nav nav-pills nav-pills-custom justify-content-md-end" id="pills-tab-custom" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab-custom" data-toggle="pill" href="#pills-health" role="tab" aria-controls="pills-home" aria-selected="true">
              Day
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab-custom" data-toggle="pill" href="#pills-career" role="tab" aria-controls="pills-profile" aria-selected="false">
              Week
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="pills-contact-tab-custom" data-toggle="pill" href="#pills-music" role="tab" aria-controls="pills-contact" aria-selected="false">
              Month
            </a>
          </li>
        </ul>
      </div>
    </div>
    <canvas id="audience-chart" width="962" height="476" style="display: block; height: 159px; width: 321px;" class="chartjs-render-monitor"></canvas>
  </div>



  <script>
(function($) {
  'use strict';
  $(function() {
    if ($("#audience-chart").length) {
      var AudienceChartCanvas = $("#audience-chart").get(0).getContext("2d");

      // PHP data passed to JavaScript
      var months = <?php echo json_encode($months); ?>; // Labels (Month-Year)
      var totalAmounts = <?php echo json_encode($totalAmounts); ?>; // Total amounts

      var AudienceChart = new Chart(AudienceChartCanvas, {
        type: 'bar',
        data: {
          labels: months, // Use dynamically fetched months
          datasets: [
            {
              label: 'Total Collection', // Label for the dataset
              data: totalAmounts, // Use dynamically fetched amounts
              backgroundColor: '#1cbccd' // Example color for the bars
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 20,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              display: true,
              gridLines: {
                display: true,
                drawBorder: false,
                color: "#f8f8f8",
                zeroLineColor: "#f8f8f8"
              },
              ticks: {
                display: true,
                min: 0, // Adjust according to your dataset
                stepSize: Math.ceil(Math.max(...totalAmounts) / 5), // Dynamic step size
                fontColor: "#b1b0b0",
                fontSize: 10,
                padding: 10
              }
            }],
            xAxes: [{
              ticks: {
                beginAtZero: true,
                fontColor: "#b1b0b0",
                fontSize: 10
              },
              gridLines: {
                color: "rgba(0, 0, 0, 0)",
                display: false
              },
              barPercentage: .9,
              categoryPercentage: .7,
            }]
          },
          legend: {
            display: true // Show the legend
          }
        },
      });
    }
  })
})(jQuery);
</script>

          
          <!-- row end -->
        </div>
        <!-- content-wrapper ends -->
     
       
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- base:js -->
  <script src="../../vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="../../vendors/chart.js/Chart.min.js"></script>
  <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="../../js/off-canvas.js"></script>
  <script src="../../js/hoverable-collapse.js"></script>
  <script src="../../js/template.js"></script>
  <!-- endinject -->
  <!-- plugin js for this page -->
    <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page -->
  <!-- Custom js for this page-->
  <script src="../../js/dashboard.js"></script>
  <!-- End custom js for this page-->
</body>

</html>
  <script src="../../js/hoverable-collapse.js"></script>
  <script src="../../js/template.js"></script>
  <!-- endinject -->
  <!-- plugin js for this page -->
    <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page -->
  <!-- Custom js for this page-->
  <script src="../../js/dashboard.js"></script>
  <!-- End custom js for this page-->
</body>

</html>