<?php
include('conn.php');  // Ensure the connection is made to the database

// Function to generate a unique serviceId
function generateUniqueServiceId($conn) {
    // Generate a random number
    $random_number = rand(100000, 999999);  // Adjust the range as needed

    // Get the current timestamp (in seconds)
    $timestamp = time();

    // Combine the random number and timestamp to create a serviceId
    $serviceId = $random_number . $timestamp;

    // Check if the generated serviceId already exists in the database
    $sql_check = "SELECT COUNT(*) FROM serviceForm WHERE serviceId = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $serviceId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // If the serviceId already exists, call the function recursively to generate a new one
    if ($count > 0) {
        return generateUniqueServiceId($conn);  // Recursive call until unique ID is found
    }

    // Return the unique serviceId
    return $serviceId;
}

// Fetch the serviceForm data for displaying in the table with the customer's full name
$sql = "SELECT sf.id, sf.carModel, sf.serviceAmount, ef.fullName ,a.ServiceType 
        FROM serviceForm sf 
        JOIN 
        enquiryForm ef ON sf.customerId = ef.customer_Id
        JOIN 
        appointment a ON sf.customerId = a.customerId";
$result = $conn->query($sql);

// Fetch the customer names from the enquiryForm table for the dropdown
$sql_customers = "SELECT customer_Id, fullName FROM enquiryForm";
$result_customers = $conn->query($sql_customers);


// Handle deletion of records
if (isset($_GET['delete_id'])) {
  $deleteId = intval($_GET['delete_id']); // Sanitize the ID to prevent SQL injection
  $conn->query("DELETE FROM serviceForm WHERE id = $deleteId"); // Execute the delete query
  header("Location: {$_SERVER['PHP_SELF']}"); // Redirect to refresh the page
  exit();
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $customerId = $_POST['Customer'];  // customer selected from dropdown
    if (empty($customerId)) {
        echo "Customer ID is missing.";
        exit();  // Prevent form submission if no customer is selected
    }

    $carModel = $_POST['Car_Model'];
    $service = $_POST['Service'];
    $amount = $_POST['Amount'];
    $datetime = date("Y-m-d H:i:s"); // Get current system datetime

    // Fetch the customer's full_name from the enquiryForm table
    $sql_customer = "SELECT fullName FROM enquiryForm WHERE customer_id = '$customerId'";
    $stmt_customer = $conn->prepare($sql_customer);
    $stmt_customer->bind_param("i", $customerId);
    $stmt_customer->execute();
    $stmt_customer->bind_result($full_name);
    $stmt_customer->fetch();
    $stmt_customer->close();

    // Call function to generate a unique serviceId
    $serviceId = generateUniqueServiceId($conn);

    // Insert into serviceForm table (auto-generate id and serviceId)
    $sql_insert = "INSERT INTO serviceForm (serviceId, customerId, serviceAmount, status, datetime, carModel, full_name) 
                    VALUES (?, ?, ?, NULL, ?, ?, ?)";

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssss", $serviceId, $customerId, $amount, $datetime, $carModel, $full_name);

    // Execute the query
    if ($stmt_insert->execute()) {
        echo "Service booking has been successfully added!";
    } else {
        echo "Error: " . $stmt_insert->error;
    }







    $stmt_insert->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarzSpa - Car and Owner Details</title>
  <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/styles.css">
  <link rel="shortcut icon" href="../../images/favicon.png">
  <style>
    .container-fluid.page-body-wrapper {
      width: 100% !important;
      max-width: 100%;
      padding-left: 0;
      padding-right: 0;
      margin-left: 0;
      margin-right: 0;
    }

    .form-control {
      border: 2px solid #ccc;
      border-radius: 4px;
      padding: 10px;
    }

    .form-group.row {
      margin-bottom: 15px;
    }

    .card {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 20px;
    }

    .btn-lg {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="container-scroller d-flex">
    <?php include '../../partials/navbar1.html'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php
      $basePath = '/Carzspa/';
      include '../../partials/_navbar.php';
      ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h2 class="card-title inverse">Service Booking</h2>
                <form class="form-sample" method="POST" action="">
                  <br>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Customer" class="col-sm-3 col-form-label">Customer</label>
                        <div class="col-sm-9">
                          <select class="form-control form-control-lg" aria-label="Customer Information" name="Customer" id="Customer" required>
                            <option value="">Select Customer</option>
                            <?php
                            $customers = $conn->query("SELECT customer_Id, fullName FROM enquiryForm");
                            while ($row = $customers->fetch_assoc()) {
                              echo "<option value='{$row['customer_Id']}'>{$row['fullName']}</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Car_Model" class="col-sm-3 col-form-label">Car Model</label>
                        <div class="col-sm-9">
                          <input type="text" id="Car_Model" name="Car_Model" class="form-control" placeholder="Enter car model" required>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Service" class="col-sm-3 col-form-label">Service</label>
                        <div class="col-sm-9">
                          <input type="text" id="Service" name="Service" class="form-control" placeholder="Enter service type" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Amount" class="col-sm-3 col-form-label">Amount</label>
                        <div class="col-sm-9">
                          <input type="text" id="Amount" name="Amount" class="form-control" placeholder="Enter amount" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="sub">
                    <button type="submit" class="btn-lg btn-primary btn-icon-text">
                      <i class="mdi mdi-file-check btn-icon-prepend"></i>
                      Submit
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>


          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-danger">Data Filling for Car and Car Owner</h4>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Sr. No</th>
                        <th>Car Model</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Full Name</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          echo "<tr>
                                  <td>" . $row['id'] . "</td>
                                  <td>" . $row['carModel'] . "</td>
                                  <td>" . $row['ServiceType'] . "</td>
                                  <td>" . $row['serviceAmount'] . " Rs</td>
                                  <td>" . $row['fullName'] . "</td>
                                  <td class='action-buttons'>
                                    <button type='button' class='btn btn-inverse-warning btn-rounded btn-icon' title='Approve'><i class='mdi mdi-file-check'></i></button>                        
                                     <a href='?delete_id=" . $row['id'] . "' class='btn btn-inverse-danger btn-rounded btn-icon' title='Delete'>
                                      <br><i class='mdi mdi-delete'></i>
                                    </a>
                                  </td>
                                </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='9'>No records found</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include '../../partials/_footer.html'; ?>
    </div>
  </div>
  

  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <script src="/js/jquery.cookie.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/dashboard.js"></script>
</body>

</html> 