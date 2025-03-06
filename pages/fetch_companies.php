<?php
include('conn.php'); // Include your database connection

$query = "SELECT DISTINCT company FROM addCars WHERE status='active' ORDER BY company";
$result = $conn->query($query);
$companies = [];
while ($row = $result->fetch_assoc()) {
    $companies[] = $row['company'];
}

header('Content-Type: application/json');
echo json_encode($companies);
?>