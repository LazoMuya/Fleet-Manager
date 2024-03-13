<?php
    session_start();
    if($_SESSION["role"] != "dispatch"){
        header("Location: redirect.php");
        exit();
    }
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT *, next_service-mileage AS service FROM vehicle_table WHERE active = '1' AND next_service-mileage < 2000 OR status = 'service' ORDER BY service,driver_assigned DESC;";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Vehicles due for service</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a href="markmaintenance_html.php">Mark For Maintanance</a>
        <a href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a class="active" href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="class1">
            <h4><b>Schedule Vehicles for Service</b></h4> 
            <p>To schedule a vehicle for service, click <a href="markmaintenance_html.php">here</a></p> 
        </div>
    <div class="container">
        <table>
            <tr>
                <th colspan="7"><h2>Vehicles due for service</h2></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Make and Model</th>
                <th>Registration</th>
                <th>Next Service Mileage</th>
                <th>Current Mileage</th>
                <th>Vehicle Age</th>
                <th>Driver Assigned</th>
            </tr>
            <?php
                $i = 1;
                while($rows=$result->fetch_assoc())
                {
            ?>
            <tr>
                <td><?php echo $i;                                                  ?></td>
                <td><?php echo $rows['model'];                                      ?></td>
                <td><?php echo $rows['reg_no'];                                     ?></td>
                <td><?php if($rows['next_service'] != NULL) echo number_format($rows['next_service'])." Kms"; else echo "N/A";         ?></td>
                <td><?php echo number_format($rows['mileage'])." Kms";              ?></td>
                <td><?php $age=date("Y")-$rows['year_made']; if ($age == 1) echo $age." Year"; else if($age == 0) echo "< 1 Year"; else echo $age." Years";    ?></td>
                <td><?php if ($rows['driver_assigned'] != NULL) echo $rows['driver_assigned']; else echo "-";   ?></td>
            </tr>
            <?php
                $i++;
                }
            ?>
            <tr><td colspan = "7"> End of report</td></tr>
        </table>
    </div>
</body>
</html>