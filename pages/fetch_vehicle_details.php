<?php
include('conn.php');

if (isset($_POST['vehicleNumber'])) {
    $vehicleNumber = mysqli_real_escape_string($conn, $_POST['vehicleNumber']);

    $query = "SELECT company, carModel FROM enquiryForm WHERE vehicleNumber = ? AND status = 'active' LIMIT 1";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $vehicleNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        echo json_encode($data); // Return data in JSON format
        $stmt->close();
    }
}
?>
