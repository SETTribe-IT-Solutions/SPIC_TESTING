<?php
include('conn.php');
?>

<div class="main-container">
<h1 class="main-heading">Monthly Status Records</h1>
<?php

$sql = "SELECT 
            YEAR(appointmentDate) AS year,
            MONTH(appointmentDate) AS month,
            status,
            COUNT(appointmentId) AS value
        FROM appointment
        WHERE YEAR(appointmentDate) = 2025
        AND status IN ('pending', 'confirmed', 'cancelled', 'completed')
        GROUP BY YEAR(appointmentDate), MONTH(appointmentDate), status
        ORDER BY YEAR(appointmentDate), MONTH(appointmentDate);";

$result = $conn->query($sql);

$months = [];
$monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

while ($row = $result->fetch_assoc()) {
    $monthKey = $row["year"] . '-' . $row["month"];
    if (!isset($months[$monthKey])) {
        $months[$monthKey] = [];
    }
    $months[$monthKey][] = $row;
}

if (!empty($months)) {
    echo "<div class='months-container'>";  // Start the container for month cards
    foreach ($months as $month => $records) {
        $monthNum = explode('-', $month)[1];
        $monthName = $monthNames[$monthNum - 1];
        
        echo "<section class='month-card' id='month-" . $month . "'>";
        echo "<h3 class='month-title'>" . $monthName . " 2025</h3>";
        
        // Loop through the statuses and create progress bars
        foreach ($records as $record) {
            $status = ucfirst($record["status"]);
            $value = $record["value"];
            $maxValue = 100; // Assuming the max value to show for a progress bar
            $progressPercentage = ($value / $maxValue) * 100; // Calculate progress percentage
            
            echo "<article class='status-card'>";
            echo "<p class='status-name'>" . $status . "</p>";
            echo "<div class='progress-bar'>";
            echo "<div class='progress' style='width:" . $progressPercentage . "%;'></div>";
            echo "</div>";
            echo "<p class='count'>" . $value . " appointments</p>";
            echo "</article>";
        }
        
        echo "</section>";
    }
    echo "</div>";  // End the container for month cards
} else {
    echo "No records found.";
}

$conn->close();
?>
</div>

<style>

    /* General styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Main container */
        .main-container {
            width: 100%;
            max-width: 1100px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        /* Heading */
        .main-heading {
            font-size: 1.5rem;
            color: #8e44ad;
            margin-bottom: 20px;
        }

        /* Container for the month cards */
        .months-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 5px;
            padding: 10px;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Month card styling */
        .month-card {
            border-radius: 6px;
            padding: 6px;
            background:rgb(203, 230, 248);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 130px;
            max-width: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .month-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Month title styling */
        .month-title {
            text-align: center;
            font-size: 0.85rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Status card styling */
        .status-card {
            background: rgba(255, 255, 255, 0.95);
            margin: 5px 0;
            padding: 5px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, background 0.2s ease;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(5px);
            display: flex;  /* Changed to flexbox */
            justify-content: space-between;  /* To space out elements side by side */
            align-items: center;  /* Vertically center the items */
        }

        /* Adjust status name and progress bar layout */
        .status-name {
            font-weight: bold;
            font-size: 0.75rem;
            color: #444;
            margin-bottom: 0;  /* Remove bottom margin */
            margin-right: 10px;  /* Space between status name and progress bar */
        }

        /* Progress bar container */
        .progress-bar {
            background-color: #ddd;
            border-radius: 50px;
            height: 10px;
            width: 50%;  /* Limit width to fit side by side */
            overflow: hidden;
            margin: 0;  /* Remove margin */
            position: relative;
        }

        /* Progress animation */
        .progress {
            background: #223e9c;
            height: 100%;
            border-radius: 50px;
            transition: width 1s ease;
            position: relative;
        }

        /* Count text */
        .count {
            text-align: center;
            font-size: 0.7rem;
            color: #555;
            margin-top: 2px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .months-container {
                grid-template-columns: repeat(3, 1fr);
            }
            .month-card {
                width: 110px;
            }
            .month-title {
                font-size: 0.75rem;
            }
            .status-name {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .months-container {
                grid-template-columns: repeat(2, 1fr);
            }
            .month-card {
                width: 100px;
            }
            .month-title {
                font-size: 0.7rem;
            }
        }

</style>