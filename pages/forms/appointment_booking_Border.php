<?php
include ('conn.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Appointment Booking</title>
    <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="shortcut icon" href="../../images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">


 <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>




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
    <?php include '../../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
        <?php
            $basePath = '/Carzspa/';
            include '../../partials/_navbar.php';
        ?>
           <?php



// Insert data into appointment table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id'])) {
    if (isset($_POST['customerId'], $_POST['appointmentDate'], $_POST['appointmentTime'], $_POST['ServiceType'])) {
        $customerId = $_POST['customerId'];
        $appointmentDate = $_POST['appointmentDate'];
        $appointmentTime = $_POST['appointmentTime'];
        $ServiceType = $_POST['ServiceType'];

        $query = "SELECT fullName FROM enquiryForm WHERE customer_id = '$customerId' AND status ='active'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            $fullName = $customer['fullName'];
        } else {
            $fullName = null; // Or handle this case as needed
        }
        $status = "Pending";
        $datetime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO appointment (appointmentId, customerId, fullName, appointmentDate, appointmentTime, ServiceType, status, datetime) 
                VALUES ('$appointmentId', '$customerId', '$fullName', '$appointmentDate', '$appointmentTime', '$ServiceType', '$status', '$datetime')";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['appointmentId']) || empty($_POST['appointmentId'])) {
        do {
            $appointmentId = rand(10000, 99999);
            $checkQuery = "SELECT appointmentId FROM appointment WHERE appointmentId = '$appointmentId'";
            $result = $conn->query($checkQuery);
        } while ($result->num_rows > 0);
    } else {
        $appointmentId = $_POST['appointmentId'];
    }

    // Your other appointment booking logic here
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Appointment booked successfully. Your Appointment ID is: $appointmentId');
                window.location.href = 'appointment_booking.php'; 
              </script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
}
}

// Edit appointment
$editingCustomerId = '';
if (isset($_GET['edit_id'])) {
    $appointmentId = $_GET['edit_id'];
    $query = "SELECT * FROM appointment WHERE appointmentId = '$appointmentId'";
    $result = $conn->query($query);
    $appointment = $result->fetch_assoc();
    $editingCustomerId = $appointment['customerId'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $appointmentDate = $_POST['appointmentDate'];
        $appointmentTime = $_POST['appointmentTime'];
        $status = $_POST['status'];
        $ServiceType = $_POST['ServiceType'];

        $updateQuery = "UPDATE appointment 
                        SET appointmentDate = '$appointmentDate', appointmentTime = '$appointmentTime', 
                            ServiceType = '$ServiceType', status = '$status' 
                        WHERE appointmentId = '$appointmentId'";

        if ($conn->query($updateQuery) === TRUE) {
            echo "<script>
                alert('Appointment updated successfully.');
                window.location.href = 'appointment_booking.php';
            </script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Delete appointment
if (isset($_GET['delete_id'])) {
    $appointmentId = $_GET['delete_id'];
    $sql = "DELETE FROM appointment WHERE appointmentId = '$appointmentId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Appointment deleted successfully.');
                window.location.href = 'appointment_booking.php';
              </script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
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
</head>
<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title inverse">Appointment Booking</h4>

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
                                        <label>Customer Information</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-lg border" aria-label="Customer Information" name="customerId" required>
                                                <option value="" disabled selected>Select Customer</option>
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

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label>Service Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control form-control-lg border" name="ServiceType" aria-label="Service Type" required>
                                                <option value="" disabled selected>Select Service</option>
                                                <option value="Coating Car">Coating Car</option>
                                                <option value="Car Cleaning">Car Cleaning</option>
                                                <option value="Full Car Service">Full Car Service</option>
                                                <option value="Oil Change">Oil Change</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="appointmentDate">Appointment Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control form-control-lg border" id="appointmentDate" name="appointmentDate" required
                                                placeholder="Select Appointment Date"
                                                <?php echo isset($appointment) ? 'value="' . $appointment['appointmentDate'] . '"' : ''; ?>>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="appointmentTime">Appointment Time</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="appointmentTime" name="appointmentTime" class="form-control border" required
                                                placeholder="Enter Appointment Time (e.g., 10:00 AM)"
                                                <?php echo isset($appointment) ? 'value="' . $appointment['appointmentTime'] . '"' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Appointment Status</label>
                                <select class="form-control border" id="status" name="status" required>
                                    <option value="Pending" <?php echo isset($appointment) && $appointment['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Confirmed" <?php echo isset($appointment) && $appointment['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="Completed" <?php echo isset($appointment) && $appointment['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo isset($appointment) && $appointment['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="sub">
                                <button type="submit" class="btn-lg btn-primary btn-icon-text">
                                    <i class="mdi mdi-file-check btn-icon-prepend"></i>
                                    <?php echo isset($appointment) ? 'Update Appointment' : 'Book Appointment'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            
            <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Appointment Details</h4>
                <div class="table-responsive"><br><br>
                    <table id="appointmentTable" class="table table-striped display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Full Name</th>
                                <th>Car Model</th>
                                <th>Contact No.</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
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
                                        <td class='status-{$row['status']}'>
                                            {$row['status']}
                                        </td>
                                        <td>
                                            <a href='?edit_id={$row['appointmentId']}' class='btn btn-inverse-warning btn-rounded btn-icon'>
                                                <br><i class='mdi mdi-file-check'></i>
                                            </a>
                                            <a href='?delete_id={$row['appointmentId']}' class='btn btn-inverse-danger btn-rounded btn-icon'>
                                                <br><i class='mdi mdi-delete'></i>
                                            </a>
                                        </td>
                                    </tr>";
                                    $srNo++;
                                }
                            } else {
                                echo "<tr><td colspan='8'>No appointments found.</td></tr>";
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

    $(document).ready(function () {
            $('#appointmentTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Appointment Details',
                        text: 'Export to Excel',
                        className: 'btn btn-success'
                    }
                ]
            });
        });


</script>