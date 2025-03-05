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

    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons/2.3.0/css/buttons.dataTables.min.css">

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons/2.3.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-buttons/2.3.0/js/buttons.html5.min.js"></script>

    <style>
        .container-fluid.page-body-wrapper {
            width: 100% !important;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }

        .selected {
            background-color: #f1f1f1;
        }

        .center-align {
    text-align: center;
}


        @media print {
            body * {
                visibility: hidden;
            }

            .printable-row, .printable-row * {
                visibility: visible;
            }

            .printable-row {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
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
                <h2 class="card-title inverse center-align">Appointment Booking</h2>
                <h4 class="card-description">Appointment Detail</h4>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="table-responsive">
                                    <table class="table table-striped" id="appointment-table">
                                        <?php
                                        include('conn.php');
                                        $sql = "SELECT 
                                                    ef.fullName AS CustomerName, 
                                                    a.ServiceType AS ServiceRequired, 
                                                    qf.amount AS TotalCost 
                                                FROM 
                                                    enquiryForm ef 
                                                JOIN 
                                                    quotationForm qf ON ef.customer_Id = qf.customerId
                                                JOIN 
                                                    appointment a ON qf.customerId = a.customerId";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            echo '<thead>
                                                    <tr>
                                                        <th>Sr. No</th>
                                                        <th>Full Name</th>
                                                        <th>Service Required</th>
                                                        <th>Total Cost</th>
                                                        <th>Action</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>';
                                            $srNo = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr onclick="selectRow(this)">
                                                        <td>' . $srNo++ . '</td>
                                                        <td>' . htmlspecialchars($row['CustomerName']) . '</td>
                                                        <td>' . htmlspecialchars($row['ServiceRequired']) . '</td>
                                                        <td>' . htmlspecialchars(number_format($row['TotalCost'], 2)) . '</td>
                                                        <td class="action-buttons">
                                                            <button type="button" onclick="sendEmail(\'' . $row['CustomerName'] . '\', \'' . $row['ServiceRequired'] . '\', \'' . $row['TotalCost'] . '\')"
                                                            class="btn btn-inverse-danger btn-rounded btn-icon">
                                                                <i class="mdi mdi-email-open"></i>
                                                            </button>
                                                            <button type="button" onclick="printSelectedRow()"
                                                            class="btn btn-inverse-info btn-rounded btn-icon">
                                                                <i class="mdi mdi-printer"></i>
                                                            </button>
                                                        </td>
                                                      </tr>';
                                            }
                                            echo '</tbody>';
                                        } else {
                                            echo "<tr><td colspan='5'>No records found.</td></tr>";
                                        }
                                        $conn->close();
                                        ?>
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

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#appointment-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        title: 'Appointment Details'
                    }
                ]
            });
        });

        let selectedRow = null;

        // Highlight selected row
        function selectRow(row) {
            if (selectedRow !== null) {
                selectedRow.classList.remove("selected");
            }
            selectedRow = row;
            selectedRow.classList.add("selected");
        }

        // Print the selected row
        function printSelectedRow() {
            if (!selectedRow) {
                alert('Please select a row to print.');
                return;
            }
            const printWindow = window.open('', '', 'width=600,height=400');
            const printContent = `
                <table class="table printable-row">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Full Name</th>
                            <th>Service Required</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>${selectedRow.innerHTML}</tbody>
                </table>
            `;
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Send email with appointment details
        function sendEmail(name, service, cost) {
            const subject = "CarzSpa Appointment Details";
            const body = `Hello,\n\nHere are the details of your appointment:\n\nCustomer Name: ${name}\nService: ${service}\nTotal Cost: ${cost}`;
            window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }
    </script>
</body>

</html>
