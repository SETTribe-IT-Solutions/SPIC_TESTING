<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
    <style>
        .banner-container {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            text-align: center;
            border-bottom: 4px solid rgb(52, 91, 219);
            border-left: 1px solid white;
            position: relative;
        }

        .banner-header {
            background-color: #223e9c;
            color: white;
            font-size: 1 rem;
            font-weight: bold;
            padding: 10px 0;
            border: 2px solid white ;
        }

        .banner-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .banner-content img {
            max-width: 100px;
            width: auto;
        }

        .banner-content h1 {
            font-size: 2.5rem;
            font-family: Abril Fatface;
            font-weight: bold;
            margin: 0;
            color: #001d6e;
        }

        .banner-content p {
            font-size: 1.5rem;
            padding-left: 20%;
            font-style: italic;
            color: #0056b3;
            margin: 0;
        }

        .banner-left,
        .banner-right {
            flex: 0;
            display: flex;
            justify-content: center;
            /* Align image horizontally */
            align-items: center;
            /* Align image vertically */
        }

        .banner-center {
            flex: 2;
        }
        .navbar-toggler span {
            font-size: 18px;
        }

        @media (max-width: 768px) {
    .banner-header {
      font-size: 25px; /* Smaller font size for medium screens */
    }
  }

  @media (max-width: 480px) {
    .banner-header {
      font-size: 14px; /* Even smaller font size for small screens */
    }
  }
  @media (max-width: 400px) {
    .banner-container img {
      max-width: 50px; /* Adjust image size for medium screens */
    }
  }

  @media (max-width: 400px) {
    .banner-container img {
      max-width: 30px; /* Further shrink for smaller screens */
    }
  }


  @media (max-width: 768px) {
    .banner-center h1 {
      font-size: 1rem; /* Cap the size for medium screens */
     
    }}
@media (max-width: 768px){
.banner-center p {
    font-size: 0.5rem;
  }}
    </style>
</head>

<body>
    <div class="container-fluid page-body-wrapper">
        <!-- partial:./partials/_navbar.html -->

        <div class="banner-container">
            <div class="banner-header">
                For everything your car needs.
            </div>
            <div class="navbar-brand-wrapper">
                <div class="banner-content">
                    <div class="banner-left">
                        <a class="navbar-brand brand-logo" href="#">
                            <img src="CarzspaLogo.jpg" alt="CarzSpa Logo">
                        </a>
                    </div>
                    <div class="banner-center">
                        <h1>CarzSpa</h1>
                        <p>...Coating Service Center</p>
                    </div>
                    <div class="banner-right">
                        <a class="navbar-brand brand-logo-mini" href="#">
                        <img src="Carzspa.jpg" alt="CarzSpa Logo">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- base:js -->
      <script src="vendors/js/vendor.bundle.base.js"></script>
      <!-- endinject -->
      <!-- Plugin js for this page-->
      <script src="vendors/chart.js/Chart.min.js"></script>
      <script src="js/jquery.cookie.js" type="text/javascript"></script>
      <!-- End plugin js for this page-->
      <!-- inject:js -->
      <script src="js/off-canvas.js"></script>
      <script src="js/hoverable-collapse.js"></script>
      <script src="js/template.js"></script>
      <!-- endinject -->
      <!-- plugin js for this page -->
      <script src="js/jquery.cookie.js" type="text/javascript"></script>
      <!-- End plugin js for this page -->
      <!-- Custom js for this page-->
      <script src="js/dashboard.js"></script>
      <!-- End custom js for this page-->

</body>
</html>