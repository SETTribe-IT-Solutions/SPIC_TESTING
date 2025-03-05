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
    <title>CarzSpa - Quotation Preparation</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    
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
        
        h2.card-title {
            font-weight: bold;
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

        .dt-head-center {
            text-align: center !important; /* Center-align all table headers */
        }

        /* Styling for the table */
        #appointmentTable {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        /* Styling for the table headers */
        #appointmentTable thead {
            background-color: #223e9c; /* Blue background for header */
            color: white; /* White text for headers */
            font-size: 16px;
            font-weight: bold;
        }

        #appointmentTable th, #appointmentTable td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        /* Styling for the table rows */
        #appointmentTable tbody tr {
            font-size: 14px;
        }

    </style>
</head>

<body>
    <div class="container-scroller d-flex">
        <?php include '../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php
            $basePath = '/Carzspa/';
            include '../partials/_navbar.php';
            ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <h2 class="card-title inverse center-align">Quotation</h2>
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="appointmentTable">
                                        <thead>
                                            <tr>
                                                <th>Sr. No</th>
                                                <th>Full Name</th>
                                                <th>Service Required</th>
                                                <th>Total Cost</th>
                                                <th>Vehicle Number</th>
                                                <th>Action</th> <!-- This is the 5th column -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include('conn.php');
                                            $sql = "SELECT 
                                                        ef.fullName AS CustomerName, 
                                                        a.ServiceType AS ServiceRequired, 
                                                        qf.amount AS TotalCost,
                                                        ef.vehicleNumber AS vehicleNumber
                                                    FROM 
                                                        enquiryForm ef 
                                                    JOIN 
                                                        quotationForm qf ON ef.customer_Id = qf.customerId
                                                    JOIN 
                                                        appointment a ON qf.customerId = a.customerId";
                                            $result = $conn->query($sql);
                                            if ($result->num_rows > 0) {
                                                $srNo = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<tr onclick="selectRow(this)">
                                                            <td>' . $srNo++ . '</td>
                                                            <td>' . htmlspecialchars($row['CustomerName']) . '</td>
                                                            <td>' . htmlspecialchars($row['ServiceRequired']) . '</td>
                                                            <td>' . htmlspecialchars(number_format($row['TotalCost'], 2)) . '</td>
                                                            <td>'. htmlspecialchars($row['vehicleNumber']).'</td>
                                                            <td class="action-buttons">
                                                                <button type="button" data-name="' . $row['CustomerName'] . '" data-service="' . $row['ServiceRequired'] . '" data-cost="' . $row['TotalCost'] . '" class="btn btn-inverse-danger btn-rounded btn-icon" onclick="sendEmail(this)">
                                                                    <i class="mdi mdi-email-open"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-inverse-info btn-rounded btn-icon" onclick="printSelectedRow(this)">
                                                                    <i class="mdi mdi-printer"></i>
                                                                </button>
                                                            </td>
                                                          </tr>';
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No records found.</td></tr>";
                                            }
                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../partials/_footer.html'; ?>
    </div>
    <script>
        $(document).ready(function () {
            $('#appointmentTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Appointment Details',
                        text: 'Export to Excel',
                        className: 'btn btn-light',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (Action)
                        }
                    }
                ],
                initComplete: function () {
                    console.log('Column count:', $('#appointmentTable thead th').length);
                },
                columnDefs: [
                    { targets: 0, className: 'text-center' },
                    { targets: [1, 2], className: 'text-left' },
                    { targets: [3], className: 'text-right' },
                    { targets: '_all', className: 'dt-head-center' }
                ]
            });
        });

        let selectedRow = null;
        let isPrinted = false; // Track if the row has been printed already

        // Highlight selected row
        function selectRow(row) {
            if (selectedRow !== null) {
                selectedRow.classList.remove("selected");
            }
            selectedRow = row;
            selectedRow.classList.add("selected");
        }

        // Print the selected row, excluding the "Action" column
        function printSelectedRow(button) {
            if (isPrinted) {
                alert('You have already printed this row.');
                return;
            }

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
                    <tbody>${selectedRow.innerHTML.replace(/<td class="action-buttons">.*<\/td>/, '')}</tbody>
                </table>
            `;
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();

            // Disable the print button and mark as printed
            button.disabled = true;
            isPrinted = true;

            // Remove the row selection after printing
            selectedRow.classList.remove("selected");
            selectedRow = null;
        }

        function sendEmail(button) {
            const name = button.getAttribute('data-name');
            const service = button.getAttribute('data-service');
            const cost = button.getAttribute('data-cost');

            const subject = "CarzSpa Appointment Details";
            const body = `Hello,\n\nHere are the details of your appointment:\n\nCustomer Name: ${name}\nService: ${service}\nTotal Cost: ${cost}`;
            window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }
    </script>
</body>
</html>
