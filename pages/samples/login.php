<?php
session_start(); // Start session management
include('conn.php'); // Include database connection

$alertMessage = ''; // For storing the alert message
$redirectUrl = ''; // For storing the redirect URL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($username) || empty($password)) {
        $alertMessage = "Swal.fire({
            title: 'Error!',
            text: 'Both fields are required!',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.back();
        });";
    } else {
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                if ($row['terms_accepted'] == 1) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];

                    $alertMessage = "Swal.fire({
                        title: 'Login Successful!',
                        text: 'Welcome, {$row['username']}!',
                        icon: 'success',
                        confirmButtonText: 'Go to Dashboard',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });";
                } else {
                    $alertMessage = "Swal.fire({
                        title: 'Terms Not Accepted!',
                        text: 'You must accept the terms and conditions to log in.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f4ad42'
                    }).then(() => {
                        window.history.back();
                    });";
                }
            } else {
                $alertMessage = "Swal.fire({
                    title: 'Invalid Password!',
                    text: 'Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.history.back();
                });";
            }
        } else {
            $alertMessage = "Swal.fire({
                title: 'User Not Found!',
                text: 'No user found with this username or email!',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.history.back();
            });";
        }
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
              <h4>Login Page</h4>
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
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent border-left-0">
                                        <i class="mdi mdi-eye-off-outline text-primary" id="togglePassword" style="cursor: pointer;"></i>
                                        </span>
                                    </div>
                        </div>
                </div>

                <div class="my-3">
                <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" href="../forms/dataFilling.php">LOGIN</a>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6 login-half-bg d-none d-lg-flex flex-row">
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('exampleInputPassword');

  togglePassword.addEventListener('click', function () {
    // Toggle the password field type
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;

    // Toggle the eye icon
    this.classList.toggle('mdi-eye-outline');
    this.classList.toggle('mdi-eye-off-outline');
  });
</script>
<!-- <script>
  document.getElementById('loginButton').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent default button action
   // Get username and password field values
   const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    // Check if username or password is missing
    if (!username || !password) {
      Swal.fire({
        title: 'Error!',
        text: 'Both username and password are required!',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33',
        allowOutsideClick: false
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect back to the login page (or reload the current page)
          window.location.href = 'login.php'; // Replace 'login.php' with the correct login page URL
        }
      });
    } else {
      // If both fields are filled, show success alert
      Swal.fire({
        title: 'Login Successful!',
        text: 'Welcome to your dashboard.',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6',
        allowOutsideClick: false
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect to dashboard after confirmation
          window.location.href = '../forms/dataFilling.php';
        }
      });
    }
  });
</script> -->
<script>
        <?php if (!empty($alertMessage)) { echo $alertMessage; } ?>
</script>
</body>

</html>