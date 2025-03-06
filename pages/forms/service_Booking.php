<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarzSpa - Car and Owner Details</title>
  <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/styles.css">
  <link rel="shortcut icon" href="../../images/favicon.png">
  <style>
        .container-fluid.page-body-wrapper {
        width: 100% !important; /* Ensures full width */
        max-width: 100%;        /* Prevents any max width restrictions */
        padding-left: 0;        /* Optional: to remove any left padding */
        padding-right: 0;       /* Optional: to remove any right padding */
        margin-left: 0;         /* Optional: to ensure no left margin */
       margin-right: 0;        /* Optional: to ensure no right margin */
}
    </style>
</head>

<body>
  <div class="container-scroller d-flex">
  <?php include '../../partials/navbar1.html'; ?>
    <div class="container-fluid page-body-wrapper">
    <?php
            $basePath = '/Carzspa/';
            include '../../partials/_navbar.php';
        ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h2 class="card-title inverse">Service Booking</h2>
                <form class="form-sample">
                  <br>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Car_Model" class="col-sm-3 col-form-label">Car Model</label>
                        <div class="col-sm-9">
                          <input type="text" id="Car_Model" name="Car_Model" class="form-control" placeholder="Enter car model">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Service" class="col-sm-3 col-form-label">Service</label>
                        <div class="col-sm-9">
                          <input type="text" id="Service" name="Service" class="form-control" placeholder="Enter service type">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="Amount" class="col-sm-3 col-form-label">Amount</label>
                        <div class="col-sm-9">
                          <input type="text" id="Amount" name="Amount" class="form-control" placeholder="Enter amount">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="sub">
                    <button type="submit" class="btn-lg btn-primary btn-icon-text">
                      <i class="mdi mdi-file-check btn-icon-prepend"></i>
                      Submit</button>
                    </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-danger">Data Filling for Car and Car Owner</h4>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Sr. No</th>
                        <th>Car Model</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Hyundai Verna</td>
                        <td>Car Water Wash</td>
                        <td>300 Rs</td>
                        <td class="action-buttons">
                          <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon" title="Approve"><i class="mdi mdi-file-check"></i></button>                        
                          <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon" title="Delete"><i class="mdi mdi-delete"></i></button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include '../../partials/_footer.html'; ?>
    </div>
  </div>
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <script src="/js/jquery.cookie.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/dashboard.js"></script>
</body>

<style>
  @font-face {
    font-family: FigtreeVF;
    src: url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2 supports variations"),
      url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2-variations");
    font-weight: 100 1000;
    font-display: swap;
  }
</style>

</html>
