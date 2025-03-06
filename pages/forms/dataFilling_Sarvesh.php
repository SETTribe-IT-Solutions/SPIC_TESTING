


<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Include the database connection file
include('conn.php');
include('../includes/sweetAlerts.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carModel = $_POST["carModel"];
    $vehicleNumber = $_POST["vehicleNumber"];
    $previousHistory = $_POST["previousHistory"];
    $fullName = $_POST["fullName"];
    $contactNumber = $_POST["contactNumber"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $status = "active"; // Example status
    $datetime = date("Y-m-d H:i:s");
    $custId = //should be unique and autogenrated

    if (isset($_GET["edit"])) {
        $id = $_GET["edit"];
        $sql = "UPDATE enquiryForm SET carModel='$carModel', vehicleNumber='$vehicleNumber', previousHistory='$previousHistory',
                fullName='$fullName', contactNumber='$contactNumber', email='$email', address='$address', datetime='$datetime' WHERE id=$id";
    } else {
        $sql = "INSERT INTO enquiryForm (carModel, vehicleNumber, previousHistory, fullName, contactNumber, email, address, status, datetime)
                VALUES ('$carModel', '$vehicleNumber', '$previousHistory', '$fullName', '$contactNumber', '$email', '$address', '$status', '$datetime')";
    }

    if ($conn->query($sql) === TRUE) {
        echo '<script>sweetalert("No user found with that username","datafilling.php");</script>';
        exit;
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
        echo '<script>erroralert("No user found with that username","datafilling.php");</script>';
    }
}

// Check if action and id are set
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    // Determine the new status based on the action
    $newStatus = ($action === 'activate') ? 'Active' : 'Deactive';

    // Update the status in the database
    $updateSql = "UPDATE enquiryForm SET status = '$newStatus' WHERE id = $id";
    
    if ($conn->query($updateSql) === TRUE) {
        echo '<script>sweetalert("Record status updated successfully !","datafilling.php");</script>';
    } else {
        echo '<script>erroralert("Error Updating the Data !","datafilling.php");</script>';
    }
}
// Fetch data for editing
if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    $sql = "SELECT * FROM enquiryForm WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $carModel = $row["carModel"];
        $vehicleNumber = $row["vehicleNumber"];
        $previousHistory = $row["previousHistory"];
        $fullName = $row["fullName"];
        $contactNumber = $row["contactNumber"];
        $email = $row["email"];
        $address = $row["address"];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Data Filling for Car and Car Owner</title>
    <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="shortcut icon" href="../../images/favicon.png">
    <style>
        .form-control {
            border: 2px solid #ced4da;
            padding: 10px;
            border-radius: 5px;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
        }

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
            <?php
            $basePath = '/Carzspa/';
            include '../../partials/_navbar.php';
            ?>
            <div class="main-panel">
                <div class="content-wrapper">
                <h2 class="card-title text-center">Data Filling for Car and Car Owner</h2>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                
                                <form class="form-sample" method="POST">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">Car Make Model</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="carModel" name="carModel" class="form-control"
                                                        placeholder="Enter Car Make and Model" value="<?php echo $carModel; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="vehicleNumber" class="col-sm-3 col-form-label">Vehicle Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="vehicleNumber" name="vehicleNumber" class="form-control"
                                                        placeholder="Enter Vehicle Number" value="<?php echo $vehicleNumber; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="previousHistory" class="col-sm-3 col-form-label">Previous Service History</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="previousHistory" name="previousHistory" class="form-control"
                                                        placeholder="Enter Previous Service History" value="<?php echo $previousHistory; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="fullName" class="col-sm-3 col-form-label">Full Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="fullName" name="fullName" class="form-control"
                                                        placeholder="Enter Full Name" value="<?php echo $fullName; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="contactNumber" class="col-sm-3 col-form-label">Mobile Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="contactNumber" name="contactNumber" class="form-control"
                                                        placeholder="Enter Mobile Number" value="<?php echo $contactNumber; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="email" name="email" class="form-control"
                                                        placeholder="Enter Email Address" value="<?php echo $email; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-3 col-form-label">Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="address" name="address" class="form-control"
                                                        placeholder="Enter Address" value="<?php echo $address; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="preferred" class="col-sm-3 col-form-label">Preferred Communication Method</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="preferred" name="preferred" class="form-control"
                                                        placeholder="Enter Preferred Communication Method">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub">
                                        <button type="submit" class="btn-lg btn-primary btn-icon-text">
                                            <i class="mdi mdi-file-check btn-icon-prepend"></i> Submit
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
                                        <br><br>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Car Model</th>
                                                    <th>Vehicle Number</th>
                                                    <th>Previous History</th>
                                                    <th>Full Name</th>
                                                    <th>Mobile Number</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <!-- <th>Status</th> -->
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM enquiryForm";
                                                $result = $conn->query($sql);
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['id'] . "</td>";
                                                    echo "<td>" . $row['carModel'] . "</td>";
                                                    echo "<td>" . $row['vehicleNumber'] . "</td>";
                                                    echo "<td>" . $row['previousHistory'] . "</td>";
                                                    echo "<td>" . $row['fullName'] . "</td>";
                                                    echo "<td>" . $row['contactNumber'] . "</td>";
                                                    echo "<td>" . $row['email'] . "</td>";
                                                    echo "<td>" . $row['address'] . "</td>";
                                                    // echo "<td>" . $row['status'] . "</td>";
                                                    echo "<td class='action-buttons'>";
                                                    echo "<a href='?edit=" . $row['id'] . "'class='btn btn-inverse-warning btn-rounded btn-icon'><br><i class='mdi mdi-file-check'></i></a> ";

                                                    echo "<a href='?action=deactivate&id=" . $row['id'] . "' class='btn btn-inverse-danger btn-rounded btn-icon' title='Deactivate Record' onclick=\"return confirm('Are you sure?');\"><br><i class='mdi mdi-delete'></i></a>";
                                                    echo "</td>";
                                                    echo "</tr>";
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
            <?php echo file_get_contents('_footer.html'); ?>

        </div>           
    </div>  
    
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <script src="../../vendors/chart.js/Chart.min.js"></script>
    <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../../js/jquery.cookie.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../../js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>






    <script>
        
        $(document).ready(function () {
    $('.table').DataTable({
        dom: 'Bfrtip', // Add buttons at the top
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export to Excel', // Button label
                className: 'btn btn-success', // Green button styling
                title: 'Car and Car Owner Data', // Title of the Excel file
                exportOptions: {
                    columns: ':not(.action-buttons)' // Exclude the "Actions" column from export
                }
            }
        ],
        paging: true, // Enable pagination
        searching: true, // Enable search
        order: [[0, 'asc']] // Default sorting by the first column (ID)
    });
});





document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    // Function to show error
    function showError(input, message) {
        const parent = input.parentElement;
        let error = parent.querySelector(".error-message");
        if (!error) {
            error = document.createElement("span");
            error.className = "error-message";
            error.style.color = "red";
            parent.appendChild(error);
        }
        error.textContent = message;
    }

    // Function to remove error
    function removeError(input) {
        const parent = input.parentElement;
        const error = parent.querySelector(".error-message");
        if (error) {
            parent.removeChild(error);
        }
    }

    // Validation functions
    function validateMobileNumber(mobileNumber) {
        const regex = /^[6-9]\d{9}$/; // Must start with 6-9 and be 10 digits
        return regex.test(mobileNumber);
    }

    function validateEmail(email) {
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return regex.test(email);
    }

    function validateVehicleNumber(vehicleNumber) {
        const regex = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/; // Example: MH12AB1234
        return regex.test(vehicleNumber);
    }

    // Check required fields
    function validateRequired(input) {
        return input.value.trim() !== "";
    }

    form.addEventListener("submit", function (event) {
        let isValid = true;

        // Select all input fields
        const inputs = document.querySelectorAll(".form-control");

        inputs.forEach((input) => {
            if (!validateRequired(input)) {
                isValid = false;
                showError(input, `${input.name} is required.`);
            } else {
                removeError(input);
            }
        });

        const mobileInput = document.querySelector("#contactNumber");
        const emailInput = document.querySelector("#email");
        const vehicleInput = document.querySelector("#vehicleNumber");

        // Mobile number validation
        if (mobileInput && !validateMobileNumber(mobileInput.value)) {
            isValid = false;
            showError(mobileInput, "Enter a valid 10-digit mobile number starting with 6-9.");
        } else if (mobileInput) {
            removeError(mobileInput);
        }

        // Email validation
        if (emailInput && !validateEmail(emailInput.value)) {
            isValid = false;
            showError(emailInput, "Enter a valid email address.");
        } else if (emailInput) {
            removeError(emailInput);
        }

        // Vehicle number validation
        if (vehicleInput && !validateVehicleNumber(vehicleInput.value)) {
            isValid = false;
            showError(vehicleInput, "Enter a valid vehicle number (e.g., MH12AB1234).");
        } else if (vehicleInput) {
            removeError(vehicleInput);
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
});
    </script>
</body>
<style>
    @font-face {
        font-family: FigtreeVF;
        src: url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2 supports variations"), url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2-variations");
        font-weight: 100 1000;
        font-display: swap
    }
</style>

</html>


