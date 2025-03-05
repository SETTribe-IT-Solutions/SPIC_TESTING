<?php
include ('conn.php');
?>
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



    
 

<!-- Include jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>





</head>
<style>
        .container-fluid.page-body-wrapper {
        width: 100% !important; /* Ensures full width */
        max-width: 100%;        /* Prevents any max width restrictions */
        padding-left: 0;        /* Optional: to remove any left padding */
        padding-right: 0;       /* Optional: to remove any right padding */
        margin-left: 0;         /* Optional: to ensure no left margin */
       margin-right: 0;        /* Optional: to ensure no right margin */
}
/* Styling for the car owner table */
#carOwnerTable {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
}

/* Styling for the table headers */
#carOwnerTable thead {
    background-color: #223e9c; /* Blue background for header */
    color: white; /* White text for headers */
    font-size: 16px;
    font-weight: bold;
}

#carOwnerTable th, #carOwnerTable td {
    padding: 12px 15px;
    text-align: center;
    border: 1px solid #ddd;
}

/* Styling for the table rows */
#carOwnerTable tbody tr {
    font-size: 14px;
}



    </style>

<div class="container-scroller d-flex">
    <?php include '../../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
        <?php
            $basePath = '/Carzspa/';
            include '../../partials/_navbar.php';
        ?>
           <?php
?>


<?php


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id'])) {
    // Ensure all form fields are present
    if (isset($_POST['companyName'], $_POST['carModel'], $_POST['manufactureYear'], $_POST['licensePlateNumber'], 
              $_POST['ownerName'], $_POST['mobileNumber'], $_POST['address'], $_POST['email'])) {

        // Sanitize form data
        $companyName = $_POST['companyName'];
        $carModel = $_POST['carModel'];
        $manufactureYear = $_POST['manufactureYear'];
        $licensePlateNumber = $_POST['licensePlateNumber'];
        $ownerName = $_POST['ownerName'];
        $mobileNumber = $_POST['mobileNumber'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $status = 'Active'; // Set default status to Active

        // Validate mobile number (example validation for 10-digit numbers starting with 6, 7, or 9)
        if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
            echo "<script>alert('Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, or 9.');</script>";
            exit;
        }

        // Get current datetime
        $datetime = date("Y-m-d H:i:s");

        // Insert query
        $insertQuery = "INSERT INTO detailsOfCarsAndOwner (companyName, carModel, manufactureYear, licensePlateNumber, 
                        ownerName, mobileNumber, address, email, status, datetime) 
                        VALUES ('$companyName', '$carModel', '$manufactureYear', '$licensePlateNumber', 
                                '$ownerName', '$mobileNumber', '$address', '$email', '$status', '$datetime')";

        // Execute the query
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>alert('Record inserted successfully.');
                    window.location.href = 'cardetails_yash.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

// Fetch car owner details if editing
$carOwnerDetails = [];
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $query = "SELECT * FROM detailsOfCarsAndOwner WHERE id = '$id'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $carOwnerDetails = $result->fetch_assoc(); // Fetch the data for editing
    } else {
        echo "<script>alert('Record not found.');</script>";
        exit;
    }
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $companyName = $_POST['companyName'];
    $carModel = $_POST['carModel'];
    $manufactureYear = $_POST['manufactureYear'];
    $licensePlateNumber = $_POST['licensePlateNumber'];
    $ownerName = $_POST['ownerName'];
    $mobileNumber = $_POST['mobileNumber'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $status = 'Active'; // Status remains active as default

    // Validate mobile number (should be a 10-digit number starting with 6, 7, or 9)
    if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
        echo "<script>alert('Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, or 9.');</script>";
        exit;
    }

    // Update query
    $updateQuery = "UPDATE detailsOfCarsAndOwner 
                    SET companyName = '$companyName', 
                        carModel = '$carModel', 
                        manufactureYear = '$manufactureYear', 
                        licensePlateNumber = '$licensePlateNumber', 
                        ownerName = '$ownerName', 
                        mobileNumber = '$mobileNumber', 
                        address = '$address', 
                        email = '$email', 
                        status = '$status' 
                    WHERE id = '$id'";

    // Execute the update query
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Record updated successfully.');
                window.location.href = 'cardetails_yash.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Ensure the ID is an integer to avoid SQL injection
    $id = (int) $id;

    // Delete query
    $deleteQuery = "DELETE FROM detailsOfCarsAndOwner WHERE id = $id";

    if ($conn->query($deleteQuery) === TRUE) {
        // Redirect after success
        header("Location: cardetails_yash.php?delete_success=true");
        exit;
    } else {
        // Handle error
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>









<body>
    
<div class="main-panel">
    <div class="content-wrapper">
        <h2 class="card-title text-center">Details of Car and Owner</h2>
        <br>
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                <form class="form-sample" method="POST">
                        <p class="card-description">Details of Car</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="companyName" class="col-sm-3 col-form-label">Select Company</label>
                                    <div class="col-sm-9">
                                        <select id="companyName" name="companyName" class="form-control" required>
                                            <option value="">Select Company</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="carModel" class="col-sm-3 col-form-label">Select Car Model</label>
                                    <div class="col-sm-9">
                                        <select id="carModel" name="carModel" class="form-control" required>
                                            <option value="">Select Model</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="manufactureYear" class="col-sm-3 col-form-label">Manufacture Year</label>
                                    <div class="col-sm-9">
                                        <select id="manufactureYear" name="manufactureYear" class="form-control" required>
                                            <option value="">Select Year</option>
                                         
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="licensePlateNumber" class="col-sm-3 col-form-label">License Plate Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="licensePlateNumber" name="licensePlateNumber" class="form-control" value="<?php echo $carOwnerDetails['licensePlateNumber']; ?>" pattern="[A-Za-z0-9]{1,10}" title="License Plate Number must be 1-10 alphanumeric characters." required>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="previousHistory" class="col-sm-3 col-form-label">Previous Service History (Optional)</label>
                                    <div class="col-sm-9">
                                        <select id="previousHistory" name="previousHistory" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Yes" <?php echo ($carOwnerDetails['previousHistory'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                            <option value="No" <?php echo ($carOwnerDetails['previousHistory'] == 'No') ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <p class="card-description">Details of Owner</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="ownerName" class="col-sm-3 col-form-label">Owner Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="ownerName" name="ownerName" class="form-control" value="<?php echo $carOwnerDetails['ownerName']; ?>" pattern="[A-Za-z\s]{3,}" title="Owner Name must be at least 3 alphabetic characters." required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="mobileNumber" class="col-sm-3 col-form-label">Mobile Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="mobileNumber" name="mobileNumber" class="form-control" value="<?php echo $carOwnerDetails['mobileNumber']; ?>" pattern="[6-9][0-9]{9}" title="Mobile Number must start with 6, 7, 8, or 9 and be 10 digits long." required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="address" class="col-sm-3 col-form-label">Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="address" name="address" class="form-control" value="<?php echo $carOwnerDetails['address']; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="email" class="col-sm-3 col-form-label">Email Address (Optional)</label>
                                    <div class="col-sm-9">
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $carOwnerDetails['email']; ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Enter a valid email address.">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sub">
                            <button type="submit" class="btn-lg btn-primary btn-icon-text">
                                <i class="mdi mdi-file-check btn-icon-prepend"></i>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
 
            <div class="col-12 grid-margin">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title text-danger">Details Of Cars and Owner</h4>
      <div class="table-responsive"><br><br>
      <table id="carOwnerTable" class="table table-striped display nowrap">
    <thead>
        <tr>
            <th>Sr. No</th>
            <th>Car Model</th>
            <th>Vehicle Number</th>
            <th>License Plate Number</th>
            <th>Previous History</th>
            <th>Full Name</th>
            <th>Contact No.</th>
            <th>Email</th>
            <th>Address</th>
            <th>Company</th>
            <th>Manufacture Year</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Assuming you have a connection to the database and have fetched the data
        include('conn.php');
        
        // Fetch car owner details from the database
        $query = "SELECT * FROM detailsOfCarsAndOwner ORDER BY id ASC";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $srNo = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$srNo}</td>
                    <td>{$row['carModel']}</td>
                    <td>{$row['licensePlateNumber']}</td>
                    <td>{$row['licensePlateNumber']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['ownerName']}</td>
                    <td>{$row['mobileNumber']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['address']}</td>
                    <td>{$row['companyName']}</td>
                    <td>{$row['manufactureYear']}</td>
                    <td>
                        <a href='?edit_id={$row['id']}' class='btn btn-inverse-warning btn-rounded btn-icon'>
                           <br> <i class='mdi mdi-border-color'></i>
                        </a>
                        <a href='#' onclick='confirmDelete({$row['id']})' class='btn btn-inverse-danger btn-rounded btn-icon'>
                           <br> <i class='mdi mdi-delete'></i>
                        </a>
                    </td>
                </tr>";
                $srNo++;
            }
        } else {
            echo "<tr><td colspan='12' class='text-center'>No car owner records found.</td></tr>";
        }

        // Close the connection
        $conn->close();
        ?>
    </tbody>
</table>

            </div>
            </div>
        </div>
        </div><?php include '../../partials/_footer.html'; ?>
      </div>
      
    </div>
  </div>
  </body>
  <!-- DataTables Buttons CSS (only for export functionality) -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">



<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons JS (for export functionality) -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>

<!-- JSZip (required for Excel export functionality) -->
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Export to Excel functionality -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

  <script>




window.onload = function() {
    // Car data
   const carData = [
  {"brand": "Seat", "models": ["Alhambra", "Altea", "Altea XL", "Arosa", "Cordoba", "Cordoba Vario", "Exeo", "Ibiza", "Ibiza ST", "Exeo ST", "Leon", "Leon ST", "Inca", "Mii", "Toledo"]},
  {"brand": "Renault", "models": ["Captur", "Clio", "Clio Grandtour", "Espace", "Express", "Fluence", "Grand Espace", "Grand Modus", "Grand Scenic", "Kadjar", "Kangoo", "Kangoo Express", "Koleos", "Laguna", "Laguna Grandtour", "Latitude", "Mascott", "Mégane", "Mégane CC", "Mégane Combi", "Mégane Grandtour", "Mégane Coupé", "Mégane Scénic", "Scénic", "Talisman", "Talisman Grandtour", "Thalia", "Twingo", "Wind", "Zoé"]},
  {"brand": "Peugeot", "models": ["1007", "107", "106", "108", "2008", "205", "205 Cabrio", "206", "206 CC", "206 SW", "207", "207 CC", "207 SW", "306", "307", "307 CC", "307 SW", "308", "308 CC", "308 SW", "309", "4007", "4008", "405", "406", "407", "407 SW", "5008", "508", "508 SW", "605", "806", "607", "807", "Bipper", "RCZ"]},
  {"brand": "Dacia", "models": ["Dokker", "Duster", "Lodgy", "Logan", "Logan MCV", "Logan Van", "Sandero", "Solenza"]},
  {"brand": "Citroën", "models": ["Berlingo", "C-Crosser", "C-Elissée", "C-Zero", "C1", "C2", "C3", "C3 Picasso", "C4", "C4 Aircross", "C4 Cactus", "C4 Coupé", "C4 Grand Picasso", "C4 Sedan", "C5", "C5 Break", "C5 Tourer", "C6", "C8", "DS3", "DS4", "DS5", "Evasion", "Jumper", "Jumpy", "Saxo", "Nemo", "Xantia", "Xsara"]},
  {"brand": "Opel", "models": ["Agila", "Ampera", "Antara", "Astra", "Astra cabrio", "Astra caravan", "Astra coupé", "Calibra", "Campo", "Cascada", "Corsa", "Frontera", "Insignia", "Insignia kombi", "Kadett", "Meriva", "Mokka", "Movano", "Omega", "Signum", "Vectra", "Vectra Caravan", "Vivaro", "Vivaro Kombi", "Zafira"]},
  {"brand": "Alfa Romeo", "models": ["145", "146", "147", "155", "156", "156 Sportwagon", "159", "159 Sportwagon", "164", "166", "4C", "Brera", "GTV", "MiTo", "Crosswagon", "Spider", "GT", "Giulietta", "Giulia"]},
  {"brand": "Škoda", "models": ["Favorit", "Felicia", "Citigo", "Fabia", "Fabia Combi", "Fabia Sedan", "Felicia Combi", "Octavia", "Octavia Combi", "Roomster", "Yeti", "Rapid", "Rapid Spaceback", "Superb", "Superb Combi"]},
  {"brand": "Chevrolet", "models": ["Alero", "Aveo", "Camaro", "Captiva", "Corvette", "Cruze", "Cruze SW", "Epica", "Equinox", "Evanda", "HHR", "Kalos", "Lacetti", "Lacetti SW", "Lumina", "Malibu", "Matiz", "Monte Carlo", "Nubira", "Orlando", "Spark", "Suburban", "Tacuma", "Tahoe", "Trax"]},
  {"brand": "Porsche", "models": ["911 Carrera", "911 Carrera Cabrio", "911 Targa", "911 Turbo", "924", "944", "997", "Boxster", "Cayenne", "Cayman", "Macan", "Panamera"]},
  {"brand": "Honda", "models": ["Accord", "Accord Coupé", "Accord Tourer", "City", "Civic", "Civic Aerodeck", "Civic Coupé", "Civic Tourer", "Civic Type R", "CR-V", "CR-X", "CR-Z", "FR-V", "HR-V", "Insight", "Integra", "Jazz", "Legend", "Prelude"]},
  {"brand": "Subaru", "models": ["BRZ", "Forester", "Impreza", "Impreza Wagon", "Justy", "Legacy", "Legacy Wagon", "Legacy Outback", "Levorg", "Outback", "SVX", "Tribeca", "Tribeca B9", "XV"]},
  {"brand": "Mazda", "models": ["121", "2", "3", "323", "323 Combi", "323 Coupé", "323 F", "5", "6", "6 Combi", "626", "626 Combi", "B-Fighter", "B2500", "BT", "CX-3", "CX-5", "CX-7", "CX-9", "Demio", "MPV", "MX-3", "MX-5", "MX-6", "Premacy", "RX-7", "RX-8", "Xedox 6"]},
  {"brand": "Mitsubishi", "models": ["3000 GT", "ASX", "Carisma", "Colt", "Colt CC", "Eclipse", "Fuso canter", "Galant", "Galant Combi", "Grandis", "L200", "L200 Pick up", "L200 Pick up Allrad", "L300", "Lancer", "Lancer Combi", "Lancer Evo", "Lancer Sportback", "Outlander", "Pajero", "Pajeto Pinin", "Pajero Pinin Wagon", "Pajero Sport", "Pajero Wagon", "Space Star"]},
  {"brand": "Lexus", "models": ["CT", "GS", "GS 300", "GX", "IS", "IS 200", "IS 250 C", "IS-F", "LS", "LX", "NX", "RC F", "RX", "RX 300", "RX 400h", "RX 450h", "SC 430"]},
  {"brand": "Toyota", "models": ["4-Runner", "Auris", "Avensis", "Avensis Combi", "Avensis Van Verso", "Aygo", "Camry", "Carina", "Celica", "Corolla", "Corolla Combi", "Corolla sedan", "Corolla Verso", "FJ Cruiser", "GT86", "Hiace", "Hiace Van", "Highlander", "Hilux", "Land Cruiser", "MR2", "Paseo", "Picnic", "Prius", "RAV4", "Sequoia", "Starlet", "Supra", "Tundra", "Urban Cruiser", "Verso", "Yaris", "Yaris Verso"]},
  {"brand": "BMW", "models": ["i3", "i8", "M3", "M4", "M5", "M6", "Rad 1", "Rad 1 Cabrio", "Rad 1 Coupé", "Rad 2", "Rad 2 Active Tourer", "Rad 2 Coupé", "Rad 2 Gran Tourer", "Rad 3", "Rad 3 Cabrio", "Rad 3 Compact", "Rad 3 Coupé", "Rad 3 GT", "Rad 3 Touring", "Rad 4", "Rad 4 Cabrio", "Rad 4 Gran Coupé", "Rad 5", "Rad 5 GT", "Rad 5 Touring", "Rad 6", "Rad 6 Cabrio", "Rad 6 Coupé", "Rad 6 Gran Coupé", "Rad 7", "Rad 8 Coupé", "X1", "X3", "X4", "X5", "X6", "Z3", "Z3 Coupé", "Z3 Roadster", "Z4", "Z4 Roadster"]},
  {"brand": "Volkswagen", "models": ["Amarok", "Beetle", "Bora", "Bora Variant", "Caddy", "Caddy Van", "Life", "California", "Caravelle", "CC", "Crafter", "Crafter Van", "Crafter Kombi", "CrossTouran", "Eos", "Fox", "Golf", "Golf Cabrio", "Golf Plus", "Golf Sportvan", "Golf Variant", "Jetta", "LT", "Lupo", "Multivan", "New Beetle", "New Beetle Cabrio", "Passat", "Passat Alltrack", "Passat CC", "Passat Variant", "Passat Variant Van", "Phaeton", "Polo", "Polo Van", "Polo Variant", "Scirocco", "Sharan", "T4", "T4 Caravelle", "T4 Multivan", "T5", "T5 Caravelle", "T5 Multivan", "T5 Transporter Shuttle", "Tiguan", "Touareg", "Touran"]},
  {"brand": "Suzuki", "models": ["Alto", "Baleno", "Baleno kombi", "Celerio", "Grand Vitara", "Ignis", "Jimny", "Kizashi", "Liana", "Splash", "Swift", "SX4", "Vitara", "Wagon R", "Wagon R+", "Wagon R Van"]},
  {"brand": "Fiat", "models": ["500", "500 Cabrio", "500 L", "500X", "Panda", "Punto", "Qubo", "Tipo", "Doblo", "Freemont", "Linea", "Punto Evo", "Punto Van", "Sedici", "Stilo", "Panda 4x4"]},
  {"brand": "Ford", "models": ["B-Max", "C-Max", "C-Max Grand", "Ecosport", "Edge", "Escape", "Fiesta", "Fiesta Van", "Fiesta Active", "Focus", "Focus Cabriolet", "Focus CC", "Focus Estate", "Focus SW", "Galaxy", "Ka", "Ka+", "Kuga", "Maverick", "Mondeo", "Mondeo Van", "Mustang", "S-Max", "Tourneo Connect", "Tourneo Custom", "Transit", "Transit Custom", "Transit Connect", "Transit Courier", "Transit Jumbo"]},
  {"brand": "Audi", "models": ["A1", "A1 Sportback", "A2", "A3", "A3 Cabrio", "A3 Sedan", "A3 Sportback", "A4", "A4 Avant", "A4 Allroad", "A5", "A5 Cabrio", "A5 Sportback", "A6", "A6 Avant", "A6 Allroad", "A7", "A7 Sportback", "A8", "A8 L", "A8 Avant", "Q2", "Q3", "Q4", "Q5", "Q5 Sportback", "Q7", "Q8", "Q8 e-tron", "R8", "RS 3", "RS 4", "RS 5", "RS 7", "RS Q3", "RS Q5", "RS Q7", "S3", "S4", "S5", "S6", "S7", "S8", "TT", "TT Roadster", "TTRS", "Q5 PHEV", "Q7 PHEV", "Q8 PHEV"]},
  {"brand": "Mercedes-Benz", "models": ["A-Class", "B-Class", "C-Class", "CLA", "CL", "CLC", "CLK", "CLS", "E-Class", "G-Class", "GL-Class", "GLA", "GLB", "GLC", "GLC Coupé", "GLS", "GLE", "GLE Coupé", "S-Class", "SLC", "SL", "SLK", "SLS", "V-Class", "Vito", "X-Class"]},
  {"brand": "Jaguar", "models": ["E-PACE", "F-PACE", "F-Type", "I-PACE", "XE", "XF", "XJ", "XK", "XK8"]},
  {"brand": "Land Rover", "models": ["Defender", "Discovery", "Discovery Sport", "Range Rover", "Range Rover Evoque", "Range Rover Velar", "Range Rover Sport"]},
  {"brand": "Volvo", "models": ["240", "740", "850", "940", "960", "C30", "C70", "S40", "S60", "S70", "S80", "S90", "V40", "V50", "V60", "V70", "V90", "XC40", "XC60", "XC70", "XC90"]},
  {"brand": "Tesla", "models": ["Model 3", "Model S", "Model X", "Model Y"]},
  {"brand": "Mini", "models": ["Clubman", "Countryman", "Hatch", "Paceman", "Roadster"]},
  {"brand": "Fiat", "models": ["500", "500 Cabrio", "500 L", "500X", "Panda", "Punto", "Qubo", "Tipo", "Doblo", "Freemont", "Linea", "Punto Evo", "Punto Van", "Sedici", "Stilo", "Panda 4x4"]},
  {"brand": "Hyundai", "models": ["i10", "i20", "i30", "i40", "iX35", "Kona", "Nexo", "Palisade", "Santa Fe", "Tucson", "Veloster", "Sonata", "Tucson N"]},
  {"brand": "Jaguar", "models": ["F-Pace", "I-Pace", "XE", "XF", "XJ", "F-Type"]}
];



    // Function to populate companies
    function populateCompanies() {
      const companySelect = document.getElementById("companyName");
      carData.forEach(brand => {
        const option = document.createElement("option");
        option.value = brand.brand;
        option.textContent = brand.brand;
        companySelect.appendChild(option);
      });
    }

    $(document).ready(function() {
    // Dummy car data structure
      const carData = [
  {brand: "Seat", models: ["Alhambra", "Altea", "Altea XL", "Arosa", "Cordoba", "Cordoba Vario", "Exeo", "Ibiza", "Ibiza ST", "Exeo ST", "Leon", "Leon ST", "Inca", "Mii", "Toledo"]},
  {brand: "Renault", models: ["Captur", "Clio", "Clio Grandtour", "Espace", "Express", "Fluence", "Grand Espace", "Grand Modus", "Grand Scenic", "Kadjar", "Kangoo", "Kangoo Express", "Koleos", "Laguna", "Laguna Grandtour", "Latitude", "Mascott", "Mégane", "Mégane CC", "Mégane Combi", "Mégane Grandtour", "Mégane Coupé", "Mégane Scénic", "Scénic", "Talisman", "Talisman Grandtour", "Thalia", "Twingo", "Wind", "Zoé"]},
  {brand: "Peugeot", models: ["1007", "107", "106", "108", "2008", "205", "205 Cabrio", "206", "206 CC", "206 SW", "207", "207 CC", "207 SW", "306", "307", "307 CC", "307 SW", "308", "308 CC", "308 SW", "309", "4007", "4008", "405", "406", "407", "407 SW", "5008", "508", "508 SW", "605", "806", "607", "807", "Bipper", "RCZ"]},
  {brand: "Dacia", models: ["Dokker", "Duster", "Lodgy", "Logan", "Logan MCV", "Logan Van", "Sandero", "Solenza"]},
  {brand: "Citroën", models: ["Berlingo", "C-Crosser", "C-Elissée", "C-Zero", "C1", "C2", "C3", "C3 Picasso", "C4", "C4 Aircross", "C4 Cactus", "C4 Coupé", "C4 Grand Picasso", "C4 Sedan", "C5", "C5 Break", "C5 Tourer", "C6", "C8", "DS3", "DS4", "DS5", "Evasion", "Jumper", "Jumpy", "Saxo", "Nemo", "Xantia", "Xsara"]},
  {brand: "Opel", models: ["Agila", "Ampera", "Antara", "Astra", "Astra cabrio", "Astra caravan", "Astra coupé", "Calibra", "Campo", "Cascada", "Corsa", "Frontera", "Insignia", "Insignia kombi", "Kadett", "Meriva", "Mokka", "Movano", "Omega", "Signum", "Vectra", "Vectra Caravan", "Vivaro", "Vivaro Kombi", "Zafira"]},
  {brand: "Alfa Romeo", models: ["145", "146", "147", "155", "156", "156 Sportwagon", "159", "159 Sportwagon", "164", "166", "4C", "Brera", "GTV", "MiTo", "Crosswagon", "Spider", "GT", "Giulietta", "Giulia"]},
  {brand: "Škoda", models: ["Favorit", "Felicia", "Citigo", "Fabia", "Fabia Combi", "Fabia Sedan", "Felicia Combi", "Octavia", "Octavia Combi", "Roomster", "Yeti", "Rapid", "Rapid Spaceback", "Superb", "Superb Combi"]},
  {brand: "Chevrolet", models: ["Alero", "Aveo", "Camaro", "Captiva", "Corvette", "Cruze", "Cruze SW", "Epica", "Equinox", "Evanda", "HHR", "Kalos", "Lacetti", "Lacetti SW", "Lumina", "Malibu", "Matiz", "Monte Carlo", "Nubira", "Orlando", "Spark", "Suburban", "Tacuma", "Tahoe", "Trax"]},
  {brand: "Porsche", models: ["911 Carrera", "911 Carrera Cabrio", "911 Targa", "911 Turbo", "924", "944", "997", "Boxster", "Cayenne", "Cayman", "Macan", "Panamera"]},
  {brand: "Honda", models: ["Accord", "Accord Coupé", "Accord Tourer", "City", "Civic", "Civic Aerodeck", "Civic Coupé", "Civic Tourer", "Civic Type R", "CR-V", "CR-X", "CR-Z", "FR-V", "HR-V", "Insight", "Integra", "Jazz", "Legend", "Prelude"]},
  {brand: "Subaru", models: ["BRZ", "Forester", "Impreza", "Impreza Wagon", "Justy", "Legacy", "Legacy Wagon", "Legacy Outback", "Levorg", "Outback", "SVX", "Tribeca", "Tribeca B9", "XV"]},
  {brand: "Mazda", models: ["121", "2", "3", "323", "323 Combi", "323 Coupé", "323 F", "5", "6", "6 Combi", "626", "626 Combi", "B-Fighter", "B2500", "BT", "CX-3", "CX-5", "CX-7", "CX-9", "Demio", "MPV", "MX-3", "MX-5", "MX-6", "Premacy", "RX-7", "RX-8", "Xedox 6"]},
  {brand: "Mitsubishi", models: ["3000 GT", "ASX", "Carisma", "Colt", "Colt CC", "Eclipse", "Fuso canter", "Galant", "Galant Combi", "Grandis", "L200", "L200 Pick up", "L200 Pick up Allrad", "L300", "Lancer", "Lancer Combi", "Lancer Evo", "Lancer Sportback", "Outlander", "Pajero", "Pajeto Pinin", "Pajero Pinin Wagon", "Pajero Sport", "Pajero Wagon", "Space Star"]},
  {brand: "Lexus", models: ["CT", "GS", "GS 300", "GX", "IS", "IS 200", "IS 250 C", "IS-F", "LS", "LX", "NX", "RC F", "RX", "RX 300", "RX 400h", "RX 450h", "SC 430"]},
  {brand: "Toyota", models: ["4-Runner", "Auris", "Avensis", "Avensis Combi", "Avensis Van Verso", "Aygo", "Camry", "Carina", "Celica", "Corolla", "Corolla Combi", "Corolla sedan", "Corolla Verso", "FJ Cruiser", "GT86", "Hiace", "Hiace Van", "Highlander", "Hilux", "Land Cruiser", "MR2", "Paseo", "Picnic", "Prius", "RAV4", "Sequoia", "Starlet", "Supra", "Tundra", "Urban Cruiser", "Verso", "Yaris", "Yaris Verso"]},
  {brand: "BMW", models: ["i3", "i8", "M3", "M4", "M5", "M6", "Rad 1", "Rad 1 Cabrio", "Rad 1 Coupé", "Rad 2", "Rad 2 Active Tourer", "Rad 2 Coupé", "Rad 2 Gran Tourer", "Rad 3", "Rad 3 Cabrio", "Rad 3 Compact", "Rad 3 Coupé", "Rad 3 GT", "Rad 3 Touring", "Rad 4", "Rad 4 Cabrio", "Rad 4 Gran Coupé", "Rad 5", "Rad 5 GT", "Rad 5 Touring", "Rad 6", "Rad 6 Cabrio", "Rad 6 Coupé", "Rad 6 Gran Coupé", "Rad 7", "Rad 8 Coupé", "X1", "X3", "X4", "X5", "X6", "Z3", "Z3 Coupé", "Z3 Roadster", "Z4", "Z4 Roadster"]},
  {brand: "Volkswagen", models: ["Amarok", "Beetle", "Bora", "Bora Variant", "Caddy", "Caddy Van", "Life", "California", "Caravelle", "CC", "Crafter", "Crafter Van", "Crafter Kombi", "CrossTouran", "Eos", "Fox", "Golf", "Golf Cabrio", "Golf Plus", "Golf Sportvan", "Golf Variant", "Jetta", "LT", "Lupo", "Multivan", "New Beetle", "New Beetle Cabrio", "Passat", "Passat Alltrack", "Passat CC", "Passat Variant", "Passat Variant Van", "Phaeton", "Polo", "Polo Van", "Polo Variant", "Scirocco", "Sharan", "T4", "T4 Caravelle", "T4 Multivan", "T5", "T5 Caravelle", "T5 Multivan", "T5 Transporter Shuttle", "Tiguan", "Touareg", "Touran"]},
  {brand: "Suzuki", models: ["Alto", "Baleno", "Baleno kombi", "Celerio", "Grand Vitara", "Ignis", "Jimny", "Kizashi", "Liana", "Splash", "Swift", "SX4", "Vitara", "Wagon R", "Wagon R+", "Wagon R Van"]},
  {brand: "Fiat", models: ["500", "500 Cabrio", "500 L", "500X", "Panda", "Punto", "Qubo", "Tipo", "Doblo", "Freemont", "Linea", "Punto Evo", "Punto Van", "Sedici", "Stilo", "Panda 4x4"]},
  {brand: "Ford", models: ["B-Max", "C-Max", "C-Max Grand", "Ecosport", "Edge", "Escape", "Fiesta", "Fiesta Van", "Fiesta Active", "Focus", "Focus Cabriolet", "Focus CC", "Focus Estate", "Focus SW", "Galaxy", "Ka", "Ka+", "Kuga", "Maverick", "Mondeo", "Mondeo Van", "Mustang", "S-Max", "Tourneo Connect", "Tourneo Custom", "Transit", "Transit Custom", "Transit Connect", "Transit Courier", "Transit Jumbo"]},
  {brand: "Audi", models: ["A1", "A1 Sportback", "A2", "A3", "A3 Cabrio", "A3 Sedan", "A3 Sportback", "A4", "A4 Avant", "A4 Allroad", "A5", "A5 Cabrio", "A5 Sportback", "A6", "A6 Avant", "A6 Allroad", "A7", "A7 Sportback", "A8", "A8 L", "A8 Avant", "Q2", "Q3", "Q4", "Q5", "Q5 Sportback", "Q7", "Q8", "Q8 e-tron", "R8", "RS 3", "RS 4", "RS 5", "RS 7", "RS Q3", "RS Q5", "RS Q7", "S3", "S4", "S5", "S6", "S7", "S8", "TT", "TT Roadster", "TTRS", "Q5 PHEV", "Q7 PHEV", "Q8 PHEV"]},
  {brand: "Mercedes-Benz", models: ["A-Class", "B-Class", "C-Class", "CLA", "CL", "CLC", "CLK", "CLS", "E-Class", "G-Class", "GL-Class", "GLA", "GLB", "GLC", "GLC Coupé", "GLS", "GLE", "GLE Coupé", "S-Class", "SLC", "SL", "SLK", "SLS", "V-Class", "Vito", "X-Class"]},
  {brand: "Jaguar", models: ["E-PACE", "F-PACE", "F-Type", "I-PACE", "XE", "XF", "XJ", "XK", "XK8"]},
  {brand: "Land Rover", models: ["Defender", "Discovery", "Discovery Sport", "Range Rover", "Range Rover Evoque", "Range Rover Velar", "Range Rover Sport"]},
  {brand: "Volvo", models: ["240", "740", "850", "940", "960", "C30", "C70", "S40", "S60", "S70", "S80", "S90", "V40", "V50", "V60", "V70", "V90", "XC40", "XC60", "XC70", "XC90"]},
  {brand: "Tesla", models: ["Model 3", "Model S", "Model X", "Model Y"]},
  {brand: "Mini", models: ["Clubman", "Countryman", "Hatch", "Paceman", "Roadster"]},
  {brand: "Fiat", models: ["500", "500 Cabrio", "500 L", "500X", "Panda", "Punto", "Qubo", "Tipo", "Doblo", "Freemont", "Linea", "Punto Evo", "Punto Van", "Sedici", "Stilo", "Panda 4x4"]},
  {brand: "Hyundai", models: ["i10", "i20", "i30", "i40", "iX35", "Kona", "Nexo", "Palisade", "Santa Fe", "Tucson", "Veloster", "Sonata", "Tucson N"]},
  {brand: "Jaguar", models: ["F-Pace", "I-Pace", "XE", "XF", "XJ", "F-Type"]}
];
    // Apply Select2 to Company Dropdown
    $('#companyName').select2({
        placeholder: "Select Company",
        allowClear: true
    });

    // Apply Select2 to Car Model Dropdown
    $('#carModel').select2({
        placeholder: "Select Car Model",
        allowClear: true
    });

    // Function to populate car models based on selected company
    function populateModels() {
        const companyName = $('#companyName').val();
        const modelSelect = $('#carModel');

        modelSelect.empty().append('<option value="">Select Model</option>'); // Clear previous models

        if (companyName) {
            // Find the selected company data
            const selectedBrand = carData.find(brand => brand.brand === companyName);
            if (selectedBrand) {
                selectedBrand.models.forEach(function(model) {
                    modelSelect.append(new Option(model, model)); // Add model options dynamically
                });
            }
        }
        // Trigger Select2 to refresh the dropdown
        modelSelect.trigger('change');
    }

    // Event listener to trigger population when company is selected
    $('#companyName').on('change', function() {
        populateModels();
    });
});

    // Populate manufacture year options (1999 to current year)
    const yearSelect = document.getElementById("manufactureYear");
    const currentYear = new Date().getFullYear();
    for (let year = 1999; year <= currentYear; year++) {
      const option = document.createElement("option");
      option.value = year;
      option.textContent = year;
      yearSelect.appendChild(option);
    }

    // Call the function to populate companies when the page loads
    populateCompanies();

    // Add event listener to populate models when a company is selected
    document.getElementById("companyName").addEventListener("change", populateModels);
  };

  function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        window.location.href = 'cardetails_yash.php?delete_id=' + id;
    }
}
//Execl 
$(document).ready(function () {
    $('#carOwnerTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center"<"top-section"B><"search-section"f>>' + // Buttons on the left, search on the right
             'rt' + // Table body
             '<"d-flex justify-content-between align-items-center mt-3"<"bottom-section"l><"pagination-section"p>>', // Show entries and pagination at the bottom
        buttons: [
            {
                extend: 'excelHtml5', // Excel export button
                text: 'Export to Excel', // Button label
                className: 'custom-excel-button', // Custom class for styling
                title: 'Car and Car Owner Data', // Title of the Excel file
                exportOptions: {
                    columns: function (idx, data, node) {
                        return idx !== 11; // Exclude the "Actions" column (column index 11)
                    }
                }
            }
        ],
        paging: true,  // Pagination
        searching: true,  // Search bar
        order: [[0, 'asc']] // Default sorting by the first column
    });
});
$(document).ready(function() {
    // Apply Select2 to Company Dropdown
    $('#companyName').select2({
        placeholder: "Select Company", // Placeholder text
        allowClear: true // Option to clear selections
    });

    $('#carModel').select2({
    placeholder: "Select Car Model",
    allowClear: true,
    ajax: {
        url: '/path/to/car/models/api', // Replace with actual URL
        dataType: 'json',
        processResults: function (data) {
            return {
                results: data.models.map(function (model) {
                    return {
                        id: model.id,
                        text: model.name
                    };
                })
            };
        }
    }
});

    // Apply Select2 to Manufacture Year Dropdown
    $('#manufactureYear').select2({
        placeholder: "Select Year", // Placeholder text
        allowClear: true // Option to clear selections
    });
});
  </script>