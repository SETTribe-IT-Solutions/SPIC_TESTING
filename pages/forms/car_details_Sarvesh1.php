<?php
include('conn.php');
// Insert data into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $company = $_POST['company'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $vin = $_POST['vin'];
    $license = $_POST['license'];
    $history = $_POST['history'];
    $owner = $_POST['owner'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $preferred = $_POST['preferred'];
    $customerId = rand(1000, 9999); // Random customer ID for example
    $status = 'Active';
    $dateTime = date('Y-m-d H:i:s');

    $sql = "INSERT INTO detailsOfCarsAndOwner (companyName, carModel, manufactureYear, licensePlateNumber, ownerName, mobileNumber, address, email, customerId, status, dateTime)
            VALUES ('$company', '$model', '$year', '$license', '$owner', '$mobile', '$address', '$email', '$customerId', '$status', '$dateTime')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch records from the database to display
$sql = "SELECT * FROM detailsOfCarsAndOwner";
$result = $conn->query($sql);

// Handle delete and edit actions
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteSql = "DELETE FROM detailsOfCarsAndOwner WHERE id = $id";
    if ($conn->query($deleteSql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $editSql = "SELECT * FROM detailsOfCarsAndOwner WHERE id = $id";
    $editResult = $conn->query($editSql);
    $editRow = $editResult->fetch_assoc();
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
  </style>
</head>

<body>
  <div class="container-scroller d-flex">
    <?php include '../../partials/navbar1.html'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include '../../partials/_navbar.php'; ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h2 class="card-title inverse">Details of Car and Owner</h2>
                <form class="form-sample" method="POST">
                  <p class="card-description">Details of Car</p>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="company" class="col-sm-3 col-form-label">Select Company</label>
                        <div class="col-sm-9">
                          <input type="text" id="company" name="company" class="form-control" value="<?php echo isset($editRow) ? $editRow['companyName'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="model" class="col-sm-3 col-form-label">Select Car Model</label>
                        <div class="col-sm-9">
                          <input type="text" id="model" name="model" class="form-control" value="<?php echo isset($editRow) ? $editRow['carModel'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="year" class="col-sm-3 col-form-label">Manufacture Year</label>
                        <div class="col-sm-9">
                          <input type="text" id="year" name="year" class="form-control" value="<?php echo isset($editRow) ? $editRow['manufactureYear'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="license" class="col-sm-3 col-form-label">License Plate Number</label>
                        <div class="col-sm-9">
                          <input type="text" id="license" name="license" class="form-control" value="<?php echo isset($editRow) ? $editRow['licensePlateNumber'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <p class="card-description">Details of Owner</p>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="owner" class="col-sm-3 col-form-label">Owner Name</label>
                        <div class="col-sm-9">
                          <input type="text" id="owner" name="owner" class="form-control" value="<?php echo isset($editRow) ? $editRow['ownerName'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="mobile" class="col-sm-3 col-form-label">Mobile Number</label>
                        <div class="col-sm-9">
                          <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo isset($editRow) ? $editRow['mobileNumber'] : ''; ?>" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="sub">
                    <button type="submit" name="submit" class="btn-lg btn-primary btn-icon-text">
                      <i class="mdi mdi-file-check btn-icon-prepend"></i> Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-danger">Details Of Cars and Owner</h4>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Sr. No</th>
                        <th>Car Model</th>
                        <th>Vehicle Number</th>
                        <th>License Plate Number</th>
                        <th>Previous History</th>
                        <th>Full Name</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Preferred</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                          <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['carModel']; ?></td>
                            <td><?php echo $row['licensePlateNumber']; ?></td>
                            <td><?php echo $row['licensePlateNumber']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['ownerName']; ?></td>
                            <td><?php echo $row['mobileNumber']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['preferred']; ?></td>
                            <td class="action-buttons">
                              <a href="?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-inverse-warning btn-rounded btn-icon"><i class="mdi mdi-file-check"></i></a>
                              <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-inverse-danger btn-rounded btn-icon"><i class="mdi mdi-delete"></i></a>
                            </td>
                          </tr>
                        <?php }
                      } ?>
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
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <script src="js/dashboard.js"></script>
</body>

</html>

<?php $conn->close(); ?>