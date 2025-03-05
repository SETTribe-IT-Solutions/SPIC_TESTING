<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include('conn.php');

$name = '';
$phoneno = '';
$status = 'active'; // Default status
$editMode = false;
$idToEdit = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phoneno = mysqli_real_escape_string($conn, $_POST['phoneno']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (isset($_GET['edit'])) {
        // Update the record if in edit mode
        $idToEdit = intval($_GET['edit']);
        $sqlUpdate = "UPDATE workers SET name = '$name', phoneno = '$phoneno', status = '$status' WHERE id = $idToEdit";
        if ($conn->query($sqlUpdate)) {
            echo "<script>Swal.fire('Success!', 'Worker record updated successfully.', 'success');</script>";
        } else {
            echo "<script>Swal.fire('Error!', 'Failed to update worker record.', 'error');</script>";
        }
    } else {
        // Insert a new record
        $sqlInsert = "INSERT INTO workers (name, phoneno, status) VALUES ('$name', '$phoneno', '$status')";
        if ($conn->query($sqlInsert)) {
            echo "<script>Swal.fire('Success!', 'Worker record added successfully.', 'success');</script>";
        } else {
            echo "<script>Swal.fire('Error!', 'Failed to add worker record.', 'error');</script>";
        }
    }

    // Reset variables and redirect to avoid duplicate submissions
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit operation
if (isset($_GET['edit'])) {
    $idToEdit = intval($_GET['edit']);
    $sqlFetch = "SELECT * FROM workers WHERE id = $idToEdit";
    $result = $conn->query($sqlFetch);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $phoneno = $row['phoneno'];
        $status = $row['status'];
        $editMode = true;
    } else {
        echo "<script>Swal.fire('Error!', 'Worker record not found.', 'error');</script>";
    }
}

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $idToDelete = intval($_GET['id']);
    $sqlDelete = "UPDATE workers SET status = 'inactive' WHERE id = $idToDelete";
    if ($conn->query($sqlDelete)) {
        echo "<script>Swal.fire('Success!', 'Worker record deleted successfully.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error!', 'Failed to delete worker record.', 'error');</script>";
    }

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
    <title>Add Workers</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/Carzspa.jpg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        .navbar-fixed-top .main-panel {
            padding-top: 0px !important;
        }

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
                    <h2 class="card-title text-center">Add Workers</h2>
                    <br>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <form class="form-sample" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-3 col-form-label">Worker Name<span
                                                        class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="name" name="name" class="form-control"
                                                        placeholder="Enter worker name" required
                                                        value="<?php echo htmlspecialchars($name); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="phoneno" class="col-sm-3 col-form-label">Phone Number <span
                                                        class="required">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="tel" id="phoneno" name="phoneno" class="form-control"
                                                        placeholder="Enter phone number" required
                                                        value="<?php echo htmlspecialchars($phoneno); ?>"
                                                        pattern="[0-9]{10}"
                                                        title="Please enter a 10-digit phone number.">
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
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-danger">Worker List</h4>
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
                                                    <th>Worker Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = 'SELECT * FROM workers WHERE status = "active" ORDER BY name';
                                                $result = $conn->query($sql);
                                                $serialNo = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td data-label='Sr.No.'>" . $serialNo++ . "</td>";
                                                    echo "<td data-label='Name'>" . htmlspecialchars($row['name']) . "</td>";
                                                    echo "<td data-label='Phone Number'>" . htmlspecialchars($row['phoneno']) . "</td>";
                                                    echo "<td data-label='Status'>" . htmlspecialchars($row['status']) . "</td>";
                                                    echo "<td data-label='Actions' class='action-buttons'>";
                                                    echo "<a href='?edit=" . $row['id'] . "' class='btn btn-inverse-warning btn-rounded btn-icon' title='Edit Record'><br><i class='mdi mdi-border-color'></i></a> ";
                                                    echo "<a href='?action=delete&id=" . $row['id'] . "' class='btn btn-inverse-danger btn-rounded btn-icon' title='Delete Record' onclick=\"return confirm('Are you sure?');\"><br><i class='mdi mdi-delete'></i></a>";
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
                        title: 'Worker List',
                        className: 'btn btn-success',
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
</body>

</html>