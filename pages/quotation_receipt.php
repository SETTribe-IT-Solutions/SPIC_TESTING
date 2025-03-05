<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('conn.php'); // Include database connection

if (isset($_GET['quotationId'])) {
    $quotationId = $_GET['quotationId'];

    // Fetch data for the selected quotation
    $sql = "SELECT qf.*, ef.fullName AS customerFullName, ef.contactNumber, ef.company, ap.ServiceType
            FROM quotationForm qf
            JOIN enquiryForm ef ON qf.customerId = ef.customer_Id
            LEFT JOIN appointment ap ON qf.customerId = ap.customerId
            WHERE qf.quotationId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $quotationId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $quotation = $result->fetch_assoc();
    } else {
        echo "<script>alert('Quotation not found!'); window.close();</script>";
        exit();
    }
    $stmt->close();

    // Get the current URL of the page
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Use the current URL as QR data
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($currentUrl);


} else {
    echo "<script>alert('Quotation ID is missing!'); window.close();</script>";
    exit();
}
// Check if the page is being accessed after a QR scan via a specific parameter
$isQrScan = isset($_GET['qr_scan']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 30px;
            /* Removed border from receipt-container */

        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
            position: relative;
            border: 1px solid #ddd; /* Add border to the header */
            padding: 15px;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #6c757d;
        }

        .header h3 {
            font-size: 1rem;
            color: #adb5bd;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #ddd; /* Add border to the table */
        }

        .details-table th,
        .details-table td {
            padding: 15px;
            text-align: left;
        }

        .details-table th {
            background-color: #f8f9fa;
            color: #343a40;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .details-table td {
            border-bottom: 1px solid #dee2e6;
            color: #495057;
        }

        .amount-row {
            font-weight: bold;
            background-color: #f1f3f5;
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
            border: 1px solid #ddd; /* Add border to the button container */
            padding: 15px;
        }

        .btn {
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
        }

        .btn-print {
            background-color: #007bff;
            color: #fff;
             display: inline-flex;
            align-items: center;
            justify-content: center;

        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-cancel:hover {
            background-color: #b02a37;
        }

        .btn-download {
            background-color: #28a745;  /* Green color */
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-download:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .btn-download i {
            margin-right: 8px; /* Spacing between icon and text */
        }

          .btn-print i {
            margin-right: 8px; /* Spacing between icon and text */
        }


        /* Hide the QR code when printing */
        @media print {
            .button-container {
                display: none; /* Hide buttons when printing */
            }
        }

        .qr-code {
            position: absolute; /* Absolutely position the QR code */
            top: 200px;        /* Adjust from the top */
            right: 10px;      /* Adjust from the right */
            width: 120px;      /* Reduce the size */
            height: 100px;     /* Ensure square aspect ratio */
            z-index: 1000;     /* Ensure it's on top of other elements */
            border: 1px solid #ccc; /* Optional border for visibility */
            background-color: white; /* Ensure background is white */
            margin-right: 5%;

        }

        .qr-code img {
            width: 100%;  /* Make image fill its container */
            height: auto;
            display: block;
        }

        /* Media Query for small screens */
        @media (max-width: 768px) {
            .qr-code {
                display: none; /* Hide QR code on screens smaller than 768px wide */
            }
        }


        .qr-header{
            font-size: 0.8rem;
        }

        /* Add this CSS class */
        .pdf-hidden {
            display: none !important;
        }

        /* Ensure QR Code is visible when generating PDF */
        .qr-code.pdf-hidden {
            display: block !important; /* Override any other display: none */
        }

        #qrCode {
            z-index: 2000; /* High z-index to ensure it's on top */
        }


    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
             <?php
                $basePath = '/Carzspa/';
                include '../partials/_navbar.php';
            ?>

            <h1>Quotation Receipt</h1>
            <h2>Quotation ID: <?php echo htmlspecialchars($quotation['quotationId']); ?></h2>
            <h3>Date: <?php echo date("F j, Y", strtotime($quotation['dateTime'])); ?></h3>
             <!-- QR Code Section -->
            <?php if(!$isQrScan): ?>

                <div class="qr-code" id="qrCode">
                    <img src="<?php echo $qrUrl; ?>" alt="QR Code" id="qrImage"/>
                    <a class="qr-header">Scan QR for Receipt</a>
                </div>

            <?php endif; ?>
        </div>
        <br>
        <table class="details-table">
            <tr>
                <th>Customer Name</th>
                <td><?php echo htmlspecialchars($quotation['customerFullName']); ?></td>
            </tr>
            <tr>
                <th>Contact Number</th>
                <td><?php echo htmlspecialchars($quotation['contactNumber']); ?></td>
            </tr>
            <tr>
                <th>Car Company</th>
                <td><?php echo htmlspecialchars($quotation['company']); ?></td>
            </tr>
            <tr>
                <th>Car Model</th>
                <td><?php echo htmlspecialchars($quotation['Carmodel']); ?></td>
            </tr>
            <tr>
                <th>Vehicle Number</th>
                <td><?php echo htmlspecialchars($quotation['Vehiclenumber']); ?></td>
            </tr>
            <tr>
                <th>Services</th>
                <td><?php echo htmlspecialchars($quotation['ServiceType']); ?></td>
            </tr>
            <tr class="amount-row">
                <th>Amount</th>
                <td>₹<?php echo htmlspecialchars(number_format($quotation['amount'], 2)); ?></td>
            </tr>
            <tr>
                <th>Prepared By</th>
                <td><?php echo htmlspecialchars($quotation['preparedBy']); ?></td>
            </tr>
        </table>
        
        <div class="button-container">
    <button class="btn btn-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>
    <button class="btn btn-cancel" onclick="window.location.href='createQuotation.php'">
        Cancel
    </button>
    <button class="btn btn-download" onclick="generatePDF()">
        <i class="fas fa-file-pdf"></i> Download PDF
    </button>
</div>
    </div>

    <script>
        const qrImageElement = document.getElementById('qrImage');
        const qrCodePreloader = new Image();
        qrCodePreloader.onload = function() {
            console.log("QR code preloaded successfully");
        };
        qrCodePreloader.onerror = function() {
            console.error("Error preloading QR code");
        };
        qrCodePreloader.src = qrImageElement.src; // Start preloading


        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const element = document.querySelector('.receipt-container');
            const buttonContainer = document.querySelector('.button-container');
            const qrCodeElement = document.getElementById('qrCode'); // Get the QR code element using its ID.

            if (!element) {
                alert('Could not find the receipt container.');
                return;
            }

            if (!buttonContainer) {
                alert('Could not find the button container.');
                return;
            }

            if (!qrCodeElement) {
               alert('Could not find the QR code element.');
               return;
            }

            // ********************* New Debugging Section *********************
           function checkImageLoaded() {
                if (qrCodePreloader.complete) {
                    console.log("QR Code image preloaded successfully.");
                    if (qrCodePreloader.naturalWidth > 0 && qrCodePreloader.naturalHeight > 0) {
                        console.log("QR Code image has valid dimensions:", qrCodePreloader.naturalWidth, "x", qrCodePreloader.naturalHeight);

                         // Hide the button container temporarily
                        buttonContainer.classList.add('pdf-hidden');

                        //Remove the class before taking screenshot.
                        qrCodeElement.classList.remove('pdf-hidden');

                         // Force a reflow/repaint before capture.  This can sometimes help with rendering issues.
                        qrCodeElement.style.display = 'none';
                        qrCodeElement.offsetHeight; // Trigger a reflow
                        qrCodeElement.style.display = '';

                        setTimeout(() => {
                            html2canvas(element, {
                                scale: 2,
                                useCORS: true // Important if the QR code is hosted on a different domain
                            }).then(canvas => {


                                const imgData = canvas.toDataURL('image/png');
                                const imgWidth = 210;
                                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                                doc.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);

                                // Add the pdf-hidden class again after canvas is created
                                qrCodeElement.classList.add('pdf-hidden');

                                // Remove the pdf-hidden class from button container *after* canvas is created
                                buttonContainer.classList.remove('pdf-hidden');

                                // Use customer's full name for the filename
                                const customerName = "<?php echo htmlspecialchars($quotation['customerFullName']); ?>";
                                const filename = `${customerName}_quotation.pdf`;

                                doc.save(filename);
                            });
                        }, 100); // Adjust delay as needed (milliseconds)

                    } else {
                        console.error("QR Code image has INVALID dimensions or is not fully loaded.");
                        alert("QR Code image has INVALID dimensions or is not fully loaded. Please try again.");
                    }

                } else {
                    console.error("QR Code image NOT preloaded!");
                    alert("QR Code image NOT preloaded! Please try again.");
                }
            }

            checkImageLoaded();

        }
    </script>
</body>
</html>