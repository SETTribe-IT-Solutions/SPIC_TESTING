<?php
// Start session
session_start();
include('conn.php');

$showAlert = false;
$alertType = '';
$alertTitle = '';
$alertText = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($pass === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header('Location: dashboard.php');
            exit();
        } else {
            $showAlert = true;
            $alertType = 'error';
            $alertTitle = 'Invalid Credentials';
            $alertText = 'Please try with valid credentials.';
        }
    } else {
        $showAlert = true;
        $alertType = 'error';
        $alertTitle = 'Invalid Credentials';
        $alertText = 'Please try with valid credentials.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Carzspa Admin</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="shortcut icon" href="../images/CarzspaLogo.jpg" />
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
        h1 {
            text-align: center;
        }
        .auth-form-transparent {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 60px;
            width: 500px;
            max-width: 90%;
            margin: auto;
        }
        .input-group {
            position: relative;
            width: 100%;
        }
        .input-group input {
            padding-right: 40px; /* Space for eye icon */
        }
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
            z-index: 10; /* Ensures it's above input */
            color: #666;
            transition: color 0.3s ease-in-out;
        }
        .eye-icon:hover {
            color: #007bff;
        }
        @media (max-width: 768px) {
            .auth-form-transparent {
                width: 90%;
                padding: 30px;
            }
        }
        @media (max-width: 576px) {
            .auth-form-transparent {
                width: 95%;
                padding: 20px;
            }
            .brand-logo img {
                width: 70px;
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
                            <div class="brand-logo">
                                <h1><img src="../images/CarzspaLogo.jpg" alt="logo"></h1>
                            </div>
                            <h4>Login Page</h4>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Username" required>
                                </div>
                                <div class="form-group input-group">
                                    <label for="password">Password</label><br>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" required>
                                        <span class="mdi mdi-eye eye-icon" id="togglePassword"></span>
                                    </div>
                                </div>
                                <div class="my-3">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">LOGIN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5 login-half-bg d-none d-lg-flex flex-row"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="../vendors/js/vendor.bundle.base.js"></script>
    <script src="../js/jquery.cookie.js" type="text/javascript"></script>
    <script src="../js/off-canvas.js"></script>
    <script src="../js/hoverable-collapse.js"></script>
    <script src="../js/template.js"></script>
    <script>
       const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent any form submission related action
            const type = passwordField.type === 'password' ? 'text' : 'password';
             passwordField.type = type;
            this.classList.toggle('mdi-eye');
             this.classList.toggle('mdi-eye-off');
        });
        
    </script>
    <?php if ($showAlert): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $alertType; ?>',
                title: '<?php echo $alertTitle; ?>',
                text: '<?php echo $alertText; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location = "login.php";
            });
        </script>
    <?php endif; ?>
</body>
</html>