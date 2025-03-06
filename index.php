<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ./pages/login.php");
    exit();

    //Saurabh has edited this file
}
if (isset($_SESSION['username'])) {
  header("Location: ./pages/dashboard.php");
  exit();
}
?>
