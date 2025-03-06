<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('conn.php');

$company = '';
$carModel = '';
$editMode = false;
$idToEdit = null;

// Function to display SweetAlert messages
function showAlert($title, $text, $icon) {
    echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: '$title',
                text: '$text',
                icon: '$icon'
            });
        }, 100);
    </script>";
}
    // Handle edit operation
    if (isset($_GET['edit'])) {
        $editMode = true;
        $idToEdit = intval($_GET['edit']);
    
        $sqlSelect = "SELECT company, carModel FROM addCars WHERE id = ?";
        if ($stmt = $conn->prepare($sqlSelect)) {
            $stmt->bind_param("i", $idToEdit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $company = htmlspecialchars($row['company']);
                $carModel = htmlspecialchars($row['carModel']);
            } else {
                 showAlert('Error!', 'Record not found.', 'error');
                $editMode = false;
                $idToEdit = null;
            }
            $stmt->close();
        } else {
            showAlert('Error!', 'Database query failed.', 'error');
             $editMode = false;
            $idToEdit = null;
        }
    }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $carModel = mysqli_real_escape_string($conn, $_POST['carModel']);

    if ($editMode && $idToEdit) {
        // Update record using prepared statement
        $sqlUpdate = "UPDATE addCars SET company = ?, carModel = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sqlUpdate)) {
            $stmt->bind_param("ssi", $company, $carModel, $idToEdit);
            if ($stmt->execute()) {
                showAlert('Success!', 'Record updated successfully.', 'success');
                echo "<script>setTimeout(function(){ window.location.href = 'addCars.php'; }, 500);</script>";
                $company = '';
                $carModel = '';
            } else {
                showAlert('Error!', 'Failed to update record.', 'error');
            }
            $stmt->close();
        } else {
             showAlert('Error!', 'Database query failed.', 'error');
        }
    } else {
        // Insert a new record using prepared statement
        $sqlInsert = "INSERT INTO addCars (company, carModel, status) VALUES (?, ?, 'active')";
        if ($stmt = $conn->prepare($sqlInsert)) {
            $stmt->bind_param("ss", $company, $carModel);
            if ($stmt->execute()) {
                showAlert('Success!', 'Record added successfully.', 'success');
                echo "<script>setTimeout(function(){ window.location.href = 'addCars.php'; }, 500);</script>";
                $company = '';
                $carModel = '';
            } else {
                showAlert('Error!', 'Failed to add record.', 'error');
            }
            $stmt->close();
        } else {
             showAlert('Error!', 'Database query failed.', 'error');
        }
    }
    $editMode = false;
}

// Handle update service operation
if (isset($_POST['updateService'])) {
    $serviceId = intval($_POST['serviceId']);
    $serviceType = htmlspecialchars($_POST['ServiceType']);
    $amount = intval($_POST['amount']);
    $status = $_POST['status'];

    // Update SQL query using prepared statements
    $sqlUpdate = "UPDATE masterService SET ServiceType = ?, amount = ?, status = ? WHERE serviceId = ?";
    if ($stmt = $conn->prepare($sqlUpdate)) {
        $stmt->bind_param("sisi", $serviceType, $amount, $status, $serviceId);
        if ($stmt->execute()) {
            showAlert('Success!', 'Record updated successfully.', 'success');
              echo "<script>setTimeout(function(){ window.location.href = 'addCars.php'; }, 500);</script>";
        } else {
            showAlert('Error!', 'Failed to update record.', 'error');
        }
        $stmt->close();
    } else {
         showAlert('Error!', 'Database query failed.', 'error');
    }
}

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $idToDelete = intval($_GET['id']);
    $sqlDelete = "UPDATE addCars SET status = 'inactive' WHERE id = ?";
    if ($stmt = $conn->prepare($sqlDelete)) {
         $stmt->bind_param("i", $idToDelete);
         if ($stmt->execute()) {
              showAlert('Success!', 'Record deleted successfully.', 'success');
             echo "<script>setTimeout(function(){ window.location.href = 'addCars.php'; }, 500);</script>";
         } else {
            showAlert('Error!', 'Failed to delete record.', 'error');
         }
          $stmt->close();
    } else {
         showAlert('Error!', 'Database query failed.', 'error');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cars</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
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
            font-weight: bold; /* Optional for emphasis */
        }

        h2.card-title {
            font-weight: bold;
        }
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
        .table-wrapper {
            overflow-x: auto;
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
                <h2 class="card-title text-center">ADD CARS </h2>
                <br>
                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <form class="form-sample" method="POST">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="company" class="col-sm-3 col-form-label">Company <span class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <input 
                                                    type="text" 
                                                    id="company" 
                                                    name="company" 
                                                    class="form-control" 
                                                    placeholder="Enter Car Company" 
                                                    value="<?php echo $company; ?>" 
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="carModel" class="col-sm-3 col-form-label">Car Model <span class="required">*</span></label>
                                            <div class="col-sm-9">
                                                <input 
                                                    type="text" 
                                                    id="carModel" 
                                                    name="carModel" 
                                                    class="form-control" 
                                                    placeholder="Enter Car Model" 
                                                    value="<?php echo $carModel; ?>" 
                                                    required>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="sub">
                                    <button type="submit" class="btn-lg btn-primary btn-icon-text">
                                        <i class="mdi mdi-file-check btn-icon-prepend"></i> <?php echo $editMode ? 'Update' : 'Submit'; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-danger">Car List</h4>
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
                                                <th>Company</th>
                                                <th>Car Model</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = 'SELECT * FROM addCars WHERE status = "active" ORDER BY company, carModel';
                                            $result = $conn->query($sql);
                                            $serialNo = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $serialNo++ . "</td>";
                                                echo "<td>" . htmlspecialchars($row['company']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['carModel']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                echo "<td class='action-buttons'>";
                                                echo "<a href='?edit=" . $row['id'] . "' class='btn btn-inverse-warning btn-rounded btn-icon' title='Edit Record'><br><i class='mdi mdi-border-color'></i></a> ";
                                                echo "<a href='#' class='btn btn-inverse-danger btn-rounded btn-icon' title='Delete Record' 
                                                        onclick='confirmDelete(" . $row['id'] . ")'>
                                                        <br><i class='mdi mdi-delete'></i>
                                                        </a>";
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
                    title: 'Car List',
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return idx !== 4;
                        }
                    }
                }
            ],
        });
    });
</script>
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
            window.location.href = "?action=delete&id=" + id;
        }
    });
}
</script>
</body>
</html>