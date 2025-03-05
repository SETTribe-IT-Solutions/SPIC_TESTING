<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Set security headers
header("X-Content-Type-Options: nosniff");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Example of setting a secure cookie
setcookie("session_id", urlencode(session_id()), [
    'expires' => time() + 3600, // 1 hour
    'path' => '/',
    'domain' => 'yourdomain.com', // Change to your domain
    'secure' => true, // Only send cookie over HTTPS
    'httponly' => true, // Prevent JavaScript access to the cookie
    'samesite' => 'Strict' // Adjust as necessary
]);

// Database connection
include('conn.php');

// Handle AJAX request to fetch appointment details
if (isset($_POST['customerId']) && isset($_POST['selectedDate']) && isset($_POST['selectedCarModel'])) {
    $customerId = $_POST['customerId'];
    $selectedDate = $_POST['selectedDate'];
    $selectedCarModel = $_POST['selectedCarModel'];

    $sql = "SELECT a.vehicleNumber, a.carModel, a.appointmentDate, a.appointmentTime, a.serviceType, a.amount as appointmentAmount, a.appointmentId
            FROM appointment a
            JOIN enquiryForm e ON a.customerId = e.customer_Id
            WHERE a.customerId = ? AND a.appointmentDate = ? AND a.carModel = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $customerId, $selectedDate, $selectedCarModel);
    $stmt->execute();
    $stmt->bind_result($vehicleNumber, $carModel, $appointmentDate, $appointmentTime, $serviceType, $appointmentAmount, $appointmentId);

    $response = array();
    if ($stmt->fetch()) {
        $response = array(
            'vehicleNumber' => $vehicleNumber,
            'carModel' => $carModel,
            'appointmentDate' => $appointmentDate,
            'appointmentTime' => $appointmentTime,
            'serviceType' => $serviceType,
            'appointmentAmount' => $appointmentAmount,
            'appointmentId' => $appointmentId
        );
    } else {
        $response = array('error' => 'No details found for appointment on this date and car model.');
    }
    $stmt->close();

    echo json_encode($response);
    exit();
}

// Handle AJAX request to fetch appointment dates for a customer
if (isset($_POST['customerIdForDates'])) {
    $customerId = $_POST['customerIdForDates'];
    $sql_dates = "SELECT DISTINCT appointmentDate FROM appointment WHERE customerId = ? AND status='Confirmed'";
    $stmt_dates = $conn->prepare($sql_dates);
    $stmt_dates->bind_param("s", $customerId);
    $stmt_dates->execute();
    $stmt_dates->bind_result($appointmentDate);

    $dates = array();
    while ($stmt_dates->fetch()) {
        $dates[] = $appointmentDate;
    }

    $stmt_dates->close();
    echo json_encode($dates);
    exit();
}

// Handle AJAX request to fetch car models for a customer and selected date
if (isset($_POST['customerIdForCarModels']) && isset($_POST['selectedDateForCarModels'])) {
    $customerId = $_POST['customerIdForCarModels'];
    $selectedDate = $_POST['selectedDateForCarModels'];
    $sql_car_models = "SELECT DISTINCT carModel FROM appointment WHERE customerId = ? AND appointmentDate = ? AND status='Confirmed'";
    $stmt_car_models = $conn->prepare($sql_car_models);
    $stmt_car_models->bind_param("ss", $customerId, $selectedDate);
    $stmt_car_models->execute();
    $stmt_car_models->bind_result($carModel);

    $car_models = array();
    while ($stmt_car_models->fetch()) {
        $car_models[] = $carModel;
    }

    $stmt_car_models->close();
    echo json_encode($car_models);
    exit();
}

// Include SweetAlert Library (add this script in the <head> or before any alert)
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

// Function to generate a unique quotationId
function generateUniqueQuotationId($conn) {
    $random_number = rand(100000, 999999);
    $timestamp = time();
    $quotationId = $random_number . $timestamp;

    $sql_check = "SELECT COUNT(*) FROM quotationForm WHERE quotationId = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $quotationId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return generateUniqueQuotationId($conn);
    }

    return $quotationId;
}

// Fetch customer data for dropdown (only unique customers)
$sql_customers = "SELECT
                    e.customer_Id,
                    e.fullName
                FROM
                    enquiryForm e
                WHERE
                    e.status = 'active'
                    AND EXISTS (SELECT 1 FROM appointment a WHERE a.customerId = e.customer_Id AND a.status='Confirmed')
                GROUP BY
                    e.customer_Id, e.fullName
                ORDER BY
                    e.fullName";



$result_customers = $conn->query($sql_customers);

// Fetch worker data for dropdown
$sql_workers = "SELECT id,name FROM workers WHERE status = 'active'";
$result_workers = $conn->query($sql_workers);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['Customer'];
    $carModel = $_POST['carModel'];
    $preparedByArray = $_POST['preparedBy']; // Get array of selected names
    $preparedBy = implode(", ", $preparedByArray); // Convert array to comma-separated string
    $vehicleNumber = $_POST['vehicleNumber'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $appointmentId = $_POST['appointmentId'];


    if (empty($appointmentId)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Appointment not found for the selected customer!'
            });
        </script>";
        exit();
    }

    // Generate unique quotationId
    $quotationId = generateUniqueQuotationId($conn);

    // Set the status (default to "active" or another value)
    $status = "active"; // Example: Set status to active

    // Insert data into quotationForm
    $datetime = date("Y-m-d H:i:s");
    $sql_insert = "INSERT INTO quotationForm (customerId, carModel, quotationId, amount, preparedBy, vehicleNumber, datetime, appointmentId, status, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
                
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssssssss", $customerId, $carModel, $quotationId, $amount, $preparedBy, $vehicleNumber, $datetime, $appointmentId, $status, $description);

    if ($stmt_insert->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Quotation Created',
                    text: 'Quotation created successfully!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                });
            });
        </script>";
    } else {
        // SweetAlert error
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to create quotation. Please try again!'
                });
            });
        </script>";
    }
    $stmt_insert->close();
}

// Fetch data for Quotation History
$sql_history = "SELECT qf.quotationId, qf.carModel, qf.amount, qf.preparedBy, qf.vehicleNumber,ef.fullName, a.ServiceType, qf.description
              FROM quotationForm qf 
              JOIN enquiryForm ef ON qf.customerId = ef.customer_Id
              JOIN appointment a ON qf.appointmentId=a.appointmentId
              ORDER BY qf.dateTime DESC";
$result_history = $conn->query($sql_history);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Quotation Preparation</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/CarzspaLogo.jpg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .container-fluid.page-body-wrapper {
            width: 100% !important; /* Ensures full width */
            max-width: 100%;        /* Prevents any max width restrictions */
            padding-left: 0;        /* Optional: to remove any left padding */
            padding-right: 0;       /* Optional: to remove any right padding */
            margin-left: 0;         /* Optional: to ensure no left margin */
            margin-right: 0;        /* Optional: to ensure no right margin */
        }
        .required {
            color: red;
            font-weight: bold; /* Optional for emphasis */
        }
        /* Table headers */
        .table thead th {
            background-color: #223e9c; /* Professional blue background */
            color: #fff; /* White text */
            font-size: 12px;
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
            padding: 4px 4px;
        }

        .table tbody td::before {
            content: attr(data-label); /* Add labels for mobile view */
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 8px;
            color: #223e9c; /* Blue text for labels */
            }
        }

            /* Make the table responsive */
    .table.table-striped.display.nowrap {
        width: 100%;
        max-width: 900px; /* Adjust this value as needed */
        margin: auto; /* Center the table */
         display: block;  /* Enable block display for overflow */
        overflow-x: auto;  /* Enable horizontal scrolling */
    }

    /* Ensure the table has a smaller width */
    .table.table-striped.display.nowrap {
       border-collapse: collapse;
        font-family: Arial, sans-serif;
        table-layout: fixed;
        width: 100%;
    }

    /* Styling for the table headers */
   .table.table-striped.display.nowrap thead {
        background-color: #223e9c;
        color: white;
        font-size: 14px; /* Decreased from 16px */
        font-weight: bold;
    }

    .table.table-striped.display.nowrap th,
    .table.table-striped.display.nowrap td {
        padding: 8px 10px; /* Reduced padding */
        text-align: center;
        border: 1px solid #ddd;
        font-size: 13px; /* Decreased from 14px */
    }

    /* Make sure the select elements have the same width */
    .select2-container {
        width: 80%% !important; /* Force Select2 width to be 100% */
    }

    /* Style the Select2 dropdowns */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        height: calc(1.5em + 0.85rem + 5px); /* Match text input height */
        padding: 0.375rem 0.75rem; /* Consistent padding */
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

        /* Style for Date selection dropdown */
     .date-dropdown {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

    /* Style for Car Model selection dropdown */
     .car-model-dropdown {
            width: 100%;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
    </style>

      <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
      <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
       <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    // Initial blank state for date dropdown
    let initialDateDropdown = '<select id="dateDropdown" class="date-dropdown">';
    initialDateDropdown += '<option value="">Select Customer First</option>';
    initialDateDropdown += '</select>';
    $('#dateDropdownContainer').html(initialDateDropdown);

    // Initial blank state for car model dropdown
    let initialCarModelDropdown = '<select id="carModelDropdown" class="car-model-dropdown">';
    initialCarModelDropdown += '<option value="">Select Date First</option>';
    initialCarModelDropdown += '</select>';
    $('#carModelDropdownContainer').html(initialCarModelDropdown);


    $('#Customer').change(function () {
        const customerId = $(this).val();

        $('#carModel, #vehicleNumber, #appointmentDate, #appointmentTime, #Services, #amount, #appointmentId').val(''); // Clear fields
        $('#dateDropdownContainer').empty();
        $('#carModelDropdownContainer').empty();

        if(customerId) {
            $.ajax({
                url: '',
                type: 'POST',
                data: { customerIdForDates: customerId },
                cache: false,
                success: function (response) {
                    console.log("AJAX Response for dates: ", response);
                    const dates = JSON.parse(response);
                    if(dates.length > 0){
                        let dateDropdown = '<select id="dateDropdown" class="date-dropdown">';
                        dateDropdown += '<option value="">Select Appointment Date</option>';
                        dates.forEach(date => {
                            dateDropdown += '<option value="'+date+'">'+date+'</option>';
                        })
                        dateDropdown += '</select>';

                        $('#dateDropdownContainer').html(dateDropdown);

                        //  Car Model Dropdown based on selected date
                         $('#dateDropdown').on('change', function(){
                            const selectedDate = $(this).val();
                            $('#carModelDropdownContainer').empty();

                                if(selectedDate) {
                                    $.ajax({
                                        url: '',
                                        type: 'POST',
                                        data: { customerIdForCarModels: customerId, selectedDateForCarModels: selectedDate },
                                        cache: false,
                                        success: function (response) {
                                            console.log("AJAX Response for car models: ", response);
                                            const carModels = JSON.parse(response);
                                            if(carModels.length > 0){
                                                let carModelDropdown = '<select id="carModelDropdown" class="car-model-dropdown">';
                                                carModelDropdown += '<option value="">Select Car Model</option>';
                                                carModels.forEach(carModel => {
                                                    carModelDropdown += '<option value="'+carModel+'">'+carModel+'</option>';
                                                })
                                                carModelDropdown += '</select>';

                                                $('#carModelDropdownContainer').html(carModelDropdown);

                                                // Fetch Appointment Details on car model select

                                                 $('#carModelDropdown').on('change', function(){
                                                    const selectedCarModel = $(this).val();
                                                    if(selectedCarModel){
                                                        $.ajax({
                                                            url: '',
                                                            type: 'POST',
                                                            data: { customerId: customerId, selectedDate:selectedDate, selectedCarModel: selectedCarModel },
                                                            cache: false,
                                                            success: function (response) {
                                                                console.log("AJAX Response for details: ", response);
                                                                const data = JSON.parse(response);
                                                                if (data.error) {
                                                                    alert(data.error);
                                                                } else {
                                                                    $('#carModel').val(data.carModel);
                                                                    $('#vehicleNumber').val(data.vehicleNumber);
                                                                    $('#appointmentDate').val(data.appointmentDate);
                                                                    $('#appointmentTime').val(data.appointmentTime);
                                                                    $('#Services').val(data.serviceType);
                                                                    $('#amount').val(data.appointmentAmount);
                                                                    $('#appointmentId').val(data.appointmentId);
                                                                }
                                                            },
                                                            error: function () {
                                                                alert('Failed to fetch customer details. Please try again.');
                                                            }
                                                        });
                                                    }
                                                });
                                            } else{
                                                $('#carModelDropdownContainer').html('<p>No car models found for this date.</p>');
                                                $('#carModel, #vehicleNumber, #appointmentDate, #appointmentTime, #Services, #amount, #appointmentId').val('');
                                            }

                                        },
                                        error: function () {
                                            alert('Failed to fetch car models. Please try again.');
                                        }
                                    });
                                } else {
                                    $('#carModelDropdownContainer').html(initialCarModelDropdown);
                                    $('#carModel, #vehicleNumber, #appointmentDate, #appointmentTime, #Services, #amount, #appointmentId').val(''); // Clear fields
                                }
                         });

                    } else{
                        $('#dateDropdownContainer').html('<p>No appointments found for this customer.</p>');
                        $('#carModelDropdownContainer').html(initialCarModelDropdown);
                        $('#carModel, #vehicleNumber, #appointmentDate, #appointmentTime, #Services, #amount, #appointmentId').val('');
                    }


                },
                error: function () {
                    alert('Failed to fetch dates. Please try again.');
                }
            });
        }
        else {
            $('#dateDropdownContainer').html(initialDateDropdown);
            $('#carModelDropdownContainer').html(initialCarModelDropdown);
            $('#carModel, #vehicleNumber, #appointmentDate, #appointmentTime, #Services, #amount, #appointmentId').val(''); // Clear fields
        }

    });
});
</script>
 <script>
         $(document).ready(function() {
             $('#Customer').select2({
                placeholder: "Select Customer",
                  width: '100%'
             });
         });
    </script>
      <script>
         $(document).ready(function() {
            $('#preparedBy').select2({
                placeholder: "Select Preparers",
                width: '100%',
                 multiple: true
             });
         });
    </script>
</head>

<body>
<div class="container-scroller d-flex">
        <?php include '../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../partials/_navbar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <h2 class="card-title text-center">Quotation Preparation</h2>
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-sample" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Customer" class="col-sm-3 col-form-label">Customer <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="Customer" id="Customer" class="form-control" required>
                                                        <option value="">Select Customer</option>
                                                        <?php while ($row = $result_customers->fetch_assoc()) {
                                                            echo "<option value='{$row['customer_Id']}'>{$row['fullName']}</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="dateDropdownContainer" class="col-sm-3 col-form-label">Appoint Date<span class="required">*</span></label>
                                                <div class="col-sm-9" id="dateDropdownContainer">
                                                    <select id="dateDropdown" class="date-dropdown">
                                                        <option value="">Select Customer First</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModelDropdownContainer" class="col-sm-3 col-form-label">Car Model<span class="required">*</span></label>
                                                <div class="col-sm-9" id="carModelDropdownContainer">
                                                    <select id="carModelDropdown" class="car-model-dropdown">
                                                        <option value="">Select Date First</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="appointmentTime" class="col-sm-3 col-form-label">Appointment Time<span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="appointmentTime" id="appointmentTime" class="form-control" placeholder="Select Time" readonly required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="display:none;">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">Car Model <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="carModel" id="carModel" class="form-control" placeholder="Enter car model" readonly required>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="vehicleNumber" class="col-sm-3 col-form-label">Vehicle No <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="vehicleNumber" name="vehicleNumber" class="form-control" placeholder="Enter Vehicle Number" readonly required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                         <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Services" class="col-sm-3 col-form-label">Services <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="Services" id="Services" class="form-control" placeholder="Enter Services" readonly required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="amount" class="col-sm-3 col-form-label">Amount <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="amount" id="amount" class="form-control" placeholder="Enter amount" readonly required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="preparedBy" class="col-sm-3 col-form-label">Prepared By <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select name="preparedBy[]" id="preparedBy" class="form-control" multiple required>
                                                        <?php while ($row = $result_workers->fetch_assoc()) {
                                                            echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="description" class="col-sm-3 col-form-label">Item Desc <span class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="description" name="description" class="form-control" placeholder="Enter item description" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="appointmentId" id="appointmentId">
                                    <input type="hidden" name="appointmentDate" id="appointmentDate">
                                    <div class="text-left mt-4">
                                        <button type="submit" class="btn btn-primary">Generate Quotation</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-danger">Quotation Table</h4>
                                <div class="table-responsive"><br>
                                    <table id="appointmentTable" class="table table-striped display nowrap">
                                        <thead>
                                            <tr>
                                                <th data-label="Sr.No.">Sr.No.</th>
                                                <th data-label="Full Name">Full Name</th>
                                                <th data-label="Car Model">Car Model</th>
                                                <th data-label="Vehicle Number">Vehicle Number</th>
                                                <th data-label="Service Type">Prepared By</th>
                                                  <th data-label="Service Type">Services</th>
                                                  <th data-label="Item Description">Item Description</th>
                                                  <th data-label="Amount">Amount</th>
                                                <th data-label="Action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $srNo = 1; // Initialize serial number
                                            while ($row = $result_history->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td data-label='Sr.No.'>" . htmlspecialchars($srNo++) . "</td>";
                                                echo "<td data-label='Full Name'>" . htmlspecialchars($row['fullName']) . "</td>"; 
                                                echo "<td data-label='Car Model'>" . htmlspecialchars($row['carModel']) . "</td>";
                                                echo "<td data-label='Vehicle Number'>" . htmlspecialchars($row['vehicleNumber']) . "</td>";
                                                 echo "<td data-label='Service Type'>" . htmlspecialchars($row['preparedBy']) . "</td>";
                                                  echo "<td data-label='Service Type'>" . htmlspecialchars($row['ServiceType']) . "</td>";
                                                   echo "<td data-label='Item Description'>" . htmlspecialchars($row['description']) . "</td>";
                                                 echo "<td data-label='Amount'>" . htmlspecialchars(number_format($row['amount'], 2)) . "</td>"
                                                ;
                                                echo "<td data-label='Action'>
                                                        <a href='mailto:varadparkhe@gmail.com?subject=Quotation Details&body=Please find the attached quotation details'
                                                        class='btn btn-inverse-danger btn-rounded btn-icon' title='Send Email'>
                                                          <br>  <i class='mdi mdi-email-open'></i>
                                                        </a>
                                                        <a href='quotation_receipt.php?quotationId=" . urlencode($row['quotationId']) . "'
                                                        class='btn btn-inverse-info btn-rounded btn-icon' title='Print'>
                                                          <br>  <i class='mdi mdi-printer'></i>
                                                        </a>
                                                    </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php include '../partials/_footer.html'; ?>
            </div>
        </div>
    </div>
     <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
       <!-- Include Flatpickr JS -->
    <cdn.jsdelivr.net/npm/flatpickr"></script>
       <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/jquery.cookie.js" type="text/javascript"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/dashboard.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr("#appointmentTime", {
            enableTime: true,        // Enable time selection
            noCalendar: true,        // Disable the calendar (only time picker)
            dateFormat: "h:i K",     // Format: 12-hour with AM/PM
            time_24hr: false,        // Disable 24-hour format
            defaultHour: 9,          // Default hour shown
            defaultMinute: 30,       // Default minute shown
            minuteIncrement: 5       // Increment minutes in steps of 5
        });
    });
    </script>
        <script>
    $(document).ready(function () {
        $('#appointmentTable').DataTable({
             dom: 'Bfrtip',

            autoWidth: false,
            buttons: [
                {
                    extend: 'excelHtml5',
                     title: 'Quotation Details',
                    text: 'Export to Excel',
                    className: 'btn btn-success',
                     exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Center-align all cells in the first column (Sr.No.)
                        $('row c[r^="A"]', sheet).attr('s', '2'); // Apply center alignment
                    }
                }
            ],
            columnDefs: [
                 { targets: 0, className: 'text-center' }, // Center-align "Sr.No." column in the table
                { targets: [1, 2, 5,6], className: 'text-left' }, // Left-align text fields
                { targets: [3, 4], className: 'text-right' }, // Right-align numbers and dates
                { targets: '_all', className: 'dt-head-center' } // Center-align all headers
            ]
        });
    });
</script>
</body>
</html>