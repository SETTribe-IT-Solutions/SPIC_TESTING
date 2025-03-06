<?php
include ('conn.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Appointment Booking</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">


 <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .container-fluid.page-body-wrapper {
        width: 100% !important; /* Ensures full width */
        max-width: 100%;        /* Prevents any max width restrictions */
        padding-left: 0;        /* Optional: to remove any left padding */
        padding-right: 0;       /* Optional: to remove any right padding */
        margin-left: 0;         /* Optional: to ensure no left margin */
       margin-right: 0;        /* Optional: to ensure no right margin */
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
           <?php

// Add vehicleNumber in appointment booking form and handle it
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id'])) {
    if (isset($_POST['customerId'], $_POST['appointmentDate'], $_POST['appointmentTime'], $_POST['ServiceType'], $_POST['amount'], $_POST['vehicleNumber'])) {
        $customerId = $_POST['customerId'];
        $appointmentDate = $_POST['appointmentDate'];
        $appointmentTime = $_POST['appointmentTime'];
        $ServiceType = $_POST['ServiceType']; // This will be an array
        $amount = $_POST['amount']; // Get amount from form
        $vehicleNumber = $_POST['vehicleNumber']; // Get vehicle number from form

        // Fetch fullName based on customerId
        $query = "SELECT fullName FROM enquiryForm WHERE customer_id = '$customerId' AND status ='active'";
        $result = $conn->query($query);
        $fullName = ($result->num_rows > 0) ? $result->fetch_assoc()['fullName'] : null;

        $status = isset($_POST['status']) ? $_POST['status'] : '';

        $datetime = date("Y-m-d H:i:s");

        // Generate unique appointmentId
        do {
            $appointmentId = rand(10000, 99999);
            $checkQuery = "SELECT appointmentId FROM appointment WHERE appointmentId = '$appointmentId'";
            $result = $conn->query($checkQuery);
        } while ($result->num_rows > 0);

        // Convert ServiceType array to a comma-separated string
        $ServiceTypeStr = implode(", ", $ServiceType);

        // Insert into database with vehicleNumber field
        $sql = "INSERT INTO appointment (appointmentId, customerId, fullName, appointmentDate, appointmentTime, ServiceType, amount, status, datetime, vehicleNumber) 
                VALUES ('$appointmentId', '$customerId', '$fullName', '$appointmentDate', '$appointmentTime', '$ServiceTypeStr', '$amount', '$status', '$datetime', '$vehicleNumber')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    Swal.fire({
                        title: 'Appointment Booked Successfully',
                        text: 'Your appointment has been successfully booked.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'appointment_yash.php';
                    });
                  </script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Edit appointment
$editingCustomerId = '';
$appointment = [];
if (isset($_GET['edit_id'])) {
    $appointmentId = $_GET['edit_id'];
    $query = "SELECT * FROM appointment WHERE appointmentId = '$appointmentId'";
    $result = $conn->query($query);
    $appointment = $result->fetch_assoc();
    $editingCustomerId = $appointment['customerId'];
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit_id'])) {
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentTime = $_POST['appointmentTime'];
    $status = $_POST['status'];
    $ServiceType = $_POST['ServiceType'];
    $amount = $_POST['amount']; // Get amount for update
    $vehicleNumber = $_POST['vehicleNumber']; // Get vehicle number for update

    $ServiceTypeStr = is_array($ServiceType) ? implode(", ", $ServiceType) : $ServiceType;

    // Update query with vehicleNumber field
    $updateQuery = "UPDATE appointment 
                    SET appointmentDate = '$appointmentDate', 
                        appointmentTime = '$appointmentTime', 
                        ServiceType = '$ServiceTypeStr', 
                        amount = '$amount', 
                        status = '$status', 
                        vehicleNumber = '$vehicleNumber'
                    WHERE appointmentId = '$appointmentId'";

    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Appointment updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = 'appointment_yash.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Error: " . $conn->error . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}

// Delete appointment
if (isset($_GET['delete_id'])) {
    $appointmentId = $_GET['delete_id'];
    $sql = "DELETE FROM appointment WHERE appointmentId = '$appointmentId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Appointment deleted successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location.href = 'appointment_yash.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Error: " . $conn->error . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
    exit;
}



// Fetch appointments with JOIN query
$sql = "SELECT a.appointmentId, co.fullName, co.carModel, co.contactNumber AS contactNumber, a.appointmentDate, a.appointmentTime, a.ServiceType, a.status FROM appointment a INNER JOIN enquiryForm co ON a.customerId = co.customer_id;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
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
#appointmentTable {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    table-layout: auto;
}

/* Styling for the table headers */
#appointmentTable thead {
    background-color: #223e9c;
    color: white;
    font-size: 14px; /* Decreased from 16px */
    font-weight: bold;
}

#appointmentTable th, #appointmentTable td {
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
    #appointmentTable thead {
        font-size: 12px;
    }
    #appointmentTable tbody tr {
        font-size: 11px;
    }
    #appointmentTable th, #appointmentTable td {
        padding: 5px 8px; /* Further reduce padding */
    }
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


    </style>
</head>
<body>
    <div class="main-panel">
        <div class="content-wrapper">
        <h2 class="card-title text-center">Appointment Booking</h2>
        <br>
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                    
                        

                        <!-- Display success message -->
                        <?php if (!empty($successMessage)) { ?>
                            <div class="alert alert-success">
                                <?php echo $successMessage; ?>
                            </div>
                        <?php } ?>

                        <form class="form-sample" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label>Customer</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-lg" id="customer-select" name="customerId" aria-label="Customer Information" required>
                                                <option value="">Select Customer</option>
                                                    <?php
                                                    $customers = $conn->query("SELECT customer_Id, fullName FROM enquiryForm WHERE status = 'active'");
                                                    while ($row = $customers->fetch_assoc()) {
                                                        $selected = ($row['customer_Id'] == $editingCustomerId) ? 'selected' : '';
                                                        echo "<option value='{$row['customer_Id']}' $selected>{$row['fullName']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
    <label>Vehicle Number</label>
    <div class="col-sm-9">
        <select class="form-control form-control-lg" id="vehicle-number-select" name="VehicleNumber[]" aria-label="Vehicle Number" required multiple onchange="updateVehicleDetails()">
            <?php
            // Fetch VehicleNumber from enquiry table
            $vehicleQuery = "SELECT VehicleNumber FROM enquiryForm WHERE status = 'active'";
            $vehicleResult = $conn->query($vehicleQuery);

            // Explode the existing selected VehicleNumbers
            $selectedVehicleNumbers = isset($appointment['VehicleNumber']) ? explode(", ", $appointment['VehicleNumber']) : [];
            $vehicleData = [];

            if ($vehicleResult->num_rows > 0) {
                while ($row = $vehicleResult->fetch_assoc()) {
                    $vehicleNumber = $row['VehicleNumber'];
                    $selected = in_array($vehicleNumber, $selectedVehicleNumbers) ? 'selected' : '';
                    echo "<option value='$vehicleNumber' $selected>$vehicleNumber</option>";
                    $vehicleData[$vehicleNumber] = $vehicleNumber;
                }
            } else {
                echo "<option disabled>No Vehicles Available</option>";
            }
            ?>
        </select>
    </div>
</div>


                                <div class="col-md-6">
    <div class="form-group row">
        <label>Service Type</label>
        <div class="col-sm-9">
            <select class="form-control form-control-lg" id="service-type-select" name="ServiceType[]" aria-label="Service Type" required multiple onchange="updateAmount()">
                <?php
                // Fetch ServiceType and Serviceamount from masterService table
                $serviceQuery = "SELECT ServiceType, Serviceamount FROM masterService WHERE status = 'active'";
                $serviceResult = $conn->query($serviceQuery);

                // Explode the existing selected ServiceType
                $selectedServiceTypes = isset($appointment['ServiceType']) ? explode(", ", $appointment['ServiceType']) : [];
                $serviceData = [];

                if ($serviceResult->num_rows > 0) {
                    while ($row = $serviceResult->fetch_assoc()) {
                        $serviceName = $row['ServiceType'];
                        $serviceAmount = $row['Serviceamount']; 
                        $selected = in_array($serviceName, $selectedServiceTypes) ? 'selected' : '';
                        echo "<option value='$serviceName' data-amount='$serviceAmount' $selected>$serviceName - ₹$serviceAmount</option>";
                        $serviceData[$serviceName] = $serviceAmount;
                    }
                } else {
                    echo "<option disabled>No Services Available</option>";
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group row">
        <label for="appointmentDate">Appointment Date</label>
        <div class="col-sm-9">
            <input type="date" class="form-control form-control-lg" id="appointmentDate" name="appointmentDate" placeholder="Select Appointment Date" required
                <?php echo isset($appointment) ? 'value="' . $appointment['appointmentDate'] . '"' : ''; ?>>
        </div>
    </div>
</div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="appointmentTime">Appointment Time</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="appointmentTime" name="appointmentTime" class="form-control" placeholder="Enter Appointment Time (e.g., 10:00 AM)" required
                                                <?php echo isset($appointment) ? 'value="' . $appointment['appointmentTime'] . '"' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                           <div class="col-md-6">
    <div class="form-group row">
        <label for="status">Appointment Status</label>
        <div class="col-sm-9">
            <select class="form-control form-control-lg" id="status" name="status" required>
                <option value="Pending" <?php echo isset($appointment) && $appointment['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Confirmed" <?php echo isset($appointment) && $appointment['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Cancelled" <?php echo isset($appointment) && $appointment['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
    </div>
</div>
<!-- Amount Textbox -->
<div class="col-md-6">
    <div class="form-group row">
        <label>Amount</label>
        <div class="col-sm-9">
            <!-- If you want it read-only, use hidden input to send the data -->
            <input type="text" class="form-control form-control-lg" id="amount-box" name="amount" value="<?php echo isset($appointment['amount']) ? $appointment['amount'] : ''; ?>" readonly>
            <!-- Alternatively, remove readonly if you want the user to modify the amount -->
            <!-- <input type="text" class="form-control form-control-lg" id="amount-box" name="amount" value="<?php echo isset($appointment['amount']) ? $appointment['amount'] : ''; ?>"> -->
        </div>
    </div>
</div>
                            <div class="sub">
                            <button type="submit" class="btn-lg btn-primary btn-icon-text">
    <i class="mdi mdi-file-check btn-icon-prepend"></i>
    <?php echo isset($_GET['edit_id']) ? 'Update Appointment' : 'Book Appointment'; ?>
</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Appointment Details</h4>
                <div class="table-responsive"><br><br>
                
<table id="appointmentTable" class="table table-striped display nowrap">
    <thead>
        <tr>
            <th>Sr.No.</th>
            <th>Full Name</th>
            <th>Car Model</th>
            <th>Contact No.</th>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Service Type</th>
            <th>Appointment Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($result->num_rows > 0) {
        $srNo = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$srNo}</td>
                <td>{$row['fullName']}</td>
                <td>{$row['carModel']}</td>
                <td>{$row['contactNumber']}</td>
                <td>{$row['appointmentDate']}</td>
                <td>{$row['appointmentTime']}</td>
                <td>{$row['ServiceType']}</td>
                <td class='status-{$row['status']}'>
                    {$row['status']}
                </td>
                <td>
                    <a href='?edit_id={$row['appointmentId']}' class='btn btn-inverse-warning btn-rounded btn-icon'>
                        <br><i class='mdi mdi mdi-border-color'></i>
                    </a>
                    <a href='#' onclick='confirmDelete({$row['appointmentId']})' class='btn btn-inverse-danger btn-rounded btn-icon'>
                        <br><i class='mdi mdi-delete'></i>
                    </a>
                </td>
            </tr>";
            $srNo++;
        }
    } else {
        echo "<tr><td colspan='9'>No appointments found.</td></tr>";
    }
?>
    </tbody>
</table>
                </div>
            </div>
        </div>
    </div> 
      
</div>  <?php include '../partials/_footer.html'; ?>   
    </div>
   </body>
</html>

<?php
$conn->close();
?>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="vendors/chart.js/Chart.min.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="/js/jquery.cookie.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
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
    function updateAmount() {
        let select = document.getElementById("service-type-select");
        let amountBox = document.getElementById("amount-box");
        let totalAmount = 0;

        for (let option of select.options) {
            if (option.selected) {
                totalAmount += parseInt(option.getAttribute("data-amount")) || 0;
            }
        }
        
        amountBox.value = totalAmount; // Set total amount
    }



    $(document).ready(function() {
        $("#customer-select, #service-type-select, #status").select2({
            width: '100%'  // Ensures all select elements have the same width
        });
    });

    $(document).ready(function () {
    $('#appointmentTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Appointment Details',
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
            { targets: [1, 2, 6], className: 'text-left' }, // Left-align text fields
            { targets: [3, 4, 5, 7], className: 'text-right' }, // Right-align numbers and dates
            { targets: '_all', className: 'dt-head-center' } // Center-align all headers
        ]
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

$(document).ready(function() {
        $('#service-type-select').select2({
            placeholder: "Select Service Types", // Placeholder text
            allowClear: true // Option to clear selections
        });
    });

    // Apply Select2 to both dropdowns
    $(document).ready(function() {
        $('#customer-select').select2({
            placeholder: "Select Customer",
            allowClear: true
        });

        $('#service-type-select').select2({
            placeholder: "Select Service Type",
            allowClear: true
        });
    });


    function confirmDelete(appointmentId) {
        if (confirm("Are you sure you want to delete this appointment?")) {
            window.location.href = '?delete_id=' + appointmentId;
        }
    }


 // Get today's date in YYYY-MM-DD format
 let today = new Date().toISOString().split('T')[0];

// Set the min attribute of the input field
document.getElementById("appointmentDate").setAttribute("min", today);


 // Fetch data from the PHP endpoint
 fetch('path_to_your_php_file.php')  // Replace with the actual path to your PHP file
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('vehicle-select');
            
            // Clear any previous options
            select.innerHTML = '<option value="">Select Vehicle</option>';
            
            // Loop through the data and add options to the select element
            data.forEach(vehicle => {
                const option = document.createElement('option');
                option.value = vehicle.vehicleId;
                option.textContent = `${vehicle.fullName} - ${vehicle.vehicleNumber}`; // Show both full name and vehicle number
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching vehicle data:', error));
</script>