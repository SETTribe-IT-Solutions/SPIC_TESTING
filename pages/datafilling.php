<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('conn.php');

function generateCustomerId() {
    // Generate a unique customer ID (e.g., "CUST123456789")
    return "CUST" . time();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST["fullName"];
    $contactNumber = $_POST["contactNumber"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $status = "Active";
    $preferdCom = $_POST["preferdCom"];
    $datetime = date("Y-m-d H:i:s");

    // Car Details are now arrays
    $companies = $_POST["company"];
    $carModels = $_POST["carModel"];
    $vehicleNumbers = $_POST["vehicleNumber"];
    $previousHistories = $_POST["previousHistory"];

    if (isset($_GET["edit"])) {
        $id = $_GET["edit"];

        // Loop through car details and create a single comma-separated string for each
        $companyString = implode(",", $companies);
        $carModelString = implode(",", $carModels);
        $vehicleNumberString = implode(",", $vehicleNumbers);
        $previousHistoryString = implode(",", $previousHistories);

        $sql = "UPDATE enquiryForm SET company='$companyString', carModel='$carModelString', vehicleNumber='$vehicleNumberString', previousHistory='$previousHistoryString',
                fullName='$fullName', contactNumber='$contactNumber', email='$email', address='$address', preferdCom='$preferdCom', datetime='$datetime' WHERE id=$id";

        $message = "Record updated successfully!";

    } else {
        // Generate a new customer_Id
        $customerId = generateCustomerId();

        // Loop through car details and create a single comma-separated string for each
        $companyString = implode(",", $companies);
        $carModelString = implode(",", $carModels);
        $vehicleNumberString = implode(",", $vehicleNumbers);
        $previousHistoryString = implode(",", $previousHistories);

        $sql = "INSERT INTO enquiryForm (customer_Id, company, carModel, vehicleNumber, previousHistory, fullName, contactNumber, email, address, status, preferdCom, datetime)
                VALUES ('$customerId', '$companyString', '$carModelString', '$vehicleNumberString', '$previousHistoryString', '$fullName', '$contactNumber', '$email', '$address', '$status','$preferdCom', '$datetime')";
        $message = "Record inserted successfully!";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '$message'
                }).then(function () {
                    window.location.href = '" . $_SERVER["PHP_SELF"] . "';
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error: " . $conn->error . "'
                });
            });
        </script>";
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
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Record status updated to $newStatus successfully'
                }).then(function () {
                    window.location.href = '" . $_SERVER["PHP_SELF"] . "';
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error updating record: " . $conn->error . "'
                });
            });
        </script>";
    }
}

// Fetch data for editing
if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    $sql = "SELECT * FROM enquiryForm WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $company = $row["company"];
        $carModel = $row["carModel"];
        $vehicleNumber = $row["vehicleNumber"];
        $previousHistory = $row["previousHistory"];
        $fullName = $row["fullName"];
        $contactNumber = $row["contactNumber"];
        $email = $row["email"];
        $address = $row["address"];
        $preferdCom = $row["preferdCom"];

        // Explode the comma-separated strings back into arrays for editing.
        $companies = explode(",", $company);
        $carModels = explode(",", $carModel);
        $vehicleNumbers = explode(",", $vehicleNumber);
        $previousHistories = explode(",", $previousHistory);

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Data Filling </title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/CarzspaLogo.jpg" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .container-fluid.page-body-wrapper {
        width: 100% !important; /* Ensures full width */
        max-width: 100%;        /* Prevents any max width restrictions */
        padding-left: 0;        /* Optional: to remove any left padding */
        padding-right: 0;       /* Optional: to remove any right padding */
        margin-left: 0;         /* Optional: to ensure no left margin */
        margin-right: 0;        /* Optional: to ensure no right margin */
        }
        .navbar-fixed-top .main-panel {
        padding-top: 0px !important;
        }
        .form-control {
            border: 2px solid #ced4da;
            padding: 10px;
            border-radius: 5px;
        }
        .required {
            color: red;
            font-weight: bold; /* Optional for emphasis */
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
        }
        .card-title {
            font-weight: bold;
        }
        .dt-head-center {
            text-align: center !important; /* Center-align all table headers */
        }

    /* Make the table container responsive */
    .table-responsive {
        width: 100%;
        max-width: 900px; /* Adjust this value as needed */
        margin: auto; /* Center the table */
        overflow-x: auto;
    }

    /* Ensure the table has a smaller width */
    #carOwnerTable {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        table-layout: auto;
    }

    /* Styling for the table headers */
    #carOwnerTable thead {
        background-color: #223e9c;
        color: white;
        font-size: 14px; /* Decreased from 16px */
        font-weight: bold;
    }

    #carOwnerTable th, #carOwnerTable td {
        padding: 8px 10px; /* Reduced padding */
        text-align: center;
        border: 1px solid #ddd;
        font-size: 13px; /* Decreased from 14px */
    }

    /* Ensure table remains scrollable on small screens */
    @media screen and (max-width: 768px) {
        .table-responsive {
            max-width: 100%;
        }
        #carOwnerTable thead {
            font-size: 12px;
        }
        #carOwnerTable tbody tr {
            font-size: 11px;
        }
        #carOwnerTable th, #appointmentTable td {
            padding: 5px 8px; /* Further reduce padding */
        }
    }
    .select2-container {
    width: 100% !important; /* Force Select2 width to be 100% */
    }

/* Style the Select2 dropdowns */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        height: calc(1.5em + 0.85rem + 5px) !important; /* Match text input height */
        padding: 0.375rem 0.75rem; /* Consistent padding */
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    /* Style for the "Add Car" button */
    .add-car-button {
        margin-top: 10px; /* Add some spacing */
        margin-bottom: 10px;
        text-align: center;
    }
    .remove-car-button {
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: center;
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
                    <h2 class="card-title text-center">Data Filling</h2> 
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                
                                <form class="form-sample" method="POST" <?php if(isset($_GET['edit'])){ echo "enctype='multipart/form-data'"; } ?>>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="fullName" class="col-sm-3 col-form-label">
                                                    Customer Name <span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="fullName" name="fullName" class="form-control"
                                                        placeholder="Enter Full Name" value="<?php echo htmlspecialchars($fullName); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="mobilenumber" class="col-sm-3 col-form-label">
                                                Mobile Number<span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="contactNumber" name="contactNumber" class="form-control"
                                                        placeholder="Enter Mobile Number" value="<?php echo htmlspecialchars($contactNumber); ?>" 
                                                        maxlength="10" minlength="10" pattern="\d{10}" 
                                                        title="Mobile number must be exactly 10 digits and only numbers are allowed" required
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>
                                            </div>
                                        </div>                                                                                                                      
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Email" class="col-sm-3 col-form-label">
                                                   Email<span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="email" id="email" name="email" class="form-control"
                                                        placeholder="Enter Email Address" value="<?php echo htmlspecialchars($email); ?>" 
                                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                                        title="Please enter a valid email address (e.g., example@domain.com)" required
                                                        oninput="validateEmail(this)">
                                                    <small id="emailError" style="color: red; display: none;">Invalid email address format.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-3 col-form-label">
                                                Address <span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="address" name="address" class="form-control"
                                                        placeholder="Enter Address" value="<?php echo htmlspecialchars($address); ?>" required>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="fpreferdCom" class="col-sm-3 col-form-label">
                                                Preferred Communication Method <span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <select id="preferdCom" name="preferdCom" class="form-control select2" required>
                                                        <option value="" disabled <?php echo empty($preferdCom) ? 'selected' : ''; ?>>Select an option</option>
                                                        <option value="Mail" <?php echo ($preferdCom === "Mail") ? 'selected' : ''; ?>>Mail</option>
                                                        <option value="WhatsApp" <?php echo ($preferdCom === "WhatsApp") ? 'selected' : ''; ?>>WhatsApp</option>
                                                        <option value="Call" <?php echo ($preferdCom === "Call") ? 'selected' : ''; ?>>Call</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                               <!-- Container for Car Details -->
                                <div id="carDetailsContainer">
                                <?php
                                    if (isset($_GET["edit"])) {
                                        $id = $_GET["edit"];
                                        $sql = "SELECT * FROM enquiryForm WHERE id=$id";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows == 1) {
                                            $row = $result->fetch_assoc();
                                            $companies = explode(",", $row["company"]);
                                            $carModels = explode(",", $row["carModel"]);
                                            $vehicleNumbers = explode(",", $row["vehicleNumber"]);
                                            $previousHistories = explode(",", $row["previousHistory"]);

                                            $numCars = count($companies);

                                            for ($i = 0; $i < $numCars; $i++) {
                                ?>
                                    <div class="carDetails" style="border: 1px solid #ced4da; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                                        <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">Car Details</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="company" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Select Company <span class="required">*</span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <select name="company[]" class="companySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="">Select Company</option>
                                                            <?php
                                                            $queryCompanies = "SELECT DISTINCT company FROM addCars WHERE status='active' ORDER BY company";
                                                            $resultCompanies = $conn->query($queryCompanies);

                                                            while ($rowCompany = $resultCompanies->fetch_assoc()) {
                                                                $selected = ($companies[$i] == $rowCompany['company']) ? "selected" : "";
                                                                echo "<option value='" . htmlspecialchars($rowCompany['company']) . "' $selected>" . htmlspecialchars($rowCompany['company']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="carModel" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Select Car Model <span class="required">*</span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <select name="carModel[]" class="carModelSelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="">Select Car Model</option>
                                                            <?php
                                                            $selectedCompany = $companies[$i];
                                                            $queryCarModels = "SELECT carModel FROM addCars WHERE company = '$selectedCompany' AND status = 'active' ORDER BY carModel";
                                                            $resultCarModels = $conn->query($queryCarModels);
                                                            while ($rowCarModel = $resultCarModels->fetch_assoc()) {
                                                                $selected = ($carModels[$i] == $rowCarModel['carModel']) ? "selected" : "";
                                                                echo "<option value='" . htmlspecialchars($rowCarModel['carModel']) . "' $selected>" . htmlspecialchars($rowCarModel['carModel']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="Vehicle Number" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Vehicle Number <span class="required">*</span>
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <input type="text" name="vehicleNumber[]" class="vehicleNumberInput form-control"
                                                            placeholder="Enter Vehicle Number" value="<?php echo isset($vehicleNumbers[$i]) ? htmlspecialchars($vehicleNumbers[$i]) : ''; ?>"
                                                            pattern="^[a-zA-Z0-9\s]+$"
                                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');"
                                                            title="Only letters, numbers, and spaces are allowed."
                                                            required style="padding: 0.375rem 0.75rem; height: auto;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="previousHistory" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Previous Service History <span class="required">*</span>
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <select name="previousHistory[]" class="previousHistorySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="" disabled>Select an option</option>
                                                            <option value="Yes" <?php echo (isset($previousHistories[$i]) && $previousHistories[$i] === "Yes") ? 'selected' : ''; ?>>Yes</option>
                                                            <option value="No" <?php echo (isset($previousHistories[$i]) && $previousHistories[$i] === "No") ? 'selected' : ''; ?>>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="remove-car-button" style="text-align: center; margin-top: 10px;">
                                            <button type="button" class="btn btn-danger removeCarSection">-</button>
                                        </div>
                                    </div>
                                <?php
                                            } // end for loop
                                        } // end if result num rows
                                    } else {
                                        // Show at least one car details section when adding
                                ?>
                                    <div class="carDetails" style="border: 1px solid #ced4da; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                                        <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">Car Details</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="company" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Select Company <span class="required">*</span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <select name="company[]" class="companySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="">Select Company</option>
                                                            <?php
                                                            $queryCompanies = "SELECT DISTINCT company FROM addCars WHERE status='active' ORDER BY company";
                                                            $resultCompanies = $conn->query($queryCompanies);

                                                            while ($rowCompany = $resultCompanies->fetch_assoc()) {
                                                                echo "<option value='" . htmlspecialchars($rowCompany['company']) . "'>" . htmlspecialchars($rowCompany['company']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="carModel" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Select Car Model <span class="required">*</span>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <select name="carModel[]" class="carModelSelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="">Select Car Model</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="Vehicle Number" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Vehicle Number <span class="required">*</span>
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <input type="text" name="vehicleNumber[]" class="vehicleNumberInput form-control"
                                                            placeholder="Enter Vehicle Number"
                                                            pattern="^[a-zA-Z0-9\s]+$"
                                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');"
                                                            title="Only letters, numbers, and spaces are allowed."
                                                            required style="padding: 0.375rem 0.75rem; height: auto;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row" style="margin-bottom: 0.5rem;">
                                                    <label for="previousHistory" class="col-sm-3 col-form-label" style="padding-right: 0;">
                                                        Previous Service History <span class="required">*</span>
                                                    </label>

                                                    <div class="col-sm-9">
                                                        <select name="previousHistory[]" class="previousHistorySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                                            <option value="" disabled selected>Select an option</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="remove-car-button" style="text-align: center; margin-top: 10px;">
                                            <button type="button" class="btn btn-danger removeCarSection">-</button>
                                        </div>
                                    </div>
                                <?php
                                    }
                                ?>
                                </div>

                                <!-- Add Car Button -->
                                <div class="add-car-button" style="text-align: left; position: relative; top: -35%;">
                                    <button type="button" id="addCarButton" class="btn btn-primary">+</button>
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
                                    <div class="table-wrapper">
                                    <table class="table table-striped" id="carOwnerTable">
                                        <thead>
                                            <tr>
                                                <th>Sr No</th>
                                                <th>Customer Name</th>
                                                <th>Mobile Number</th>
                                                <th>Email</th>
                                                <th>Company</th>
                                                <th>Car Model</th>
                                                <th>Vehicle Number</th>
                                                <th>Previous History</th>
                                                <th>Address</th>
                                                <th>Preferred Communication</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                include('conn.php'); // Ensure database connection
                                                $sql = 'SELECT * FROM enquiryForm WHERE status = "active" ORDER BY datetime DESC';
                                                $result = $conn->query($sql);
                                                $serialNo = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $serialNo++ . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['contactNumber']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['company']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['carModel']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['vehicleNumber']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['previousHistory']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['preferdCom']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                    echo "<td class='action-buttons'>";
                                                    echo "<a href='?edit=" . $row['id'] . "' class='btn btn-inverse-warning btn-rounded btn-icon'><br><i class='mdi mdi mdi-border-color'></i></a> ";
                                                    echo "<a href='?action=deactivate&id=" . $row['id'] . "' class='btn btn-inverse-danger btn-rounded btn-icon'><br><i class='mdi mdi-delete'></i></a>";
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
    <!-- jQuery (Load First) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (After jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons CSS (For Export) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <!-- JSZip (Required for Excel Export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });

        // Function to fetch and update Car Model options based on selected Company
        function updateCarModels(companySelect, carModelSelect) {
            var company = companySelect.val();
            if (company) {
                $.ajax({
                    url: "fetch_car_models.php",
                    type: "POST",
                    data: { company: company },
                    success: function (data) {
                        carModelSelect.html(data).trigger('change'); // Update and trigger Select2 change
                    }
                });
            } else {
                carModelSelect.html('<option value="">Select Car Model</option>').trigger('change');
            }
        }

        // Event listener for Company dropdown change
        $(document).on('change', '.companySelect', function () {
            var companySelect = $(this);
            var carModelSelect = $(this).closest('.carDetails').find('.carModelSelect');
            updateCarModels(companySelect, carModelSelect);
        });

         // Event listener for "Add Another Car" button click
         $("#addCarButton").click(function () {
            var newCarSection = `
                <div class="carDetails" style="border: 1px solid #ced4da; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                    <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">Car Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row" style="margin-bottom: 0.5rem;">
                                <label class="col-sm-3 col-form-label" style="padding-right: 0;">
                                    Select Company <span class="required">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="company[]" class="companySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                        <option value="">Select Company</option>
                                        <?php
                                        $query = "SELECT DISTINCT company FROM addCars WHERE status='active' ORDER BY company";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['company']) . "'>" . htmlspecialchars($row['company']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row" style="margin-bottom: 0.5rem;">
                                <label class="col-sm-3 col-form-label" style="padding-right: 0;">
                                    Select Car Model <span class="required">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="carModel[]" class="carModelSelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                        <option value="">Select Car Model</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row" style="margin-bottom: 0.5rem;">
                                <label class="col-sm-3 col-form-label" style="padding-right: 0;">
                                    Vehicle Number <span class="required">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" name="vehicleNumber[]" class="vehicleNumberInput form-control"
                                           placeholder="Enter Vehicle Number"
                                           pattern="^[a-zA-Z0-9\s]+$"
                                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');"
                                           title="Only letters, numbers, and spaces are allowed."
                                           required style="padding: 0.375rem 0.75rem; height: auto;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row" style="margin-bottom: 0.5rem;">
                                <label class="col-sm-3 col-form-label" style="padding-right: 0;">
                                    Previous Service History <span class="required">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select name="previousHistory[]" class="previousHistorySelect form-control select2" required style="padding: 0.375rem 0.75rem; height: auto;">
                                        <option value="" disabled selected>Select an option</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="remove-car-button" style="text-align: center; margin-top: 10px;">
                        <button type="button" class="btn btn-danger removeCarSection">-</button>
                    </div>
                </div>
            `;

            $("#carDetailsContainer").append(newCarSection);

            // Initialize Select2 for the new elements
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            // Attach the change event to the newly created company select dropdown
            $('.companySelect').on('change', function () {
                var companySelect = $(this);
                var carModelSelect = $(this).closest('.carDetails').find('.carModelSelect');
                updateCarModels(companySelect, carModelSelect);

            });
             //after adding the car, show the all remove button
            $('.removeCarSection').show();
        });
        // Remove car section functionality
        $(document).on('click', '.removeCarSection', function () {
            $(this).closest('.carDetails').remove();
            //check car details and hide the remove button when there is only one car
            var carcount=$(".carDetails").length;
            if(carcount==1){
                $('.removeCarSection').hide();
            }
        });

        //hide the remove button when there is only one car
        var carcount=$(".carDetails").length;
            if(carcount==1){
                $('.removeCarSection').hide();
            }
  $(document).on('click', '.delete-button', function(e) {
                e.preventDefault(); // Prevent default link behavior
                var id = $(this).data('id'); // Get the id from the data attribute
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, deactivate it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, redirect to the deactivate URL
                        window.location.href = '?action=deactivate&id=' + id;
                    }
                });
            });
        $('#carOwnerTable').DataTable({
            dom: '<"top"lfB>rt<"bottom"ip>', // Properly places buttons, search, and pagination
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn btn-sm',
                    title: 'Car and Owner Data',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude last column (Actions)
                    }
                },
            ],
            "paging": false,        // Enable pagination
            "searching": true,     // Enable search
            "ordering": true,      // Enable sorting
            "info": true,          // Show table info
            "responsive": true,    // Make table responsive
            "lengthMenu": [10, 25, 50, 100], // Control how many rows are displayed
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