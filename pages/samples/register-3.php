<?php
session_start(); // Start session management
include('conn.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $terms_accepted = isset($_POST['terms']) ? 1 : 0;

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }
    if (!$terms_accepted) {
        echo "<script>alert('You must accept the Terms & Conditions!'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $sql = "INSERT INTO users (username, email, password, terms_accepted) VALUES ('$username', '$email', '$hashed_password', '$terms_accepted')";
    if (mysqli_query($conn, $sql)) {
        // Show success message and redirect
        echo "<script>
                alert('Registration successful! Please login.');
                window.location.href = 'login.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Registration</title>
  <!-- base:css -->
  <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="shortcut icon" href="../../images/CarzspaLogo.jpg" />
  <style>
    @media (max-width: 767px) {
      .container-scroller {
        flex-direction: column;
      }
      .auth-form-transparent {
        width: 100%;
        padding: 15px;
      }
      .brand-logo img {
        width: 80px;
        height: auto;
      }
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
              <div class="brand-logo d-flex align-items-center">
                <img src="../../images/CarzspaLogo.jpg" alt="logo" style="width: 100px; height: auto; margin-right: 20px;">
                <div>
                  <h4>New here?</h4>
                  <h6 class="font-weight-light">Join us today! It takes only a few steps</h6>
                </div>
              </div>
              <form class="pt-3" method="POST" action="login.php">
                <div class="form-group">
                  <label>Username</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-account-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="text" name="username" class="form-control form-control-lg border-left-0" placeholder="Username" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-email-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="email" name="email" class="form-control form-control-lg border-left-0" placeholder="Email" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                      <span class="input-group-text bg-transparent border-right-0">
                        <i class="mdi mdi-lock-outline text-primary"></i>
                      </span>
                    </div>
                    <input type="password" name="password" class="form-control form-control-lg border-left-0" placeholder="Password" required>
                  </div>
                </div>
                <div class="mb-4">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" name="terms" class="form-check-input" required>
                      I agree to all Terms & Conditions
                    </label>
                  </div>
                </div>
                <div class="mt-3">
                     <a href="login-2.php" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN UP</a>
                </div>

                <div class="text-center mt-4 font-weight-light">
                  Already have an account? <a href="login-2.php" class="text-primary">Login</a>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6 register-half-bg d-none d-lg-flex flex-row">
            <p class="text-white font-weight-medium text-center flex-grow align-self-end">Copyright &copy; 2021 All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
