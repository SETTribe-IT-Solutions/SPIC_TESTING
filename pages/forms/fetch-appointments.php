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
