<?php
include('conn.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $carModel = $_POST['license'];
    $vehicleNumber = $_POST['model'];
    $previousHistory = $_POST['history'];
    $fullName = $_POST['Full_Name'];
    $mobileNumber = $_POST['Mobile_Number'];
    $email = $_POST['Email'];
    $address = $_POST['Address'];

    // Auto-generate customer_Id
    $result = $conn->query("SELECT MAX(id) AS max_id FROM enquiryForm");
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $nextId = $row['max_id'] + 1;
    $customerId = 'CUST' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

    // Insert data into the database
    $sql = "INSERT INTO enquiryForm (customer_Id, carModel, vehicleNumber, previousHistory, fullName, contactNumber, email, address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $customerId, $carModel, $vehicleNumber, $previousHistory, $fullName, $mobileNumber, $email, $address);

    if ($stmt->execute()) {
        echo "<script>alert('Data inserted successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Retrieve data from the database for display
$result = $conn->query("SELECT * FROM enquiryForm");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Data Filling for Car and Car Owner</title>
    <link rel="stylesheet" href="template/css/style.css">
    <link rel="stylesheet" href="template/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="shortcut icon" href="../../images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <script>
        // Form validation function
        function validateForm() {
            const fullName = document.getElementById("Full_Name").value.trim();
            const mobileNumber = document.getElementById("Mobile_Number").value.trim();
            const email = document.getElementById("Email").value.trim();
            const address = document.getElementById("Address").value.trim();
            const vehicleNumber = document.getElementById("model").value.trim();

            const nameRegex = /^[a-zA-Z\s]+$/; // Only letters and spaces
            const mobileRegex = /^[6-9]\d{9}$/; // Starts with 6-9 and is 10 digits
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation
            const vehicleRegex = /^[A-Z]{2}\s\d{2}\s[A-Z]{1,2}\s\d{4}$/; // Format: MH 12 AB 1234

            // Validate Full Name
            if (!nameRegex.test(fullName)) {
                alert("Please enter a valid full name (letters and spaces only).");
                return false;
            }

            // Validate Mobile Number
            if (!mobileRegex.test(mobileNumber)) {
                alert("Please enter a valid 10-digit mobile number starting with 6-9.");
                return false;
            }

            // Validate Email
            if (email && !emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Validate Address
            if (address.length < 5) {
                alert("Please enter a valid address with at least 5 characters.");
                return false;
            }

            // Validate Vehicle Number
            if (!vehicleRegex.test(vehicleNumber)) {
                alert("Invalid vehicle number! Please enter in the format 'MH 12 AB 1234'.");
                return false;
            }

            return true;
        }

        // Function to confirm delete action
        function confirmDelete(customerId) {
            if (confirm("Are you sure you want to delete this record?")) {
                const deleteUrl = `deleteRecord.php?customerId=${customerId}`;
                window.location.href = deleteUrl;
            }
        }

        function redirectToEditPage(customerId) {
            const editUrl = `editPage.php?customerId=${customerId}`;
            window.location.href = editUrl;
        }
    </script>
</head>

<body>
    <div class="container-scroller d-flex">
        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title inverse">Data Filling for Car and Car Owner</h2>
                                <form class="form-sample" method="POST" onsubmit="return validateForm()">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="license" class="col-sm-3 col-form-label">Car Make Model</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="license" name="license" class="form-control" placeholder="Enter Your Car Model" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="model" class="col-sm-3 col-form-label">Vehicle Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="model" name="model" class="form-control" placeholder="Eg. MH 12 AB 1234" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="history" class="col-sm-3 col-form-label">Previous Service History</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="history" name="history" class="form-control" placeholder="Enter Previous Service History">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Full_Name" class="col-sm-3 col-form-label">Full Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Full_Name" name="Full_Name" class="form-control" placeholder="Enter Your Full Name" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Mobile_Number" class="col-sm-3 col-form-label">Mobile Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Mobile_Number" name="Mobile_Number" class="form-control" placeholder="Enter Your Mobile Number" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Email" class="col-sm-3 col-form-label">Email</label>
                                                <div class="col-sm-9">
                                                    <input type="email" id="Email" name="Email" class="form-control" placeholder="Enter Your Email Address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Address" class="col-sm-3 col-form-label">Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Address" name="Address" class="form-control" placeholder="Enter Your Address">
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

                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-danger">Data Filling for Car and Car Owner</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Customer ID</th>
                                                <th>Car Model</th>
                                                <th>Vehicle Number</th>
                                                <th>History</th>
                                                <th>Full Name</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result && $result->num_rows > 0) {
                                                $srNo = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$srNo}</td>
                                                        <td>{$row['customer_Id']}</td>
                                                        <td>{$row['carModel']}</td>
                                                        <td>{$row['vehicleNumber']}</td>
                                                        <td>{$row['previousHistory']}</td>
                                                        <td>{$row['fullName']}</td>
                                                        <td>{$row['contactNumber']}</td>
                                                        <td>{$row['email']}</td>
                                                        <td>{$row['address']}</td>

                                                        <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon">
                                                        <i class="mdi mdi-file-check"></i>
                                                    </button>

                                                        <td>
                                                            <button onclick=\"redirectToEditPage('{$row['customer_Id']}')\" class="btn btn-inverse-warning btn-rounded btn-icon">Edit</button>
                                                            <button onclick=\"confirmDelete('{$row['customer_Id']}')\" class=\"btn btn-danger btn-sm\">Delete</button>
                                                        </td>
                                                    </tr>";
                                                    $srNo++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
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
    </div>
</body>

</html>
