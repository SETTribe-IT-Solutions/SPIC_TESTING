<?php
include('conn.php');

// Check if the form is submitted for insert, update, or delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Print the POST data for debugging (Remove this after debugging)
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';

  // Ensure 'action' is set
  if (isset($_POST['action'])) {
      // If 'action' is 'edit', we are editing a record
      if ($_POST['action'] === 'edit') {
          // Edit functionality
          $id = htmlspecialchars(trim($_POST['id']));
          $companyName = htmlspecialchars(trim($_POST['companyName']));
          $carModel = htmlspecialchars(trim($_POST['carModel']));
          $manufactureYear = htmlspecialchars(trim($_POST['manufactureYear']));
          $licensePlateNumber = htmlspecialchars(trim($_POST['licensePlateNumber']));
          $ownerName = htmlspecialchars(trim($_POST['ownerName']));
          $mobileNumber = htmlspecialchars(trim($_POST['mobileNumber']));
          $address = htmlspecialchars(trim($_POST['address']));
          $email = htmlspecialchars(trim($_POST['email']));
          $status = "Active"; // Default status

          // Check if any required field is empty
          if (empty($companyName) || empty($carModel) || empty($licensePlateNumber) || empty($ownerName) || empty($mobileNumber)) {
              echo "Please fill in all required fields.";
              exit;
          }

          // Validate mobile number (ensure it follows a specific format, e.g., 10 digits starting with 6, 7, or 9)
          if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
              echo "Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, or 9.";
              exit;
          }

          // Check if record exists
          $stmt = $conn->prepare("SELECT id FROM detailsOfCarsAndOwner WHERE id = ?");
          $stmt->bind_param("i", $id);
          $stmt->execute();
          $stmt->store_result();

          if ($stmt->num_rows > 0) {
              $stmt->close(); // Close the SELECT statement before reusing it for UPDATE

              // Update query (removed created_at from the query)
              $stmt = $conn->prepare("UPDATE detailsOfCarsAndOwner 
                                      SET companyName = ?, carModel = ?, manufactureYear = ?, licensePlateNumber = ?, ownerName = ?, mobileNumber = ?, address = ?, email = ?, status = ? 
                                      WHERE id = ?");
              // Bind parameters
              $stmt->bind_param("ssissssssi", $companyName, $carModel, $manufactureYear, $licensePlateNumber, $ownerName, $mobileNumber, $address, $email, $status, $id);

              // Execute the query and check if it was successful
              if ($stmt->execute()) {
                  if ($stmt->affected_rows > 0) {
                      echo "Record updated successfully.";
                  } else {
                      echo "No rows were updated. Check if the data is the same or if the ID is correct.";
                  }
              } else {
                  echo "Error updating record: " . $stmt->error;
              }

              $stmt->close();
          } else {
              echo "No record found with the provided ID.";
          }
      } 
      // If 'action' is 'delete', we are deleting a record
      elseif ($_POST['action'] === 'delete') {
          // Delete functionality
          $id = htmlspecialchars(trim($_POST['id']));

          // Delete query
          $stmt = $conn->prepare("DELETE FROM detailsOfCarsAndOwner WHERE id = ?");
          $stmt->bind_param("i", $id);

          if ($stmt->execute()) {
              echo "Record deleted successfully.";
          } else {
              echo "Error deleting record: " . $stmt->error;
          }

          $stmt->close();
      } 
      // Insert functionality (for inserting a new record)
      else {
          $companyName = htmlspecialchars(trim($_POST['companyName']));
          $carModel = htmlspecialchars(trim($_POST['carModel']));
          $manufactureYear = htmlspecialchars(trim($_POST['manufactureYear']));
          $licensePlateNumber = htmlspecialchars(trim($_POST['licensePlateNumber']));
          $ownerName = htmlspecialchars(trim($_POST['ownerName']));
          $mobileNumber = htmlspecialchars(trim($_POST['mobileNumber']));
          $address = htmlspecialchars(trim($_POST['address']));
          $email = htmlspecialchars(trim($_POST['email']));
          $status = "Active"; // Default status

          // Validate mobile number
          if (!preg_match("/^[6-9][0-9]{9}$/", $mobileNumber)) {
              echo "Invalid mobile number. Please enter a valid 10-digit number starting with 6, 7, or 9.";
              exit;
          }

          // Insert query (created_at will be set automatically by the database)
          $stmt = $conn->prepare("INSERT INTO detailsOfCarsAndOwner 
                                  (companyName, carModel, manufactureYear, licensePlateNumber, ownerName, mobileNumber, address, email, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssissssss", $companyName, $carModel, $manufactureYear, $licensePlateNumber, $ownerName, $mobileNumber, $address, $email, $status);

          if ($stmt->execute()) {
              echo "Record inserted successfully.";
          } else {
              echo "Error inserting record: " . $stmt->error;
          }

          $stmt->close();
      }
  }
} else {
  echo "No form submitted.";
}
        // Insert query
        $stmt = $conn->prepare("INSERT INTO detailsOfCarsAndOwner 
            (companyName, carModel, manufactureYear, licensePlateNumber, ownerName, mobileNumber, address, email, status, dateTime) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssisssssss",
            $companyName,
            $carModel,
            $manufactureYear,
            $licensePlateNumber,
            $ownerName,
            $mobileNumber,
            $address,
            $email,
            $status,
            $dateTime
        );

        if ($stmt->execute()) {
            // Redirect after successful insertion
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Car and Owner Details</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

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
        // Handle the form submission to edit or delete records
        $(document).ready(function () {
            // Edit Button Click
            $('.btn-edit').on('click', function () {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const companyName = row.find('td:nth-child(2)').text();
                const carModel = row.find('td:nth-child(3)').text();
                const licensePlateNumber = row.find('td:nth-child(4)').text();
                const ownerName = row.find('td:nth-child(6)').text();
                const mobileNumber = row.find('td:nth-child(7)').text();
                const email = row.find('td:nth-child(8)').text();
                const address = row.find('td:nth-child(9)').text();

                // Fill the form fields with existing data
                $('#id').val(id); // Hidden input field for the ID
                $('#companyName').val(companyName);
                $('#carModel').val(carModel);
                $('#licensePlateNumber').val(licensePlateNumber);
                $('#ownerName').val(ownerName);
                $('#mobileNumber').val(mobileNumber);
                $('#email').val(email);
                $('#address').val(address);

                // Change the form action to "edit"
                $('<input>').attr({ type: 'hidden', name: 'action', value: 'edit' }).appendTo('form');
            });

            // Delete Button Click
            $('.btn-delete').on('click', function () {
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = $(this).data('id');
                    $('<form>', {
                        method: 'POST',
                        action: window.location.href,
                    })
                        .append($('<input>', { type: 'hidden', name: 'id', value: id }))
                        .append($('<input>', { type: 'hidden', name: 'action', value: 'delete' }))
                        .appendTo('body')
                        .submit();
                }
            });
        });
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


//Execl 
$(document).ready(function () {
    $('.table').DataTable({
        dom: '<"d-flex justify-content-between align-items-center"<"top-section"B><"search-section"f>>' + // Buttons on the left, search on the right
             'rt' + // Table body
             '<"d-flex justify-content-between align-items-center mt-3"<"bottom-section"l><"pagination-section"p>>', // Show entries and pagination at the bottom
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export to Excel', // Button label
                className: 'custom-excel-button', // Custom class for styling
                title: 'Car and Car Owner Data', // Title of the Excel file
                exportOptions: {
                    columns: function (idx, data, node) {
                        return idx !== 9; // Exclude the "Actions" column
                    }
                }
            }
        ],
        paging: true,
        searching: true,
        order: [[0, 'asc']] // Default sorting by the first column
    });
});
</script>


</head>
<head>
  <style>
    .form-control {
      border: 1px solid #ccc;
    }

    h2.card-title.inverse {
    text-align: center;
    
}

h2.card-title {
    font-weight: bold;
}

 /* General table styling */
 .table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for table */
    overflow: hidden;
    border-radius: 8px; /* Rounded corners */
}

/* Table headers */
.table thead th {
    background-color: #223e9c; /* Professional blue background */
    color: #fff; /* White text */
    font-size: 16px;
    font-weight: bold;
    text-align: center; /* Center-align headers */
    border: 1px solid #ddd;
}

/* Table body cells */
.table tbody td {
  
    font-size: 14px;
    text-align: center; /* Center-align data */
    color: #333; /* Dark text for better readability */
    border: 1px solid #ddd;
}

/* Zebra striping for rows */
.table tbody tr:nth-child(even) {
    background-color: #f9f9f9; /* Light gray for even rows */
}

.table tbody tr:nth-child(odd) {
    background-color: #ffffff; /* White for odd rows */
}

/* Hover effect for rows */
.table tbody tr:hover {
    background-color: #f1f1f1; /* Slightly darker gray on hover */
    cursor: pointer; /* Pointer cursor for interactivity */
}


/* Responsive styling for smaller screens */
@media (max-width: 768px) {
    .table thead {
        display: none; /* Hide table headers on smaller screens */
    }

    .table tbody tr {
        display: block; /* Stack rows */
       
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        text-align: left; /* Left-align content for better readability */
       
    }

    .table tbody td::before {
        content: attr(data-label); /* Add labels for mobile view */
        font-weight: bold;
        text-transform: uppercase;
        margin-right: 10px;
        color: #223e9c; /* Blue text for labels */
    }
}

/* Styling for the Export to Excel button */
.custom-excel-button {
   
    background-color: #F0F0F0; /* Light gray background */
  border: 1px solid #ccc; /* Light gray border */
  border-radius: 3px; /* Rounded corners */
  padding: 10px 20px; /* Padding for text */
  font-size: 16px; /* Text size */
  color: #333; /* Text color */
  cursor: pointer; /* Mouse pointer on hover */
}

.custom-excel-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

/* Styling for the "Show entries" dropdown */
.bottom-section select {
    padding: 8px 10px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ccc;
    outline: none;
    margin-right: 10px; /* Add some spacing */
}

/* Styling for the pagination */
.pagination-section .paginate_button {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    font-size: 14px;
    color: #007bff;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.pagination-section .paginate_button:hover {
    background-color: #007bff;
    color: white;
}

.pagination-section .paginate_button.current {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

/* Align "Show entries" and pagination properly */
.bottom-section, .pagination-section {
    display: flex;
    align-items: center;
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
                          <select id="companyName" name="companyName" class="form-control" onchange="populateModels()">
                            <option value="">Select Company</option>
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
                          <input type="text" id="licensePlateNumber" name="licensePlateNumber" class="form-control" placeholder="Enter License Plate Number" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="previousHistory" class="col-sm-3 col-form-label">Previous Service History (Optional)</label>
                        <div class="col-sm-9">
                          <select id="previousHistory" name="previousHistory" class="form-control">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                          </select>
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
                          <input type="text" id="ownerName" name="ownerName" class="form-control" placeholder="Enter Owner Name" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="mobileNumber" class="col-sm-3 col-form-label">Mobile Number</label>
                        <div class="col-sm-9">
                          <input type="text" id="mobileNumber" name="mobileNumber" class="form-control" placeholder="Enter Mobile Number" required>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="address" class="col-sm-3 col-form-label">Address (Optional)</label>
                        <div class="col-sm-9">
                          <input type="text" id="address" name="address" class="form-control" placeholder="Enter Address">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email Address (Optional)</label>
                        <div class="col-sm-9">
                          <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email Address">
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
      <div class="table-responsive"><br><br>
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
              
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Database connection
            include('conn.php');

            // Fetch data query
            $query = "SELECT * FROM detailsOfCarsAndOwner ORDER BY id ASC";
            $result = $conn->query($query);

            // Check if there are results
            if ($result->num_rows > 0) {
              $srNo = 1; // Initialize serial number
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $srNo++ . "</td>
                        <td>" . htmlspecialchars($row['carModel']) . "</td>
                        <td>" . htmlspecialchars($row['licensePlateNumber']) . "</td>
                        <td>" . htmlspecialchars($row['licensePlateNumber']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . htmlspecialchars($row['ownerName']) . "</td>
                        <td>" . htmlspecialchars($row['mobileNumber']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['address']) . "</td>
                        <td class='action-buttons'>
                                                                <button class='btn btn-inverse-warning btn-rounded btn-icon btn-edit' data-id='" . $row['id'] . "'>
                                                                    <i class='mdi mdi-pencil'></i>
                                                                </button>
                                                                <button class='btn btn-inverse-danger btn-rounded btn-icon btn-delete' data-id='" . $row['id'] . "'>
                                                                    <i class='mdi mdi-delete'></i>
                                                                </button>
                                                            </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='11' class='text-center'>No data available</td></tr>";
            }

            // Close connection
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

        </div><?php include '../partials/_footer.html'; ?>
      </div>
      
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
</html>
