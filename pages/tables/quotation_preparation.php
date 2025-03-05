<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Quotation Preparation</title>
    <link rel="stylesheet" href="../../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../css/style.css">
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

                                <h2 class="card-title inverse">Appointment Booking</h2>
                                <h4 class="card-description">Appointment Detail</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="trbold">
                                                <th>Sr. No</th>
                                                <th>Full Name</th>
                                                <th>Service Required</th>
                                                <th>Total Cost</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Syed Adil</td>
                                                <td>coating type</td>
                                                <td>10,000</td>
                                                <td class="action-buttons">
                                                    <button type="button"
                                                        class="btn btn-inverse-danger btn-rounded btn-icon"><i
                                                            class="mdi mdi-email-open"></i></button>
                                                    <button type="button"
                                                        class="btn btn-inverse-info btn-rounded btn-icon"><i
                                                            class="mdi mdi-printer"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Aweas Khan</td>
                                                <td>car cleaning</td>
                                                <td>5,000</td>
                                                <td class="action-buttons">
                                                    <button type="button"
                                                        class="btn btn-inverse-danger btn-rounded btn-icon"><i
                                                            class="mdi mdi-email-open"></i></button>
                                                    <button type="button"
                                                        class="btn btn-inverse-info btn-rounded btn-icon"><i
                                                            class="mdi mdi-printer"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Ajay Patil</td>
                                                <td>Vacuuming</td>
                                                <td>2,999</td>
                                                <td class="action-buttons">
                                                    <button type="button"
                                                        class="btn btn-inverse-danger btn-rounded btn-icon"><i
                                                            class="mdi mdi-email-open"></i></button>
                                                    <button type="button"
                                                        class="btn btn-inverse-info btn-rounded btn-icon"><i
                                                            class="mdi mdi-printer"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Zamir Shaikh</td>
                                                <td>Intensive Interior cleaning</td>
                                                <td>10,000</td>
                                                <td class="action-buttons">
                                                    <button type="button"
                                                        class="btn btn-inverse-danger btn-rounded btn-icon"><i
                                                            class="mdi mdi-email-open"></i></button>
                                                    <button type="button"
                                                        class="btn btn-inverse-info btn-rounded btn-icon"><i
                                                            class="mdi mdi-printer"></i></button>
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