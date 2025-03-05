<?php
include('conn.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = $_POST['companyName'];
    $carModel = $_POST['carModel'];
    $manufactureYear = $_POST['manufactureYear'];
    $licensePlateNumber = $_POST['licensePlateNumber'];
    $previousHistory = $_POST['previousHistory'];
    $ownerName = $_POST['ownerName'];
    $mobileNumber = $_POST['mobileNumber'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $customerId = $_POST['customerId'];

    // Insert data into the database
    $sql = "INSERT INTO detailsOfCarsAndOwner 
            (companyName, carModel, manufactureYear, licensePlateNumber, previousHistory, ownerName, mobileNumber, address, email, customerId, status, dateTime) 
            VALUES 
            ('$companyName', '$carModel', '$manufactureYear', '$licensePlateNumber', '$previousHistory', '$ownerName', '$mobileNumber', '$address', '$email', '$customerId', 'Active', NOW())";
    $conn->query($sql);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $conn->query("DELETE FROM detailsOfCarsAndOwner WHERE id = $deleteId");
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Fetch records from the database
$records = $conn->query("SELECT * FROM detailsOfCarsAndOwner");
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
    <link rel="shortcut icon" href="../../images/favicon.png">
    <style>
        .container-fluid.page-body-wrapper {
            width: 100% !important;
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <script>
        function editRecord(id) {
            // Redirect to edit the record with the given ID
            window.location.href = "editForm.php?id=" + id;
        }

        function deleteRecord(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                window.location.href = "?delete_id=" + id;
            }
        }
    </script>
</head>

<body>
    <div class="container-scroller d-flex">
        <?php include '../../partials/navbar1.html'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include '../../partials/_navbar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title inverse">Details of Car and Owner</h2>
                                <form class="form-sample" method="POST">
                                    <p class="card-description">Details of Car</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="companyName" class="col-sm-3 col-form-label">Select Company</label>
                                                <div class="col-sm-9">
                                                <select id="companyName" name="companyName" class="form-control" onchange="populateModels()">
                                                <option value="">Select Company</option>
                                                <!-- Company options will be added dynamically -->
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="carModel" class="col-sm-3 col-form-label">Select Car Model</label>
                                                <div class="col-sm-9">
                                                <select id="carModel" name="carModel" class="form-control">
                                                <option value="">Select Model</option>
                                                <!-- Car models will be populated based on selected company -->
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
                                                <select id="manufactureYear" name="manufactureYear" class="form-control">
                                                    <option value="">Select Year</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="licensePlateNumber" class="col-sm-3 col-form-label">License Plate Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="licensePlateNumber" name="licensePlateNumber" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-description">Details of Owner</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="ownerName" class="col-sm-3 col-form-label">Owner Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="ownerName" name="ownerName" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="mobileNumber" class="col-sm-3 col-form-label">Mobile Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="mobileNumber" name="mobileNumber" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="address" class="col-sm-3 col-form-label">Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="address" name="address" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-3 col-form-label">Email Address</label>
                                                <div class="col-sm-9">
                                                    <input type="email" id="email" name="email" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="customerId" class="col-sm-3 col-form-label">Customer ID</label>
                                                <div class="col-sm-9">
                                                    <input type="text" id="customerId" name="customerId" class="form-control" required>
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
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-danger">Details Of Cars and Owner</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No</th>
                                                    <th>Car Company</th>
                                                    <th>Car Model</th>
                                                    <th>License Plate Number</th>
                                                    <th>Previous History</th>
                                                    <th>Contact No</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($records->num_rows > 0) : ?>
                                                    <?php while ($row = $records->fetch_assoc()) : ?>
                                                        <tr>
                                                            <td><?= $row["id"] ?></td>
                                                            <td><?= $row["companyName"] ?></td>
                                                            <td><?= $row["carModel"] ?></td>
                                                            <td><?= $row["licensePlateNumber"] ?></td>
                                                            <td><?= $row["previousHistory"] ?></td>
                                                            <td><?= $row["mobileNumber"] ?></td>
                                                            <td><?= $row["email"] ?></td>
                                                            <td><?= $row["address"] ?></td>
                                                            <td><?= $row["status"] ?></td>
                                                            <td>
                                                                <button class="btn btn-inverse-warning btn-rounded btn-icon" onclick="editRecord(<?= $row['id'] ?>)"><i class="mdi mdi-file-check"></i></button>
                                                                <button class="btn btn-inverse-danger btn-rounded btn-icon" onclick="deleteRecord(<?= $row['id'] ?>)"><i class="mdi mdi-delete"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center">No Records Found</td>
                                                    </tr>
                                                <?php endif; ?>
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
    </div>
  
    <script>
    function validateMobileNumber() {
      var mobileNumber = document.getElementById('mobileNumber').value;
      var pattern = /^[6-9][0-9]{9}$/; // Mobile number should start with 6, 7, or 9 and have 10 digits
      if (!pattern.test(mobileNumber)) {
        alert('Please enter a valid mobile number starting with 6, 7, or 9 and containing 10 digits.');
        return false;
      }
      return true;
    }
  </script>
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

    // Function to populate car models based on selected company
    function populateModels() {
      const companyName = document.getElementById("companyName").value;
      const modelSelect = document.getElementById("carModel");
      modelSelect.innerHTML = '<option value="">Select Model</option>'; // Clear previous models

      if (companyName) {
        const selectedBrand = carData.find(brand => brand.brand === companyName);
        if (selectedBrand) {
          selectedBrand.models.forEach(model => {
            const option = document.createElement("option");
            option.value = model;
            option.textContent = model;
            modelSelect.appendChild(option);
          });
        }
      }
    }

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
</script>
<script>
  function validateLicensePlate() {
    const licensePlateInput = document.getElementById('licensePlateNumber');
    const errorMessage = document.getElementById('error-message');
    
    // Regex for validating license plate
    const pattern = /^[A-Z0-9-]{1,15}$/;
    
    if (!pattern.test(licensePlateInput.value)) {
      // Show error message if input is invalid
      errorMessage.style.display = 'block';
      licensePlateInput.classList.add('is-invalid');
    } else {
      // Hide error message if input is valid
      errorMessage.style.display = 'none';
      licensePlateInput.classList.remove('is-invalid');
    }
  }
</script>
<script>
  function validateMobileNumber() {
    const mobileNumberInput = document.getElementById('mobileNumber');
    const errorMessage = document.getElementById('mobile-error-message');
    
    // Regex to check that the mobile number starts with 6,7,8,9 and contains exactly 10 digits
    const pattern = /^[6789][0-9]{9}$/;
    
    if (!pattern.test(mobileNumberInput.value)) {
      // Show error message if the mobile number is invalid
      errorMessage.style.display = 'block';
      mobileNumberInput.classList.add('is-invalid');
    } else {
      // Hide error message if the mobile number is valid
      errorMessage.style.display = 'none';
      mobileNumberInput.classList.remove('is-invalid');
    }
  }
</script>
<script>
  function validateEmail() {
    const emailInput = document.getElementById('email');
    const errorMessage = document.getElementById('email-error-message');
    
    // Regex to check if email ends with @gmail.com
    const pattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    
    if (!pattern.test(emailInput.value)) {
      // Show error message if the email is invalid
      errorMessage.style.display = 'block';
      emailInput.classList.add('is-invalid');
    } else {
      // Hide error message if the email is valid
      errorMessage.style.display = 'none';
      emailInput.classList.remove('is-invalid');
    }
  }
</script>
</body>
</html>