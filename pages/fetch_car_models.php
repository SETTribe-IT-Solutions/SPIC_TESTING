<?php
include('conn.php');

if (isset($_POST['company'])) {
    $company = mysqli_real_escape_string($conn, $_POST['company']);

    $query = "SELECT carModel FROM addCars WHERE company = ? AND status = 'active' ORDER BY carModel";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $company);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<option value=''>Select Car Model</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['carModel']) . "'>" . htmlspecialchars($row['carModel']) . "</option>";
        }

        $stmt->close();
    }
}
?>
