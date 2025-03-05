<?php
include('conn.php');

// Function to generate a unique serviceId
function generateUniqueServiceId($conn) {
    $random_number = rand(100000, 999999);
    $timestamp = time();
    $serviceId = $random_number . $timestamp;

    $sql_check = "SELECT COUNT(*) FROM serviceForm WHERE serviceId = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $serviceId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return generateUniqueServiceId($conn);
    }

    return $serviceId;
}

$sql = "SELECT sf.id, sf.carModel, sf.serviceAmount, ef.fullName, a.ServiceType 
        FROM serviceForm sf 
        JOIN enquiryForm ef ON sf.customerId = ef.customer_Id
        JOIN appointment a ON sf.customerId = a.customerId";
$result = $conn->query($sql);

$sql_customers ="SELECT customer_Id, fullName FROM enquiryForm WHERE status = 'active'";
$result_customers = $conn->query($sql_customers);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerId = $_POST['Customer'];
    if (empty($customerId)) {
        echo "Customer ID is missing.";
        exit();
    }

    $carModel = $_POST['Car_Model'];
    $service = $_POST['Service'];
    $amount = $_POST['Amount'];
    $datetime = date("Y-m-d H:i:s");

    $sql_customer = "SELECT fullName FROM enquiryForm WHERE customer_id = ?";
    $stmt_customer = $conn->prepare($sql_customer);
    $stmt_customer->bind_param("i", $customerId);
    $stmt_customer->execute();
    $stmt_customer->bind_result($full_name);
    $stmt_customer->fetch();
    $stmt_customer->close();

    $serviceId = generateUniqueServiceId($conn);

    $sql_insert = "INSERT INTO serviceForm (serviceId, customerId, serviceAmount, status, datetime, carModel, full_name) 
                    VALUES (?, ?, ?, NULL, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssss", $serviceId, $customerId, $amount, $datetime, $carModel, $full_name);

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
  <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
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

    h2.card-title.inverse {
      text-align: center;
    }

    h2.card-title {
    font-weight: bold;
}
  </style>
</head>

<body>
  <div class="container-scroller d-flex">
    <?php include '../partials/navbar1.html'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php
      $basePath = '/Carzspa/';
      include '../partials/_navbar.php';
      ?>

      <div class="main-panel">
        <div class="content-wrapper">
        <h2 class="card-title text-center">Service Booking</h2>
        <br>
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                
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
                                    <table class="table table-striped" id="serviceTable">
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
                                            <?php if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr onclick='selectRow(this)'>
                                                        <td>{$row['id']}</td>
                                                        <td>{$row['carModel']}</td>
                                                        <td>{$row['ServiceType']}</td>
                                                        <td>{$row['serviceAmount']} Rs</td>
                                                        <td>{$row['fullName']}</td>
                                                        <td class='action-buttons'>
                                                            <button type='button' onclick=\"window.location.href='mailto:varadparkhe@gmail.com?subject=Quotation Details&body=Please find the attached quotation details'\" class='btn btn-inverse-danger btn-rounded btn-icon'><i class='mdi mdi-email-open'></i></button>
                                                            <button type='button' onclick='window.print()' class='btn btn-inverse-info btn-rounded btn-icon'><i class='mdi mdi-printer'></i></button>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No records found</td></tr>";
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include '../partials/_footer.html'; ?>
            </div>

            
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#serviceTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        title: 'Service Booking Details'
                    }
                ]
            });
        });
    </script>
</body>

</html>
