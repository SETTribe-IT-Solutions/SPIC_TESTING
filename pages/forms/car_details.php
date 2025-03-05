

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
  <!-- <header>
        <img src="logo.png" alt="CarzSpa Logo">
        <h1>CarzSpa - Coating Service Center</h1>
    </header> -->
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
                <h2 class="card-title inverse">Details of Car and Owner</h2>
                <form class="form-sample">
                  <p class="card-description">Details of Car</p>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="company" class="col-sm-3 col-form-label">Select Company</label>
                        <div class="col-sm-9">
                          <select id="company" name="company" class="form-control">
                            <option value="">Select Company</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="model" class="col-sm-3 col-form-label">Select Car Model</label>
                        <div class="col-sm-9">
                          <select id="model" name="model" class="form-control">
                            <option value="">Select Model</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="year" class="col-sm-3 col-form-label">Manufacture Year</label>
                        <div class="col-sm-9">
                          <select id="year" name="year" class="form-control">
                            <option value="">Select Year</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="vin" class="col-sm-3 col-form-label">Vehicle Identification Number (VIN)</label>
                        <div class="col-sm-9">
                          <input type="text" id="vin" name="vin" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="license" class="col-sm-3 col-form-label">License Plate Number</label>
                        <div class="col-sm-9">
                          <input type="text" id="license" name="license" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="history" class="col-sm-3 col-form-label">Previous Service History (Optional)</label>
                        <div class="col-sm-9">
                          <select id="history" name="history" class="form-control">
                            <option value="">Select</option>
                            <option value="">Yes</option>
                            <option value="">No</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <p class="card-description">Details of Owner</p>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="owner" class="col-sm-3 col-form-label">Owner Name</label>
                        <div class="col-sm-9">
                          <input type="text" id="owner" name="owner" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="mobile" class="col-sm-3 col-form-label">Mobile Number</label>
                        <div class="col-sm-9">
                          <input type="text" id="mobile" name="mobile" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="address" class="col-sm-3 col-form-label">Address (Optional)</label>
                        <div class="col-sm-9">
                          <input type="text" id="address" name="address" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email Address (Optional)</label>
                        <div class="col-sm-9">
                          <input type="email" id="email" name="email" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="preferred" class="col-sm-3 col-form-label">Preferred Communication Method</label>
                        <div class="col-sm-9">
                          <input type="text" id="preferred" name="preferred" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <!-- <button type="button" class="btn-lg btn-outline-primary btn-icon-text">
                <i class="mdi mdi-reload btn-icon-prepend"></i>                                                    
                Reset</button> -->
              <div class="sub">
              <button type="submit" class="btn-lg btn-primary btn-icon-text">
                <i class="mdi mdi-file-check btn-icon-prepend"></i>
                Submit</button>
              </div>
              </form>
            </div>
          </div>
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title text-danger">Details Of Cars and Owner</h4>
                <!-- <p class="card-description">
                  Add class <code>.table-striped</code>
                </p> -->
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Sr. No</th>
                        <th>Car Model</th>
                        <th>Vehicle Number</th>
                        <th>License Plate Number</th>
                        <th>Previous History</th>
                        <th>Full Name</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Preferred</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Swift Dzire</td>
                        <td>MH-29-AB-3032</td>
                        <td>MH-29-AB-3032</td>
                        <td>PDF</td>
                        <td>Syed Akil</td>
                        <td>9878767858</td>
                        <td>aakil87@gmail.com</td>
                        <td>Mirzapura</td>
                        <td>NA</td>
                        <td class="action-buttons">
                          <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon"><i class="mdi mdi-file-check"></i></button>                        
                          <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon"><i class="mdi mdi-delete"></i></button>
                        </td>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>Swift Dzire</td>
                        <td>MH-29-AB-3032</td>
                        <td>MH-29-AB-3032</td>
                        <td>PDF</td>
                        <td>Syed Akil</td>
                        <td>9878767858</td>
                        <td>aakil87@gmail.com</td>
                        <td>Mirzapura</td>
                        <td>NA</td>
                        <td class="action-buttons">
                          <button type="button" class="btn btn-inverse-warning btn-rounded btn-icon"><i class="mdi mdi-file-check"></i></button>                        
                          <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon"><i class="mdi mdi-delete"></i></button>
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
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <script src="js/dashboard.js"></script>
</body>
<style>
  @font-face {
    font-family: FigtreeVF;
    src: url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2 supports variations"), url(chrome-extension://panammoooggmlehahpcjckcncfeffcoi/fonts/FigtreeVF.woff2) format("woff2-variations");
    font-weight: 100 1000;
    font-display: swap
  }
</style>
</html>