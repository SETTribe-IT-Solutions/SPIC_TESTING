<?php
include('conn.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarzSpa - Appointment Booking</title>
    <link rel="stylesheet" href="../vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="shortcut icon" href="../images/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    </head>
<?php
include('conn.php');

// Define the four required service types
$requiredServices = ['Car Cleaning', 'Full Car Service', 'Coating Car', 'Oil Change'];

// SQL query to count appointments for only the specified services
$sql = "SELECT ServiceType, COUNT(*) as service_count 
        FROM appointment 
        WHERE ServiceType IN ('Car Cleaning', 'Full Car Service', 'Coating Car', 'Oil Change') 
        GROUP BY ServiceType"; 

// Execute the query
$result = $conn->query($sql);

// Initialize the appointment array with default values (0 counts for missing types)
$appointment = [
    'Car Cleaning' => 0,
    'Full Car Service' => 0,
    'Coating Car' => 0,
    'Oil Change' => 0
];

// Update counts from the database
if ($result->num_rows > 0) {     
    while ($row = $result->fetch_assoc()) {         
        $appointment[$row['ServiceType']] = (int) $row['service_count'];     
    }      
}  

// Convert the associative array to a list format for JSON response
$response = [];
foreach ($appointment as $service => $count) {
    $response[] = ['ServiceType' => $service, 'service_count' => $count];
}

// Return JSON response
echo json_encode($response);

// Close the connection
$conn->close();

?>
<style>

        /* Chart container style */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 10vh;
        }

        /* Card and container for chart styling */
        .col-lg-6.grid-margin.stretch-card {
            margin-top: 10px;
        }

        .card-body {
            padding: 10px;
        }

        /* Responsive handling */
        @media (max-width: 768px) {
            .chart-container {
                padding: 10px;
            }
        }

        /* Hide the scrollbar for the whole body */
        body {
            overflow: hidden;
        }

        /* If you want to hide the scrollbar for specific elements */
        .some-element-class {
            overflow: hidden;
        }

        /* For webkit-based browsers (Chrome, Safari) */
        .some-element-class::-webkit-scrollbar {
            display: none;
        }

        /* For Firefox */
        .some-element-class {
            scrollbar-width: none;
        }
    </style>
</head>

<body>
    <!-- Main container div -->
    <div class="container">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Service Types Bar Chart</h4>
                    <!-- Canvas to render the Bar Chart -->
                    <canvas id="barChart" width="280" height="50" style="display: block; width: 300px; height: 150px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fetch the data from the PHP script
        fetch('fetch-appointments.php')
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    console.error("No data received.");
                    return;
                }

                // Extract labels (ServiceType) and data (service_count)
                const labels = data.map(item => item.ServiceType);
                const serviceCounts = data.map(item => parseInt(item.service_count));

                // Dynamically generate colors if there are many services
                const backgroundColors = labels.map((_, i) => rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.2));
                const borderColors = backgroundColors.map(color => color.replace('0.2', '1')); // Adjust border opacity

                // Create the bar chart using Chart.js
                const ctx = document.getElementById('barChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Service Types Count',
                            data: serviceCounts,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>
</body>

</html>