<?php
    session_start();
    if($_SESSION["role"] != "driver"){
        header("Location: redirect.php");
        exit();
    }
    $user = $_SESSION['id'];
    $conn = require __DIR__ . "\config\dbconnect.php";
    $result = $conn->query("SELECT * FROM vehicle_table WHERE active = '1' AND driver_assigned = '$user'");
    $row = $result->fetch_assoc();
    $reg_no = $row['reg_no'];
    $array = $conn->query("SELECT * FROM maintenance_table WHERE vehicle_regno = '$reg_no'");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Vehicle History</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a class="active" href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <table>
            <tr>
                <th>Work No.</th>
                <th>Operations Done</th>
                <th>Date Completed</th>
                <th>Next Service Mileage</th>
                <th>Duration Taken</th>
                <th>Comments</th>
            </tr>
            <?php
                while($record = $array->fetch_assoc())
                {
            ?>
            <tr>
                <td><?php echo $record['work_number'];                                      ?></td>
                <td><?php echo $record['operations_done'];                                     ?></td>
                <td><?php echo date_format(date_create($record['date']),"D, d M Y");                                             ?></td>
                <td><?php echo number_format($record['next_service'])." Kms";              ?></td>
                <td><?php echo $record['duartion_taken'];    ?></td>
                <td><?php if ($record['comment'] != NULL) echo $record['comment']; else echo "-";   ?></td>
            </tr>
            <?php
                }
            ?>
            <tr><td colspan = "6"> End of report</td></tr>
        </table>
    </div>
</body>
</html>