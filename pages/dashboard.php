<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container-fluid.page-body-wrapper {
            width: 100%;
            padding: 0;
            margin: 0;
        }
       .iframe-container {
            border: none;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            overflow: hidden;
        }
        .iframe-container:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .iframe-container iframe {
             width: 100%;
              border: none;
             display: block;
        }
         .content-wrapper {
             padding: 1rem;
             overflow-x: hidden;
        }
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }
        .dashboard-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
       .full-width-iframe {
            width: 100%;
            height: auto;
            border: none;
            display: block;
        }
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .dashboard-item {
                padding: 1rem;
            }
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
                <div class="dashboard-container">
                    <div class="dashboard-item">
                        <div class="iframe-container">
                            <iframe src="./forms/dashboard_yash1.php" title="Yash Dashboard"></iframe>
                        </div>
                    </div>
                    <div class="dashboard-item">
                        <div class="iframe-container">
                            <iframe src="./dashboard_DM.php" title="Deepesh Dashboard"></iframe>
                        </div>
                    </div>
                    <div class="dashboard-item">
                        <div class="iframe-container">
                        <iframe src="./workerdashboard_DM.php" title="Worker Dashboard" style="height: 550px;"></iframe>
                        </div>
                    </div>
                     <div class="dashboard-item">
                        <div class="iframe-container">
                            <iframe src="./forms/dashboard_mostcar.php" title="Pie Chart Dashboard"></iframe>
                        </div>
                    </div>
                     <div class="dashboard-item">
                        <div class="iframe-container">
                           <iframe src="./forms/dashboard_Chart.php" title="Chart Dashboard"></iframe>
                       </div>
                   </div>
                   <div class="dashboard-item">
                        <div class="iframe-container">                           
                           <iframe src="./forms/dashboard_PieChart2.php" title="Most Car Dashboard"></iframe>
                       </div>
                   </div>
                </div>
                 <iframe class="full-width-iframe" src="./forms/Dashbord_Yash.php" title="Most Car Dashboard"></iframe>
            </div>
            <?php include '../partials/_footer.html'; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('iframe').forEach(iframe => {
            iframe.onload = function() {
                iframe.style.height = iframe.contentWindow.document.documentElement.scrollHeight + 'px';
            };
        });
    });
</script>
</body>
</html>