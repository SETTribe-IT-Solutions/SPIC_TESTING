<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Data Filling for Car and Car Owner</title>
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
                                <h2 class="card-title inverse">Data Filling for Car and Car Owner</h2>
                                <form class="form-sample">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="license" class="col-sm-3 col-form-label">Car Make
                                                    Model</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="license" name="license" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="model" class="col-sm-3 col-form-label">Vehicle
                                                    Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="model" name="model" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="model" class="col-sm-3 col-form-label">Previous Service
                                                    History</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="model" name="model" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Full_Name" class="col-sm-3 col-form-label">Full Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Full_Name" name="Full_Name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Mobile_Number" class="col-sm-3 col-form-label">Mobile
                                                    Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Mobile_Number" name="Mobile_Number"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Email" class="col-sm-3 col-form-label">Email</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Email" name="Email" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="Address" class="col-sm-3 col-form-label">Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="Address" name="Address" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="preferred" class="col-sm-3 col-form-label">Preferred
                                                    Communication Method</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="preferred" name="preferred"
                                                        class="form-control">
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
                                                <!-- <th>License Plate Number</th> -->
                                                <th>Previous History</th>
                                                <th>Full Name</th>
                                                <th>Contact No</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Preferred</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Swift Dzire</td>
                                                <td>MH-29-AB-3032</td>
                                                <!-- <td>MH-29-AB-3032</td> -->
                                                <td>PDF</td>
                                                <td>Syed Akil</td>
                                                <td>9878037865</td>
                                                <td>aakil87@gmail.com</td>
                                                <td>Islampura</td>
                                                <td>NA</td>
                                                <td class="action-buttons">
                                                    <button type="button"
                                                        class="btn btn-inverse-warning btn-rounded btn-icon"><i
                                                            class="mdi mdi-file-check"></i></button>
                                                    <button type="button"
                                                        class="btn btn-inverse-danger btn-rounded btn-icon"><i
                                                            class="mdi mdi-delete"></i></button>
                                                </td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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