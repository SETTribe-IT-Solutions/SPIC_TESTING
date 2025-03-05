<?php
include('conn.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Status Records</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Prevent scrolling */
            align-items: center;
        }

        .main-container {
            width: 100%;
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            height: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        .filter-container {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        /* .month-select {
            padding: 5px 12px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: pointer;
        } */

        .status-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .status-card {
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .status-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .progress-section {
            margin: 10px 0;
        }

        .status-name {
            font-weight: bold;
            font-size: 1rem;
            color: #333;
            margin-bottom: 5px;
        }

        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 10px;
            height: 10px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .progress {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .green-bar { background: #28a745; }
        .blue-bar { background: #007bff; }
        .red-bar { background: #dc3545; }
        .orange-bar { background: #fd7e14; }

        .count {
            font-size: 0.9rem;
            color: #555;
            margin-top: 5px;
        }

        .filter-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #555;
            width: 100%;
            padding-right: 20px;
        }

        .month-select {
            padding: 10px 15px;
            font-size: 0.8rem;
            border: 2px solid #007bff;
            border-radius: 8px;
            background: #fff;
            color: #007bff;
            font-weight: bold;
            outline: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.2);
        }

        .month-select:hover {
            /* background: #007bff; */
            color: #007bff;
            /* box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3); */
        }

        .month-select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 10px rgba(0, 91, 187, 0.5);
        }

    </style>
</head>
<body>

<div class="main-container">
    <h1 class="main-heading">Monthly Status Records</h1>

    <!-- Dropdown for Month Selection -->
    <div class="filter-container">
        <label for="monthFilter">Select Month:</label>
        <select id="monthFilter" class="month-select" onchange="filterByMonth()">
            <option value="all" selected>All Months</option>
            <?php
            $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            for ($i = 0; $i < 12; $i++) {
                echo "<option value='" . ($i + 1) . "'>" . $monthNames[$i] . "</option>";
            }
            ?>
        </select>
    </div>

    <?php
    // Initialize an array to store data
    $statuses = ['Confirmed', 'Cancelled', 'Pending'];
    $monthlyData = [];
    $totalData = array_fill_keys($statuses, 0);

    // Fetch data for each individual month
    for ($i = 1; $i <= 12; $i++) {
        $sql = "SELECT status, COUNT(appointmentId) AS value
                FROM appointment 
                WHERE YEAR(appointmentDate) = 2025 AND MONTH(appointmentDate) = $i 
                GROUP BY status";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $status = $row["status"];
            $monthlyData[$i][$status] = $row["value"];
            $totalData[$status] += $row["value"];  // Update totals for "All Months"
        }
    }

    // Displaying All Months Data (Totals)
    echo "<div class='status-container'>";
    echo "<section class='status-card' id='month-all' style='display: block;'>";
    echo "<h3 class='status-title'>All Months 2025</h3>";

    foreach ($statuses as $status) {
        $totalCount = $totalData[$status] ?? 0;
        $progressPercentage = ($totalCount / 96) * 100;  // Assuming a max value (adjust if needed)
        $colorClasses = [
            'Confirmed' => 'blue-bar',
            'Cancelled' => 'red-bar',
            'Pending' => 'orange-bar'
        ];

        echo "<div class='progress-section status-$status'>";
        echo "<p class='status-name'>$status</p>";
        echo "<div class='progress-bar'><div class='progress {$colorClasses[$status]}' style='width:$progressPercentage%;'></div></div>";
        echo "<p class='count'>$totalCount appointments</p>";
        echo "</div>";
    }
    echo "</section>";

    // Displaying Data for Individual Months
    for ($i = 1; $i <= 12; $i++) {
        echo "<section class='status-card' id='month-$i' style='display: none;'>";
        echo "<h3 class='status-title'>{$monthNames[$i-1]} 2025</h3>";

        foreach ($statuses as $status) {
            $value = $monthlyData[$i][$status] ?? 0;
            $progressPercentage = ($value / 8) * 100;  // Assuming a max value per month (adjust if needed)

            echo "<div class='progress-section status-$status'>";
            echo "<p class='status-name'>$status</p>";
            echo "<div class='progress-bar'><div class='progress {$colorClasses[$status]}' style='width:$progressPercentage%;'></div></div>";
            echo "<p class='count'>$value appointments</p>";
            echo "</div>";
        }

        echo "</section>";
    }

    echo "</div>"; // Close status-container
    $conn->close();
    ?>

</div>

<script>
function filterByMonth() {
    var selectedMonth = document.getElementById("monthFilter").value;
    var statusCards = document.querySelectorAll(".status-card");

    if (selectedMonth === "all") {
        // Show only the "All Months" section
        statusCards.forEach(function(card) {
            if (card.id === 'month-all') {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    } else {
        // Hide all sections initially
        statusCards.forEach(function(card) {
            var cardMonth = card.id.split("-")[1];

            // Show the section for the selected month
            if (cardMonth === selectedMonth) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }
}
</script>

</body>
</html>
