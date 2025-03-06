


<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Include the database connection file
include('conn.php');

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

    if (isset($_GET["edit"])) {
        $id = $_GET["edit"];
        $sql = "UPDATE enquiryForm SET carModel='$carModel', vehicleNumber='$vehicleNumber', previousHistory='$previousHistory',
                fullName='$fullName', contactNumber='$contactNumber', email='$email', address='$address', datetime='$datetime' WHERE id=$id";
    } else {
        $sql = "INSERT INTO enquiryForm (carModel, vehicleNumber, previousHistory, fullName, contactNumber, email, address, status, datetime)
                VALUES ('$carModel', '$vehicleNumber', '$previousHistory', '$fullName', '$contactNumber', '$email', '$address', '$status', '$datetime')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
        echo "<script>alert('Record status updated to $newStatus successfully');</script>";
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
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
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
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

        h2.card-title {
    font-weight: bold;
}


        .container-fluid.page-body-wrapper {
            width: 100% !important;
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
        }
   /* General table styling */
.table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    margin-top: 20px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for table */
    overflow: hidden;
    border-radius: 8px; /* Rounded corners */
}

/* Table headers */
.table thead th {
    background-color: #223e9c; /* Professional blue background */
    color: #fff; /* White text */
    font-size: 16px;
    font-weight: bold;
    text-align: center; /* Center-align headers */
    
   
    border: 1px solid #ddd;
}

/* Table body cells */
.table tbody td {
  
    font-size: 14px;
    text-align: center; /* Center-align data */
    color: #333; /* Dark text for better readability */
    border: 1px solid #ddd;
}

/* Zebra striping for rows */
.table tbody tr:nth-child(even) {
    background-color: #f9f9f9; /* Light gray for even rows */
}

.table tbody tr:nth-child(odd) {
    background-color: #ffffff; /* White for odd rows */
}

/* Hover effect for rows */
.table tbody tr:hover {
    background-color: #f1f1f1; /* Slightly darker gray on hover */
    cursor: pointer; /* Pointer cursor for interactivity */
}


/* Responsive styling for smaller screens */
@media (max-width: 768px) {
    .table thead {
        display: none; /* Hide table headers on smaller screens */
    }

    .table tbody tr {
        display: block; /* Stack rows */
       
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        text-align: left; /* Left-align content for better readability */
        padding: 10px 5px;
    }

    .table tbody td::before {
        content: attr(data-label); /* Add labels for mobile view */
        font-weight: bold;
        text-transform: uppercase;
        margin-right: 10px;
        color: #223e9c; /* Blue text for labels */
    }
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
                <h2 class="card-title text-center">Data Filling for Car and Car Owner</h2>
                
                <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                
                                <form class="form-sample" method="POST">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="fullName" class="col-sm-3 col-form-label">Full Name *</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="fullName" name="fullName" class="form-control"
                                                        placeholder="Enter Full Name" value="<?php echo $fullName; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">Car Model *</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="carModel" name="carModel" class="form-control"
                                                        placeholder="Enter Car Make and Model" value="<?php echo $carModel; ?>" required>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                        <div class="form-group row">
    <label for="vehicleNumber" class="col-sm-3 col-form-label">Vehicle Number *</label>
    <div class="col-sm-9">
        <input type="text" id="vehicleNumber" name="vehicleNumber" class="form-control"
            placeholder="Enter Vehicle Number" value="<?php echo htmlspecialchars($vehicleNumber); ?>" 
            pattern="^[a-zA-Z0-9\s]+$" 
            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');" 
            title="Only letters, numbers, and spaces are allowed."
            required>
    </div>
</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="previousHistory" class="col-sm-3 col-form-label">Previous Service History *</label>
                                                <div class="col-sm-9">
                                                    <select id="previousHistory" name="previousHistory" class="form-control" required>
                                                        <option value="" disabled selected>Select an option</option>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="contactNumber" class="col-sm-3 col-form-label">Mobile Number *</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="contactNumber" name="contactNumber" class="form-control"
                                                    placeholder="Enter Mobile Number" value="<?php echo $contactNumber; ?>" 
                                                    maxlength="10" minlength="10" pattern="\d{10}" 
                                                    title="Mobile number must be exactly 10 digits and only numbers are allowed" required
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                        </div>
                                    </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-3 col-form-label">Email *</label>
                                                <div class="col-sm-9">
                                                    <input type="email" id="email" name="email" class="form-control"
                                                        placeholder="Enter Email Address" value="<?php echo $email; ?>" 
                                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                                        title="Please enter a valid email address (e.g., example@domain.com)" required
                                                        oninput="validateEmail(this)">
                                                    <small id="emailError" style="color: red; display: none;">Invalid email address format.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-3 col-form-label">Address *</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="address" name="address" class="form-control"
                                                        placeholder="Enter Address" value="<?php echo $address; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="preferred" class="col-sm-3 col-form-label">Preferred Communication Method *</label>
                                                <div class="col-sm-9">
                                                    <select id="preferred" name="preferred" class="form-control" required>
                                                        <option value="" disabled selected>Select an option</option>
                                                        <option value="Mail">Mail</option>
                                                        <option value="WhatsApp">WhatsApp</option>
                                                        <option value="Call">Call</option>
                                                    </select>
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
            <th>Sr No</th>
            <th>Car Model</th>
            <th>Vehicle Number</th>
            <th>Previous History</th>
            <th>Full Name</th>
            <th>Mobile Number</th>
            <th>Email</th>
            <th>Address</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM enquiryForm";
        $result = $conn->query($sql);
        
        $serialNo = 1; // Initialize serial number
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $serialNo++ . "</td>"; // Auto-incrementing serial number
            echo "<td>" . htmlspecialchars($row['carModel']) . "</td>";
            echo "<td>" . htmlspecialchars($row['vehicleNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['previousHistory']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contactNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td class='action-buttons'>";
            echo "<a href='?edit=" . $row['id'] . "' class='btn btn-inverse-warning btn-rounded btn-icon' title='Edit Record'><br><i class='mdi mdi-border-color'></i></a> ";
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
            <?php include '../partials/_footer.html'; ?> 
            

        </div>           
    </div>  
    
    <script src="../vendors/js/vendor.bundle.base.js"></script>
    <script src="../vendors/chart.js/Chart.min.js"></script>
    <script src="../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../js/jquery.cookie.js"></script>
    <script src="../js/hoverable-collapse.js"></script>
    <script src="../js/template.js"></script>
    <script src="../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../js/dashboard.js"></script>
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
                    columns: function (idx, data, node) {
                        // Exclude the "Actions" column (last column, index 9)
                        return idx !== 9; // Update '9' to match the index of the "Actions" column
                    }
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
    <script>
    function validateEmail(input) {
        const errorElement = document.getElementById('emailError');
        if (input.validity.patternMismatch || input.validity.typeMismatch) {
            errorElement.style.display = 'block';
        } else {
            errorElement.style.display = 'none';
        }
    }
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


