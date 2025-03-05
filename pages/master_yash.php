<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('conn.php');

$serviceType = '';
$amount = '';
$status = 'active'; // Default status
$editMode = false;
$serviceIdToEdit = null;
$errors = [];

// Function to validate and sanitize input data
function validateAndSanitizeData($conn)
{
    global $serviceType, $amount, $status, $errors;
    $serviceType = trim($_POST['service_name'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $status = $_POST['status'] ?? 'active';


    if (empty($serviceType)) {
        $errors[] = "Service name is required.";
    }

    if (empty($amount)) {
        $errors[] = "Amount is required.";
    } else if (!is_numeric($amount) || $amount < 0) {
        $errors[] = "Amount must be a positive number.";
    }

    if ($status != 'active' && $status != 'inactive') {
        $errors[] = "Invalid status.";
    }

    // Sanitize values
    $serviceType = mysqli_real_escape_string($conn, $serviceType);
    $amount = mysqli_real_escape_string($conn, $amount);
    $status = mysqli_real_escape_string($conn, $status);
}


// Handle form submission for adding/editing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      validateAndSanitizeData($conn);


       if (empty($errors)) {
        if (isset($_POST['edit'])) {
            // Update the record if in edit mode
            $serviceIdToEdit = intval($_POST['serviceId']);
            $sqlUpdate = "UPDATE masterService SET ServiceType = ?, Serviceamount = ?, status = ? WHERE serviceId = ?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("sssi", $serviceType, $amount, $status, $serviceIdToEdit);


            if ($stmt->execute()) {
               echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Service record updated successfully.',
                        timer: 300000, // Optional: auto-close after 5 min
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "'; // Redirect after closing the alert
                    });
                  </script>";
            } else {
                echo "<script>Swal.fire('Error!', 'Failed to update service record. Error: " . $stmt->error . "', 'error');</script>";
            }
            $stmt->close();
        } else {
           // Insert a new record
              $sqlInsert = "INSERT INTO masterService (ServiceType, Serviceamount, status) VALUES (?, ?, ?)";
              $stmt = $conn->prepare($sqlInsert);
               $stmt->bind_param("sss", $serviceType, $amount, $status);
              if ($stmt->execute()) {
                   echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Service record added successfully.',
                        timer: 300000, // Optional: auto-close after 5 min
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "'; // Redirect after closing the alert
                    });
                  </script>";
              } else {
                   echo "<script>Swal.fire('Error!', 'Failed to add service record. Error: " . $stmt->error . "', 'error');</script>";
              }
               $stmt->close();
        }

         // Reset variables and redirect to avoid duplicate submissions
         header("Location: " . $_SERVER['PHP_SELF']);
          exit;


       } else {
           // Display validation errors
            $errorString = implode('<br>', $errors);
            echo "<script>Swal.fire('Validation Error!', '$errorString', 'error');</script>";
       }
}

// Handle edit operation
if (isset($_GET['edit'])) {
    $serviceIdToEdit = intval($_GET['edit']);
    $sqlFetch = "SELECT * FROM masterService WHERE serviceId = ?";
    $stmt = $conn->prepare($sqlFetch);
    $stmt->bind_param("i", $serviceIdToEdit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $serviceType = $row['ServiceType'];
        $amount = $row['Serviceamount']; // Store amount for displaying in input
        $status = $row['status'];
        $editMode = true;
    } else {
        echo "<script>Swal.fire('Error!', 'Service record not found.', 'error');</script>";
    }
    $stmt->close();
}

// Handle soft delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $serviceIdToDelete = intval($_GET['id']);
    $sqlDelete = "UPDATE masterService SET status = 'inactive' WHERE serviceId = ?";
    $stmt = $conn->prepare($sqlDelete);
     $stmt->bind_param("i", $serviceIdToDelete);
    if ($stmt->execute()) {
       echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Service record deleted successfully.',
                        timer: 300000, // Optional: auto-close after 5 min
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "'; // Redirect after closing the alert
                    });
                  </script>";
    } else {
         echo "<script>Swal.fire('Error!', 'Failed to delete service record. Error: " . $stmt->error . "', 'error');</script>";
    }
    $stmt->close();

    // Redirect to avoid duplicate actions
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Services</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("amount").addEventListener("input", function () {
                this.value = this.value.replace(/[^0-9.]/g, ''); // Allow numbers and decimal point
            });

            document.querySelector('.form-sample').addEventListener('submit', function(e) {
                // Prevent the form from submitting immediately
                e.preventDefault();

                // Show the SweetAlert success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Service record submitted successfully.',
                    timer: 300000,  // Auto close after 5 minutes (300000 milliseconds)
                    showConfirmButton: false  // Remove the confirmation button
                }).then(() => {
                    // After the SweetAlert is closed, submit the form
                   document.querySelector('.form-sample').removeEventListener('submit', arguments.callee); // Remove the event listener
                   setTimeout(() => {
                    this.submit();
                }, 100); // Delay the form submission for 0.1 seconds (100 milliseconds)
                });
            });
         });
    </script>
    <style>
        .table th {
            text-align: center !important;
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

        .required {
            color: red;
            font-weight: bold;
            /* Optional for emphasis */
        }

        h2.card-title {
            font-weight: bold;
        }

        .table thead th {
            background-color: #223e9c;
            /* Professional blue background */
            color: #fff;
            /* White text */
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            /* Center-align headers */
            border: 1px solid #ddd;
        }

        /* Table body cells */
        .table tbody td {

            font-size: 14px;
            text-align: center;
            /* Center-align data */
            color: #333;
            /* Dark text for better readability */
            border: 1px solid #ddd;
        }

        /* Zebra striping for rows */
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
            /* Light gray for even rows */
        }

        .table tbody tr:nth-child(odd) {
            background-color: #ffffff;
            /* White for odd rows */
        }

        /* Hover effect for rows */
        .table tbody tr:hover {
            background-color: #f1f1f1;
            /* Slightly darker gray on hover */
            cursor: pointer;
            /* Pointer cursor for interactivity */
        }

        .table-wrapper {
            overflow-x: auto;
        }

        /* Responsive styling for smaller screens */
        @media (max-width: 768px) {
            .table thead {
                display: none;
                /* Hide table headers on smaller screens */
            }

            .table tbody tr {
                display: block;
                /* Stack rows */

            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                text-align: left;
                /* Left-align content for better readability */
                padding: 10px 5px;
            }

            .table tbody td::before {
                content: attr(data-label);
                /* Add labels for mobile view */
                font-weight: bold;
                text-transform: uppercase;
                margin-right: 10px;
                color: #223e9c;
                /* Blue text for labels */
            }
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
        <?php include '../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../partials/_navbar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <h2 class="card-title text-center">Add Service Types</h2>
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-sample" method="POST"  id="form">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="company" class="col-sm-3 col-form-label"> Service <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="service_name" name="service_name"
                                                        class="form-control" placeholder=" Enter Service Name" required
                                                        value="<?php echo htmlspecialchars($serviceType); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">Amount <span
                                                        class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" id="amount" name="amount" class="form-control" 
                                                placeholder="Enter Amount for this service" required 
                                                value="<?php echo htmlspecialchars($amount); ?>">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="status" class="col-sm-3 col-form-label">Status <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <select id="status" name="status" class="form-control" required>
                                                        <option value="active"
                                                            <?php echo ($status === 'active' ? 'selected' : ''); ?>>
                                                            Active</option>
                                                        <option value="inactive"
                                                            <?php echo ($status === 'inactive' ? 'selected' : ''); ?>>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub">
                                         <button type="submit" class="btn-lg btn-primary btn-icon-text">
                                             <i class="mdi mdi-file-check btn-icon-prepend"></i> Submit
                                         </button>
                                         <?php if ($editMode) { ?>
                                            <input type="hidden" name="serviceId" value="<?php echo $serviceIdToEdit; ?>">
                                            <input type="hidden" name="edit" value="true">
                                             <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-lg btn-secondary btn-icon-text">
                                                 <i class="mdi mdi-close btn-icon-prepend"></i> Cancel
                                             </a>
                                        <?php } ?>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-danger">Service Details</h4>
                                <div class="table-responsive">
                                    <div class="table-wrapper">
                                        <style>
                                            .table-wrapper th {
                                                text-align: center;
                                            }
                                        </style>
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr No</th>
                                                    <th>Service</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM masterService WHERE status = 'active' ORDER BY ServiceType";
                                                $result = $conn->query($sql);
                                                $serialNo = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td data-label='Sr No'>" . $serialNo++ . "</td>";
                                                    echo "<td data-label='Service'>" . htmlspecialchars($row['ServiceType']) . "</td>";
                                                    echo "<td data-label='Amount'>" . htmlspecialchars($row['Serviceamount']) . "</td>";
                                                    echo "<td data-label='Status'>" . htmlspecialchars($row['status']) . "</td>";
                                                     echo "<td data-label='Actions' class='action-buttons'>";
                                                    echo "<a href='?edit=" . $row['serviceId'] . "' class='btn btn-inverse-warning btn-rounded btn-icon' title='Edit Record'><br><i class='mdi mdi-border-color'></i></a> ";
                                                    echo "<a href='?action=delete&id=" . $row['serviceId'] . "' class='btn btn-inverse-danger btn-rounded btn-icon' title='Delete Record' onclick=\"return confirm('Are you sure?');\"><br><i class='mdi mdi-delete'></i></a>";
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
    <script src="../js/jquery.cookie.js"></script>
    <script src="../js/hoverable-collapse.js"></script>
    <script src="../js/template.js"></script>
    <script src="../js/dashboard.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        className: 'btn btn-success',
                        title: 'Service List',
                         exportOptions: {
                            columns: function (idx, data, node) {
                                return idx !== 4;
                            }
                        }
                    }
                ],
            });
        });

        document.querySelector('.form-sample').addEventListener('submit', function(e) {
                // Prevent the form from submitting immediately
                e.preventDefault();

                // Show the SweetAlert success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Service record submitted successfully.',
                    timer: 300000,  // Auto close after 5 minutes (300000 milliseconds)
                    showConfirmButton: false  // Remove the confirmation button
                })
                .then(() => {
                    // After the SweetAlert is closed, submit the form
                   document.querySelector('.form-sample').removeEventListener('submit', arguments.callee); // Remove the event listener

                   // Set a timeout to redirect after 5 minutes (300000 ms)
                   setTimeout(() => {
                       this.submit();
                   }, 100);
                });
            });


    </script>
</body>

</html>