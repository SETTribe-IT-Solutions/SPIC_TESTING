<?php
session_start(); // Start session management
include('conn.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('Both fields are required!'); window.history.back();</script>";
        exit;
    }

    // Query to check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Check if terms are accepted
            if ($row['terms_accepted'] == 1) {
                // Store user information in the session
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Redirect to the dashboard or home page
                echo "<script>
                        alert('Login successful! Welcome, {$row['username']}');
                        window.location.href = 'dashboard.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('You must accept the terms and conditions to log in.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid password! Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No user found with this username or email!'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Carzspa Admin</title>
  <!-- base:css -->
  <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="shortcut icon" href="../../images/CarzspaLogo.jpg" />
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../../css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../../images/Carzspa.jpg" />
  <style>
    /* CSS for Carzspa Admin Login Page */
body {
  font-family: 'Roboto', sans-serif;
  background-color: #f4f5f7;
  margin: 0;
  padding: 0;
  color: #333;
}

h4 {
  color: #2d2d2d;
  text-align: center;
  font-weight: bold;
}

h1{
  text-align: center;
}

.auth-form-transparent {
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  padding: 60px;
  width: 500px;
  max-width: 90%; /* Ensure responsiveness */
  margin: auto; /* Center the form */
}

.btn-primary {
  display:block;
  background-color: #007bff;
  border: none;
  transition: background-color 0.3s;
}

  </style>
</head>
<body>
  <div class="container-scroller d-flex">
    <div class="container-fluid page-body-wrapper full-page-wrapper d-flex">
      <div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
        <div class="row flex-grow">
          <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="auth-form-transparent text-left p-3">
              <div class="brand-logo">
                <h1><img src="../../images/CarzspaLogo.jpg" alt="logo"></h1>
              </div>
              <h4>Welcome back..!!</h4>
              <h6 class="font-weight-light"></h6>
              <form class="pt-3">
                <div class="form-group">
                  <label for="exampleInputEmail">Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-account-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control form-control-lg border-left-0" id="exampleInputEmail" placeholder="Username">
                  </div>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword">Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="password" class="form-control form-control-lg border-left-0" id="exampleInputPassword" placeholder="Password">                        
                  </div>
                </div>
                <div class="my-3">
                  <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" href="../forms/dataFilling.php">LOGIN</a>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register-3.html" class="text-primary">Create</a>
                </div>
              </form>
            </div>
          </div>
         
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="../../vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <script src="../../js/jquery.cookie.js" type="text/javascript"></script>
  <!-- inject:js -->
  <script src="../../js/off-canvas.js"></script>
  <script src="../../js/hoverable-collapse.js"></script>
  <script src="../../js/template.js"></script>
  <!-- endinject -->
</body>

</html>