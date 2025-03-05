<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include ('conn.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Details of Car and Owner</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/CarzspaLogo.jpg" />

    <!-- Include jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validateMobileNumber(input) {
            const errorElement = document.getElementById('mobileNumberError');
            const regex = /^[6-9][0-9]{0,9}$/; // Allows only numbers starting with 6-9 and up to 10 digits
            if (!regex.test(input.value) || input.value.length !== 10) {
                errorElement.classList.remove('d-none');
            } else {
                errorElement.classList.add('d-none');
            }
            // Prevent more than 10 characters
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        function validateLicensePlate(input) {
            const errorElement = document.getElementById('licensePlateError');
            const regex = /^[A-Za-z0-9]{1,10}$/; // Allows only alphanumeric characters (1-10 characters)
            if (!regex.test(input.value)) {
                errorElement.classList.remove('d-none');
            } else {
                errorElement.classList.add('d-none');
            }
        }

        function validateEmail(input) {
            const errorElement = document.getElementById('emailError');
            const regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;
            if (input.value !== "" && !regex.test(input.value)) {
                errorElement.classList.remove('d-none');
            } else {
                errorElement.classList.add('d-none');
            }
        }
    </script>

</head>
<style>
    .container-fluid.page-body-wrapper {
        width: 100% !important;
        /* Ensures full width */
        max-width: 100%;
        /* Prevents any max width restrictions */
        padding-left: 0;
        /* Optional: to remove any left padding */
        padding-right: 0;
        /* Optional: to remove any right padding */
        margin-left: 0;
        /* Optional: to ensure no left margin */
        margin-right: 0;
        /* Optional: to ensure no right margin */
    }

    .form-control {
        border: 2px solid hsl(210, 8.60%, 72.50%);
        padding: 10px;
        border-radius: 5px;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
    }

    .required {
        color: red;
        font-weight: bold;
        /* Optional for emphasis */
    }


    /* Make the table container responsive */
    .table-responsive {
        width: 100%;
        max-width: 900px;
        /* Adjust this value as needed */
        margin: auto;
        /* Center the table */
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
        font-size: 14px;
        /* Decreased from 16px */
        font-weight: bold;
    }

    #carOwnerTable th,
    #appointmentTable td {
        padding: 8px 10px;
        /* Reduced padding */
        text-align: center;
        border: 1px solid #ddd;
        font-size: 13px;
        /* Decreased from 14px */
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

        #carOwnerTable th,
        #appointmentTable td {
            padding: 5px 8px;
            /* Further reduce padding */
                
        }
    }

    .select2-container {
        width: 100% !important;
        /* Force Select2 width to be 100% */
    }

    /* Style the Select2 dropdowns */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        height: calc(1.5em + 0.85rem + 5px) !important;
        /* Match text input height */
        padding: 0.375rem 0.75rem;
        /* Consistent padding */
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
</style>

<div class="container-scroller d-flex">
    <?php include '../partials/navbar1.html'; ?>
    <div class="container-fluid page-body-wrapper">
        <?php
        $basePath = '/Carzspa/';
        include '../partials/_navbar.php';
        ?>
        <?php

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id'])) {
            if (isset($_POST['companyName'], $_POST['carModel'], $_POST['manufactureYear'], $_POST['licensePlateNumber'],
                $_POST['ownerName'], $_POST['mobileNumber'], $_POST['address'], $_POST['email'])) {

                $companyName = $_POST['companyName'];
                $carModel = $_POST['carModel'];
                $manufactureYear = $_POST['manufactureYear'];
                $licensePlateNumber = $_POST['licensePlateNumber'];
                $ownerName = $_POST['ownerName'];
                $mobileNumber = $_POST['mobileNumber'];
                $address = $_POST['address'];
                $email = $_POST['email'];
                $status = 'Active';

                if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Mobile Number',
                            text: 'Please enter a valid 10-digit number starting with 6, 7, or 9.',
                        });
                    </script>";
                    exit;
                }

                $datetime = date("Y-m-d H:i:s");
                $insertQuery = "INSERT INTO detailsOfCarsAndOwner (companyName, carModel, manufactureYear, licensePlateNumber,
                                ownerName, mobileNumber, address, email, status, datetime)
                                VALUES ('$companyName', '$carModel', '$manufactureYear', '$licensePlateNumber',
                                        '$ownerName', '$mobileNumber', '$address', '$email', '$status', '$datetime')";

                if ($conn->query($insertQuery) === TRUE) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Record Inserted',
                            text: 'The record has been inserted successfully!',
                        }).then(() => {
                            window.location.href = 'carDetails.php';
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '" . $conn->error . "',
                        });
                    </script>";
                }
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Form',
                        text: 'Please fill in all required fields.',
                    });
                </script>";
            }
        }

        if (isset($_GET['edit_id'])) {
            $id = $_GET['edit_id'];
            $query = "SELECT * FROM detailsOfCarsAndOwner WHERE id = '$id'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                $carOwnerDetails = $result->fetch_assoc();
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Record Not Found',
                        text: 'The record you are trying to edit does not exist.',
                    });
                </script>";
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit_id'])) {
            $id = $_GET['edit_id'];
            $companyName = $_POST['companyName'];
            $carModel = $_POST['carModel'];
            $manufactureYear = $_POST['manufactureYear'];
            $licensePlateNumber = $_POST['licensePlateNumber'];
            $ownerName = $_POST['ownerName'];
            $mobileNumber = $_POST['mobileNumber'];
            $address = $_POST['address'];
            $email = $_POST['email'];
            $status = 'Active';

            if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Mobile Number',
                        text: 'Please enter a valid 10-digit number starting with 6, 7, or 9.',
                    });
                </script>";
                exit;
            }

            $updateQuery = "UPDATE detailsOfCarsAndOwner
                            SET companyName = '$companyName',
                                carModel = '$carModel',
                                manufactureYear = '$manufactureYear',
                                licensePlateNumber = '$licensePlateNumber',
                                ownerName = '$ownerName',
                                mobileNumber = '$mobileNumber',
                                address = '$address',
                                email = '$email',
                                status = '$status'
                            WHERE id = '$id'";

            if ($conn->query($updateQuery) === TRUE) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Updated',
                        text: 'The record has been updated successfully!',
                    }).then(() => {
                        window.location.href = 'carDetails.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '" . $conn->error . "',
                    });
                </script>";
            }
        }

        if (isset($_GET['delete_id'])) {
            $id = (int) $_GET['delete_id'];
            $updateQuery = "UPDATE detailsOfCarsAndOwner SET status = 'inactive' WHERE id = $id";

            if ($conn->query($updateQuery) === TRUE) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Updated',
                        text: 'The record status has been updated to inactive successfully!',
                    }).then(() => {
                        window.location.href = 'carDetails.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '" . $conn->error . "',
                    });
                </script>";
            }
        }
        ?>

        <body>

            <div class="main-panel">
                <div class="content-wrapper">
                    <h2 class="card-title text-center">Details of Car and Owner</h2>
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-sample" method="POST">
                                    <p class="card-description">Details of Car</p>
                                    <div class="row">
                                        <!-- Vehicle Number Dropdown -->
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="licensePlateNumber" class="col-sm-3 col-form-label">
                                                    Vehicle Number <span class="required">*</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select id="licensePlateNumber" name="licensePlateNumber"
                                                        class="form-control select2" required>
                                                        <option value="">Select Vehicle Number</option>
                                                        <?php
                                                        $query = "SELECT vehicleNumber, company, carModel FROM enquiryForm WHERE status='active'";
                                                        $result = $conn->query($query);

                                                        $enquiryData = []; // Store the enquiry data

                                                        while ($row = $result->fetch_assoc()) {
                                                            $vehicleNumbers = explode(',', $row['vehicleNumber']);
                                                            $companies = explode(',', $row['company']);
                                                            $carModels = explode(',', $row['carModel']);

                                                            $count = count($vehicleNumbers);

                                                            for ($i = 0; $i < $count; $i++) {
                                                                $enquiryData[] = [
                                                                    'vehicleNumber' => trim($vehicleNumbers[$i]),
                                                                    'company' => trim($companies[$i]),
                                                                    'carModel' => trim($carModels[$i])
                                                                ];
                                                            }
                                                        }

                                                        // Remove duplicates from vehicle numbers
                                                        $uniqueVehicleNumbers = array_unique(array_column($enquiryData, 'vehicleNumber'));
                                                        sort($uniqueVehicleNumbers);

                                                        // Populate the dropdown with unique vehicle numbers
                                                        foreach ($uniqueVehicleNumbers as $vehicleNumber) {
                                                            $selected = (isset($carOwnerDetails['licensePlateNumber']) && $vehicleNumber == $carOwnerDetails['licensePlateNumber']) ? "selected" : "";
                                                            echo "<option value='" . htmlspecialchars($vehicleNumber) . "' $selected>" . htmlspecialchars($vehicleNumber) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Company Dropdown (Disabled & Auto-filled) -->
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="companyName" class="col-sm-3 col-form-label">
                                                    Select Company <span class="required">*</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="companyName" name="companyName"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($carOwnerDetails['companyName'] ?? ''); ?>"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Car Model Dropdown (Disabled & Auto-filled) -->
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">
                                                    Select Car Model <span class="required">*</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="carModel" name="carModel"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($carOwnerDetails['carModel'] ?? ''); ?>"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="manufactureYear" class="col-sm-3 col-form-label">
                                                    Manufacture Year<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select id="manufactureYear" name="manufactureYear"
                                                        class="form-control border" required>
                                                        <option value="">Select Year</option>
                                                        <?php
                                                        $currentYear = date("Y");
                                                        for ($year = 1999; $year <= $currentYear; $year++) {
                                                            $selected = (isset($carOwnerDetails['manufactureYear']) && $carOwnerDetails['manufactureYear'] == $year) ? "selected" : "";
                                                            echo "<option value='" . $year . "' " . $selected . ">" . $year . "</option>";
                                                        }
                                                        ?>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-description">Details of Owner</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="ownerName" class="col-sm-3 col-form-label">
                                                    Owner Name<span class="required"> *</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="ownerName" name="ownerName"
                                                        class="form-control border" placeholder="Enter Owner Name"
                                                        value="<?php echo $carOwnerDetails['ownerName'] ?? ''; ?>"
                                                        pattern="[A-Za-z\s]{3,}"
                                                        title="Owner Name must be at least 3 alphabetic characters."
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="mobileNumber" class="col-sm-3 col-form-label">
                                                    Mobile Number<span class="required">*</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="tel" id="mobileNumber" name="mobileNumber"
                                                        class="form-control border" placeholder="Enter Mobile Number"
                                                        value="<?php echo $carOwnerDetails['mobileNumber'] ?? ''; ?>"
                                                        pattern="[6-9][0-9]{9}"
                                                        title="Mobile Number must start with 6, 7, 8, or 9 and be 10 digits long."
                                                        required maxlength="10" oninput="validateMobileNumber(this)">
                                                    <small id="mobileNumberError" class="text-danger d-none">Invalid Mobile
                                                        Number. Must be exactly 10 digits starting with 6, 7, 8, or
                                                        9.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-3 col-form-label">
                                                    Address<span class="required"> *</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="text" id="address" name="address"
                                                        class="form-control border" placeholder="Enter Address"
                                                        value="<?php echo $carOwnerDetails['address'] ?? ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-3 col-form-label">
                                                    Email Address<span class="required"> *</span>
                                                </label>

                                                <div class="col-sm-9">
                                                    <input type="email" id="email" name="email"
                                                        class="form-control border" placeholder="Enter Email Address"
                                                        value="<?php echo $carOwnerDetails['email'] ?? ''; ?>"
                                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                        title="Enter a valid email address."
                                                        oninput="validateEmail(this)">
                                                    <small id="emailError" class="text-danger d-none">Invalid email
                                                        address format.</small>
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
                                <h4 class="card-title text-danger">Details Of Cars and Owner</h4>
                                <div class="table-responsive">
                                    <br><br>
                                    <table id="carOwnerTable" class="table table-striped display nowrap">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Owner Name</th>
                                                <th>Contact No.</th>
                                                <th>Email</th>
                                                <th>Company</th>
                                                <th>Car Model</th>
                                                <th>Vehicle Number</th>
                                                <!-- <th>License Plate Number</th> -->
                                                <th>Status</th>
                                                <th>Address</th>
                                                <th>Manufacture Year</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include('conn.php');

                                            $query = "SELECT vehicleNumber, company, carModel FROM enquiryForm WHERE status='active'";
                                            $result = $conn->query($query);

                                            $enquiryData = []; // Store the enquiry data

                                            while ($row = $result->fetch_assoc()) {
                                                $vehicleNumbers = explode(',', $row['vehicleNumber']);
                                                $companies = explode(',', $row['company']);
                                                $carModels = explode(',', $row['carModel']);

                                                $count = count($vehicleNumbers);

                                                for ($i = 0; $i < $count; $i++) {
                                                    $enquiryData[] = [
                                                        'vehicleNumber' => trim($vehicleNumbers[$i]),
                                                        'company' => trim($companies[$i]),
                                                        'carModel' => trim($carModels[$i])
                                                    ];
                                                }
                                            }

                                            // Now, fetch car owner details and display the table
                                            $ownerQuery = "SELECT * FROM detailsOfCarsAndOwner WHERE status='active' ORDER BY id DESC";
                                            $ownerResult = $conn->query($ownerQuery);

                                            if ($ownerResult->num_rows > 0) {
                                                $srNo = 1;
                                                while ($row = $ownerResult->fetch_assoc()) {
                                                    // Find the corresponding car details from enquiryData
                                                    $carDetails = null;
                                                    foreach ($enquiryData as $data) {
                                                        if ($data['vehicleNumber'] == $row['licensePlateNumber']) {
                                                            $carDetails = $data;
                                                            break;
                                                        }
                                                    }

                                                    echo "<tr>
                                                        <td>{$srNo}</td>
                                                        <td>{$row['ownerName']}</td>
                                                        <td>{$row['mobileNumber']}</td>
                                                        <td>{$row['email']}</td>
                                                        <td>" . ($carDetails ? $carDetails['company'] : '') . "</td>
                                                        <td>" . ($carDetails ? $carDetails['carModel'] : '') . "</td>
                                                        <td>{$row['licensePlateNumber']}</td>
                                                        <td>{$row['status']}</td>
                                                        <td>{$row['address']}</td>
                                                        <td>{$row['manufactureYear']}</td>
                                                        <td>
                                                            <a href='?edit_id={$row['id']}' class='btn btn-inverse-warning btn-rounded btn-icon'>
                                                                <br> <i class='mdi mdi-border-color'></i>
                                                            </a>
                                                            <a href='#' onclick='confirmDelete({$row['id']})' class='btn btn-inverse-danger btn-rounded btn-icon'>
                                                                <br> <i class='mdi mdi-delete'></i>
                                                            </a>
                                                        </td>
                                                    </tr>";
                                                    $srNo++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='12' class='text-center'>No car owner records found.</td></tr>";
                                            }

                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php include '../../partials/_footer.html'; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- jQuery (Load First) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

        </body>

        <script>
            function confirmDelete(id) {
                if (confirm('Are you sure you want to delete this item?')) {
                    window.location.href = 'carDetails.php?delete_id=' + id;
                }
            }
            //Execl
            $(document).ready(function () {
                $("#licensePlateNumber").select2({
                    placeholder: "Select Vehicle Number",
                    allowClear: true
                });

                // On Vehicle Number change, fetch related details
                $("#licensePlateNumber").change(function () {
                    var vehicleNumber = $(this).val();

                    if (vehicleNumber) {
                        // Find the corresponding company and car model
                        var selectedVehicle = enquiryData.find(function (vehicle) {
                            return vehicle.vehicleNumber === vehicleNumber;
                        });

                        if (selectedVehicle) {
                            $("#companyName").val(selectedVehicle.company);
                            $("#carModel").val(selectedVehicle.carModel);
                        } else {
                            $("#companyName").val('');
                            $("#carModel").val('');
                        }
                    } else {
                        // Reset fields if no vehicle selected
                        $("#companyName").val('');
                        $("#carModel").val('');
                    }
                });

                // Store enquiry data to a global javascript variable for accessing at client side
                var enquiryData = <?php echo json_encode($enquiryData); ?>;


                $('#carOwnerTable').DataTable({
                    dom: '<"d-flex justify-content-between align-items-center"<"top-section"B><"search-section"f>>' +
                        'rt' +
                        '<"d-flex justify-content-between align-items-center mt-3"<"bottom-section"l><"pagination-section"p>>',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export to Excel',
                            className: 'btn btn-success',
                            title: 'Car and Car Owner Data',
                            exportOptions: {
                                columns: ':not(:last-child)' // Exclude the last column (Actions)
                            }
                        }
                    ],
                    paging: true,
                    searching: true,
                    responsive: true, // Make it responsive
                    autoWidth: false, // Prevent incorrect column width
                    order: [[0, 'asc']]
                });
            });
            $(document).ready(function () {
                // Initialize Select2 for searchable dropdown
                $("#licensePlateNumber").select2({
                    placeholder: "Select Vehicle Number",
                    allowClear: true
                });

                // On Vehicle Number change, fetch related details
                $("#licensePlateNumber").change(function () {
                    var vehicleNumber = $(this).val();

                    if (vehicleNumber) {
                        // Find the corresponding company and car model
                        var selectedVehicle = enquiryData.find(function (vehicle) {
                            return vehicle.vehicleNumber === vehicleNumber;
                        });

                        if (selectedVehicle) {
                            $("#companyName").val(selectedVehicle.company);
                            $("#carModel").val(selectedVehicle.carModel);
                        } else {
                            $("#companyName").val('');
                            $("#carModel").val('');
                        }
                    } else {
                        // Reset fields if no vehicle selected
                        $("#companyName").val('');
                        $("#carModel").val('');
                    }
                });
                // Apply Select2 to Company Dropdown
                $('#companyName').select2({
                    placeholder: "Select Company", // Placeholder text
                    allowClear: true // Option to clear selections
                });

                $('#carModel').select2({
                    placeholder: "Select Car Model",
                    allowClear: true,
                    ajax: {
                        url: '/path/to/car/models/api', // Replace with actual URL
                        dataType: 'json',
                        processResults: function (data) {
                            return {
                                results: data.models.map(function (model) {
                                    return {
                                        id: model.id,
                                        text: model.name
                                    };
                                })
                            };
                        }
                    }
                });

                // Apply Select2 to Manufacture Year Dropdown
                $('#manufactureYear').select2({
                    placeholder: "Select Year", // Placeholder text
                    allowClear: true // Option to clear selections
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the delete URL
                        window.location.href = 'carDetails.php?delete_id=' + id;
                    }
                });
            }
        </script>