<?php
    session_start();
    if($_SESSION["role"] != "manager"){
        header("Location: redirect.php");
        exit();
    }
    $conn = require __DIR__ . "\config\dbconnect.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Manager Home</title>
</head>
<body>
    <div class="topnav">
        <a class="active" href="home_manager.php">Home</a>
        <a href="vehicles.php">Vehicle Information</a>
        <a href="employees.php">Employee Information</a>
        <a href="deliveries.php">Deliveries Information</a>
        <a href="latedeliveries.php">Late Deliveries</a>
        <a href="fuelbreakdown.php">Fuel Information</a>
        <a href="servicereports.php">Service Reports</a>
        <div class="topnav-right">
            <a>Welcome <?php echo $_SESSION['user'];?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b>Number of  Vehicles</b></h4> 
            <p>
                <?php 
                    $result = $conn->query("SELECT COUNT(reg_no) as count FROM vehicle_table WHERE active = 1");
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 0)
                        echo("There are no active vehicles.");
                    else if ($row['count'] == 1)
                        echo("There is currently <b>".$row['count']."</b> vehicle. Click <a href='vehicles.php'>here</a> for more information.");
                    else
                        echo("There are currently <b>".$row['count']."</b> vehicles. Click <a href='vehicles.php'>here</a> for more information.");
                ?>
            </p> 
        </div>
        <div class="class2">
            <h4><b>Fuel Information</b></h4>
            <p>
                <?php
                    $result2 = $conn->query("SELECT SUM(amount_refueled * price) as fuel_cost FROM refuel_table WHERE time >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
                    $row2 = $result2->fetch_assoc();
                    if ($row2['fuel_cost'] == NULL) $amount = 0; else $amount = number_format($row2['fuel_cost']);
                    echo("The amount spent on fuel in the last month is <b>".$amount." Ksh</b>. Click <a href='fuelbreakdown.php'>here</a> for more information.");
                ?>
            </p>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b>New employees</b></h4> 
            <p>
                <?php 
                    $result = $conn->query("SELECT count(emp_number) as users FROM emp_table WHERE date_created >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
                    $row = $result->fetch_assoc();
                    if ($row['users'] == 0)
                        echo("There were no new employees in the past month.");
                    else if ($row['users'] == 1)
                        echo("There was <b>".$row['users']."</b> new user in the past month. Click <a href='employees.php'>here</a> for more information.");
                    else
                        echo("There are <b>".$row['users']."</b> new users in the past month. Click <a href='employees.php'>here</a> for more information.");
                ?>
            </p> 
        </div>
        <div class="class2">
            <h4><b>Deliveries in the past month</b></h4> 
            <p>
                <?php
                    $result3 = $conn->query("SELECT count(delivery_number) as deliveries FROM delivery_table WHERE date_created >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH);");
                    $row3 = $result3->fetch_assoc();
                    if ($row3['deliveries'] == 0)
                        echo("There are no new deliveries in the past month.");
                    else if ($row3['deliveries'] == 1)
                        echo("There is currently ".$row3['deliveries']." delivery that has been created. Click <a href='deliveries.php'>here</a> for more information.");
                    else
                        echo("There are currently ".$row3['deliveries']." deliveries that have been created. Click <a href='deliveries.php'>here</a> for more information.");
                ?>
            </p> 
        </div>
    </div>
</body>
</html>