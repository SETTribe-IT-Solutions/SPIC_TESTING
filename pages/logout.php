<?php
// logout.php

// Start the session
session_start();

// If the user confirmed the logout
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Destroy all session data
    session_unset(); // Removes all session variables
    session_destroy(); // Destroys the session

    // Redirect to the login page immediately
    header("Location: login.php");
    exit;
}

// If the user canceled the logout
if (isset($_GET['confirm']) && $_GET['confirm'] === 'no' && isset($_GET['redirect'])) {
    // Redirect back to the referring page
    $redirectPage = htmlspecialchars($_GET['redirect']);
    header("Location: $redirectPage");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const currentPage = document.referrer || "index.php"; // Fallback to index.php if no referrer

            // Use SweetAlert for confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to logout with confirmation
                    window.location.href = "logout.php?confirm=yes";
                } else {
                    // Redirect back to the referring page
                    window.location.href = "logout.php?confirm=no&redirect=" + encodeURIComponent(currentPage);
                }
            });
        });
    </script>
</head>
<body>
</body>
</html>

