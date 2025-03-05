<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('conn.php');

// Check if we have a customer ID for fetching vehicle numbers
if (isset($_POST['customerId']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'getVehicles') {
    $customerId = $_POST['customerId'];
    $query = "SELECT vehicleNumber, carModel FROM enquiryForm WHERE customer_id = '$customerId' AND status='active'";
    $result = $conn->query($query);

    $vehicles = array();
    while ($row = $result->fetch_assoc()) {
        // Explode the comma-separated string into an array
        $vehicleNumbers = explode(',', $row['vehicleNumber']);
        $carModels = explode(',', $row['carModel']); // Explode car models too

        // Trim whitespace and remove empty entries
        $trimmedVehicles = array_filter(array_map('trim', $vehicleNumbers));
        $trimmedCarModels = array_filter(array_map('trim', $carModels)); // Trim car models

        // Ensure car models correspond to vehicle numbers
        $count = count($trimmedVehicles);
        for ($i = 0; $i < $count; $i++) {
            $vehicle = $trimmedVehicles[$i];
            // Get the corresponding car model. If it doesn't exist, use an empty string.
            $carModel = isset($trimmedCarModels[$i]) ? $trimmedCarModels[$i] : '';

            $vehicles[] = array(
                'vehicleNumber' => $vehicle,
                'carModel' => $carModel  // Add carModel to the array
            );
        }
    }

    echo json_encode($vehicles);
    exit;
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
    $editingVehicleNumber = $appointment['vehicleNumber']; // Get the vehicleNumber
    $editingCarModel = $appointment['carModel']; // Get the carModel during edit
}


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
    <link rel="shortcut icon" href="../images/CarzspaLogo.jpg" />
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            /* Optional: to ensure no right margin */
        }

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
            text-align: center !important;
            /* Center-align all table headers */
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
            font-size: 14px;
            /* Decreased from 16px */
            font-weight: bold;
        }

        #appointmentTable th,
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

            #appointmentTable thead {
                font-size: 12px;
            }

            #appointmentTable tbody tr {
                font-size: 11px;
            }

            #appointmentTable th,
            #appointmentTable td {
                padding: 5px 8px;
                /* Further reduce padding */
            }
        }

        /* Make sure the select elements have the same width */
        .select2-container {
            width: 320px !important;
            /* Force Select2 width to be 100% */
        }

        /* Style the Select2 dropdowns */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            height: calc(1.9em + 0.85rem + 5px);
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
        /* Ensure the container has a fixed width */
.col-md-6 {
    position: relative;
    max-width: 100%; /* Ensure it stays within its container */
}

.required {
  color: red;
}


/* Limit the width of the Select2 dropdown */
.select2-container {
    width: 100% !important;  /* Make it fit the parent container */
}

/* Prevent the select dropdown from overflowing outside the box */
.select2-selection {
    overflow: hidden;
    max-width: 100% !important;
}

/* Optional: Adjust the height if needed */
.select2-container--default .select2-selection--multiple {
    min-height: 38px; /* Adjust as necessary */
    padding: 6px 12px;
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
          if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id']) && !isset($_POST['action'])) {
                if (isset($_POST['customerId'], $_POST['vehicleNumber'], $_POST['carModel'], $_POST['appointmentDate'], $_POST['appointmentTime'], $_POST['ServiceType'], $_POST['amount'])) {
                    $customerId = $_POST['customerId'];
                    $vehicleNumber = $_POST['vehicleNumber'];
                    $carModel = $_POST['carModel']; // Get carModel from form
                    $appointmentDate = $_POST['appointmentDate'];
                    $appointmentTime = $_POST['appointmentTime'];
                    $ServiceType = $_POST['ServiceType']; // This will be an array
                    $amount = $_POST['amount']; // Get amount from form

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

                    // Insert into database with amount and carModel fields
                    $sql = "INSERT INTO appointment (appointmentId, customerId, fullName, vehicleNumber, carModel, appointmentDate, appointmentTime, ServiceType, amount, status, datetime) 
                            VALUES ('$appointmentId', '$customerId', '$fullName', '$vehicleNumber', '$carModel', '$appointmentDate', '$appointmentTime', '$ServiceTypeStr', '$amount', '$status', '$datetime')";


                    if ($conn->query($sql) === TRUE) {
                        echo "<script>
                                Swal.fire({
                                    title: 'Appointment Booked Successfully',
                                    text: 'Your appointment has been successfully booked.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(function() {
                                    window.location.href = 'appointment_booking.php';
                                });
                              </script>";
                    } else {
                        echo "<script>alert('Error: " . $conn->error . "');</script>";
                    }
                }
            }

            // Edit appointment
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['edit_id'])) {
                $vehicleNumber = $_POST['vehicleNumber'];
                $carModel = $_POST['carModel'];
                $appointmentDate = $_POST['appointmentDate'];
                $appointmentTime = $_POST['appointmentTime'];
                $status = $_POST['status'];
                $ServiceType = $_POST['ServiceType'];
                $amount = $_POST['amount'];

                $ServiceTypeStr = is_array($ServiceType) ? implode(", ", $ServiceType) : $ServiceType;

                $updateQuery = "UPDATE appointment 
                                SET vehicleNumber = '$vehicleNumber', 
                                    carModel = '$carModel',
                                    appointmentDate = '$appointmentDate', 
                                    appointmentTime = '$appointmentTime', 
                                    ServiceType = '$ServiceTypeStr', 
                                    amount = '$amount',
                                    status = '$status' 
                                WHERE appointmentId = '$appointmentId'";

                if ($conn->query($updateQuery) === TRUE) {
                    echo "<script>
                            Swal.fire({
                                title: 'Success!',
                                text: 'Appointment updated successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = 'appointment_booking.php';
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
                                    window.location.href = 'appointment_booking.php';
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


            $sql = "SELECT a.appointmentId, co.fullName, a.vehicleNumber, a.carModel, co.contactNumber AS contactNumber, a.appointmentDate, a.appointmentTime, a.ServiceType, a.amount, a.status 
            FROM appointment a 
            INNER JOIN enquiryForm co ON a.customerId = co.customer_id";
    $result = $conn->query($sql);
            ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <h2 class="card-title text-center">Appointment Booking</h2>
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-sample" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="customer">
                                                Customer <span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-lg" id="customer-select" name="customerId" aria-label="Customer Information" required>
                                                        <option value="">Select Customer</option>
                                                        <?php
                                                        $customers = $conn->query("SELECT customer_Id, fullName FROM enquiryForm WHERE status = 'active' GROUP BY fullName");
                                                        while ($row = $customers->fetch_assoc()) {
                                                            $selected = ($row['customer_Id'] == $editingCustomerId) ? 'selected' : '';
                                                            echo "<option value='{$row['customer_Id']}' $selected>{$row['fullName']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="VehicleNumber">
                                                Vehicle Number<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-lg" id="vehicle-select" name="vehicleNumber" aria-label="Vehicle Number" required>
                                                        <option value="">Select Vehicle</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="carModel" id="carModel">

                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Services">
                                                Services<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-lg" id="service-type-select" name="ServiceType[]" aria-label="Services" required multiple onchange="updateAmount()">
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

                                          <!-- Amount Textbox -->
                                          <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Amount">
                                                Amount<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg" id="amount-box" name="amount" value="<?php echo isset($appointment['amount']) ? $appointment['amount'] : ''; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Appointment Date">
                                                Appointment Date<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <input type="date" class="form-control form-control-lg" id="appointmentDate" name="appointmentDate" placeholder="Select Appointment Date" required <?php echo isset($appointment) ? 'value="' . $appointment['appointmentDate'] . '"' : ''; ?>>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Appointment Time">
                                                Appointment Time<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="appointmentTime" name="appointmentTime" class="form-control" placeholder="Enter Appointment Time (e.g., 10:00 AM)" required <?php echo isset($appointment) ? 'value="' . $appointment['appointmentTime'] . '"' : ''; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                               
                                                <label for="Appointment Time">
                                                Appointment Status<span class="required"> *</span>
                                                </label>
                                                <div class="col-sm-9">
                                                    <select class="form-control form-control-lg" id="status" name="status" required>
                                                        <option value="Pending" <?php echo isset($appointment) && $appointment['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Confirmed" <?php echo isset($appointment) && $appointment['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                  
                                                        <option value="Cancelled" <?php echo isset($appointment) && $appointment['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
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
                                <div class="table-responsive">
                                    <br><br>

                                    <table id="appointmentTable" class="table table-striped display nowrap">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Full Name</th>
                                                <th>Vehicle Info</th>
                                                <th>Contact No.</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Time</th>
                                                <th>Service Type</th>
                                                <th>Amount</th>
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
                                                  <td>{$row['vehicleNumber']} - {$row['carModel']}</td>  
                                                <td>{$row['contactNumber']}</td>
                                                <td>{$row['appointmentDate']}</td>
                                                <td>{$row['appointmentTime']}</td>
                                                <td>{$row['ServiceType']}</td>
                                                <td>{$row['amount']}</td>
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
                                                echo "<tr><td colspan='10'>No appointments found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div><?php include '../partials/_footer.html'; ?>
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
    let bookedTimes = []; // Array to store unavailable times
    let fp; // Flatpickr instance

    document.addEventListener("DOMContentLoaded", function() {
        // Fetch booked times from the server
        function fetchBookedTimes(selectedDate) {
            // Make AJAX request
            $.ajax({
                url: 'get_booked_times.php', // Replace with the actual URL
                type: 'POST',
                data: {
                    date: selectedDate
                },
                dataType: 'json',
                success: function(data) {
                    bookedTimes = data; // Assign booked times to the global variable
                    fp.set('disable', bookedTimes.map(time => ({
                        from: time,
                        to: time
                    }))); // Disable the fetched times
                },
                error: function() {
                    console.error("Failed to fetch booked times.");
                }
            });
        }

        // Initialize Flatpickr
        fp = flatpickr("#appointmentTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            defaultHour: 9,
            defaultMinute: 30,
            minuteIncrement: 5,
            disableMobile: "true",
            onOpen: function(selectedDates, dateStr, instance) {
                // Fetch booked times when the time picker opens
                let selectedDate = document.getElementById("appointmentDate").value;
                if (selectedDate) {
                    fetchBookedTimes(selectedDate);
                }
            },
            onValueUpdate: function(selectedDates, dateStr, instance) {
                // Re-fetch booked times if the value is updated in the input
                let selectedDate = document.getElementById("appointmentDate").value;
                if (selectedDate) {
                    fetchBookedTimes(selectedDate);
                }
            }
        });

        // Fetch booked times on date selection
        $("#appointmentDate").on('change', function() {
            let selectedDate = $(this).val();
            fetchBookedTimes(selectedDate);
        });

        // Initial booked times fetch
        let initialSelectedDate = document.getElementById("appointmentDate").value;
        if (initialSelectedDate) {
            fetchBookedTimes(initialSelectedDate);
        }

         <?php if (isset($_GET['edit_id']) && isset($editingCustomerId) && isset($editingVehicleNumber) && isset($editingCarModel)): ?>
            var customerIdForEdit = '<?php echo $editingCustomerId; ?>';
            var selectedVehicle = '<?php echo $editingVehicleNumber; ?>';
            var selectedCarModel = '<?php echo $editingCarModel; ?>';

           
             var vehicleSelect = $('#vehicle-select');
             vehicleSelect.empty(); // Clear vehicle dropdown each time customer changed
             vehicleSelect.append('<option value="'+selectedVehicle+'" data-car-model="'+selectedCarModel+'" selected >'+selectedVehicle+' - ' + selectedCarModel +'</option>');
             $('#carModel').val(selectedCarModel); //set value for hidden field when editing

              if (customerIdForEdit) {
                $.ajax({
                    url: '', // php to fetch vehicle details
                    type: 'POST',
                      data: {
                        customerId: customerIdForEdit,
                        action: 'getVehicles' // Add action to POST data
                     },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.length > 0) {
                            data.forEach(function(item) {
                                if (item && item.vehicleNumber && item.vehicleNumber !== selectedVehicle ) {
                                     vehicleSelect.append('<option value="' + item.vehicleNumber + '" data-car-model="' + item.carModel + '">' + item.vehicleNumber + ' - ' + item.carModel + '</option>');
                                }
                            });
                        }

                        vehicleSelect.select2({
                           width: '100%' // Ensures vehicle select is 100% width
                       });
                    },
                   error: function() {
                        $('#vehicle-select').empty().append('<option value="">Error fetching vehicles</option>');
                    }
               });
           }
        <?php endif; ?>
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
            width: '100%'
        });

       
        //When customer selected fetch vehicle numbers
        $('#customer-select').on('change', function() {
            var customerId = $(this).val();
            if (customerId) {
                $.ajax({
                    url: '', // php to fetch vehicle details
                    type: 'POST',
                      data: {
                        customerId: customerId,
                        action: 'getVehicles' // Add action to POST data
                     },
                    dataType: 'json',
                    success: function(data) {
                        var vehicleSelect = $('#vehicle-select');
                        vehicleSelect.empty(); // Clear vehicle dropdown each time customer changed
                        vehicleSelect.append('<option value="">Select Vehicle</option>');
                        
                        if (data && data.length > 0) {
                          data.forEach(function(item) {
                            vehicleSelect.append('<option value="' + item.vehicleNumber + '" data-car-model="' + item.carModel + '">' + item.vehicleNumber + ' - ' + item.carModel + '</option>');
                          });
                        }

                        vehicleSelect.select2({
                           width: '100%' // Ensures vehicle select is 100% width
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown); // Log the error
                        $('#vehicle-select').empty().append('<option value="">Error fetching vehicles. Please try again.</option>');

                        if (jqXHR.status === 403) {
                           alert("You do not have permission to access this resource. Contact your administrator."); //optional user friendly warning
                        } else {
                           alert("An error occurred while fetching vehicle data. Please try again later.");
                        }

                    }

                });
           } else {
                $('#vehicle-select').empty().append('<option value="">Select Vehicle</option>');
            }
        });

          // On vehicle number change, update the carModel hidden input
        $('#vehicle-select').on('change', function() {
           var carModel = $(this).find(':selected').data('car-model');
           $('#carModel').val(carModel);
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
                        className: 'btn btn-light',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (Action)
                        }
                    }
                ],
                initComplete: function () {
                    console.log('Column count:', $('#appointmentTable thead th').length);
                },
                columnDefs: [
                    { targets: 0, className: 'text-center' },
                    { targets: [1, 2], className: 'text-left' },
                    { targets: [3], className: 'text-right' },
                    { targets: '_all', className: 'dt-head-center' }
                ]
            });
        });

$(document).ready(function() {
        $('#service-type-select').select2({
            placeholder: "Select Service Types", // Placeholder text
            allowClear: true // Option to clear selections
        });
        $('#vehicle-select').select2({
            placeholder: "Select vehicle number",
            allowClear: true
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



</script>

