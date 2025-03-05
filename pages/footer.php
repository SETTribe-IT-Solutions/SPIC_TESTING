<!DOCTYPE html>
<html lang="en">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet">

  <style>
    .developer-info {
      font-size: 1rem;
      font-weight: 500;
      color: #ffffff;
      margin: 0.5rem 0;
    }

    .developer-info span {
      color: #ffffff;
    }

    .developer-info span:first-child {
      color: #face0b;
    }

    .btn-rounded {
      width: 30px;
      height: 20px;
      font-size: 12px;
    }

    .btn-social-icon-text {
      font-size: 15px;
      padding: 4px 8px;
    }
  </style>
</head>
<body>
  <!-- Footer Section 1 -->
  <footer class="footer" style="background-color: #223e9c; font-size: 90%; padding: 8px 0; border: 2px solid white;">
    <div class="card" style="background-color: transparent; border: none;">
      <div class="card-body">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-center">
          <!-- Left Section -->
          <div class="mb-2 mb-sm-0">
            <button type="button" class="btn btn-warning btn-rounded btn-icon rounded-circle">
              <i class="mdi mdi-map-marker-radius"></i>
            </button>
           <button type="button" class="btn btn-light btn-rounded btn-icon rounded-circle" onclick="window.location.href='dashboard.php';">
  <i class="mdi mdi-home"></i>
</button>
          </div>
          <!-- Center Section -->
          <div>
            <a href="#" class="developer-info" style="color: white; text-decoration: none; cursor: default; font-size: 14px;">
              This site is owned by <span>CarzSpa</span><br>
              Developed & Maintained by <span>SETTribe</span>
            </a>
          </div>
          <!-- Right Section -->
          <div class="mt-2 mt-sm-0">
            <button type="button" class="btn btn-social-icon-text btn-facebook"><i class="mdi mdi-facebook"></i>Facebook</button>
            <button type="button" class="btn btn-social-icon-text btn-twitter"><i class="mdi mdi-twitter"></i>Twitter</button>
            <button type="button" class="btn btn-social-icon-text btn-dribbble"><i class="mdi mdi-instagram"></i>Instagram</button>
          </div>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>