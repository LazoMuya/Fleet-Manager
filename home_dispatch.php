<?php
    session_start();
    if($_SESSION["role"] != "dispatch"){
        header("Location: redirect.php");
        exit();
    }
    $conn = require __DIR__ . "\config\dbconnect.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Dispatch Manager</title>
</head>
<body>
    <div class="topnav">
        <a class="active" href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a href="markmaintenance_html.php">Mark For Maintanance</a>
        <a href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b><?php echo ucfirst($_SESSION["role"]); ?> Home</b></h4> 
            <p>
                <?php
                    $result = $conn->query("SELECT COUNT(delivery_number) as count FROM delivery_table WHERE status = 'incomplete'");
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 0)
                        echo("No delieveries need reassigning.");
                    else if ($row['count'] == 1)
                        echo("There is currently ".$row['count']." delivery that needs reassigning. Click <a href='reassignjob_html.php'>here</a> to reassign.");
                    else
                        echo("There are currently ".$row['count']." deliveries that needs reassigning. Click <a href='reassignjob_html.php'>here</a> to reassign.");
                ?>
            </p> 
        </div>
        <div class="class2">
            <h4><b>Unassigned Vehicles</b></h4> 
            <p>
                <?php 
                    $result2 = $conn->query("SELECT COUNT(reg_no) as count FROM vehicle_table WHERE active = 1 AND driver_assigned is NULL");
                    $row2 = $result2->fetch_assoc();
                    if ($row2['count'] == 0)
                        echo("All vehicles are currently assigned to drivers.");
                    else if ($row2['count'] == 1)
                        echo("There is currently ".$row2['count']." vehicle unassigned to a driver. Click <a href='assigndriver_html.php'>here</a> to assign.");
                    else
                        echo("There are currently ".$row2['count']." vehicles unassigned to drivers. Click <a href='assigndriver_html.php'>here</a> to assign.");
                ?>
            </p> 
        </div>
        <div class="class3">
            <h4><b>Vehicles due for service soon</b></h4> 
            <p>
                <?php
                    $result3 = $conn->query("SELECT COUNT(reg_no) as count FROM vehicle_table WHERE active = '1' AND next_service < (mileage + 2000)");
                    $row3 = $result3->fetch_assoc();
                    if ($row3['count'] == 0)
                        echo("No vehicles are due for service soon.");
                    else if ($row3['count'] == 1)
                        echo("There is currently ".$row3['count']." vehicle due for service soon. Click <a href='vehicles_due.php'>here</a> for more information.");
                    else
                        echo("There are currently ".$row3['count']." vehicles due for service soon. Click <a href='vehicles_due.php'>here</a> for more information.");
                ?>
            </p> 
        </div>
</body>
</html>