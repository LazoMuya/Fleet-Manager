<?php
    session_start();
    if($_SESSION["role"] != "driver"){
        header("Location: redirect.php");
        exit();
    }
    $user = $_SESSION["id"];
    $conn = require __DIR__ . "\config\dbconnect.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Home Page</title>
</head>
<body>
    <div class="topnav">
        <a class="active" href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b><?php echo ucfirst($_SESSION["role"]); ?> Home</b></h4> 
            <p><?php echo ($_SESSION["user"]); ?></p> 
        </div>
        <div class="class2">
            <h4><b>Current Vehicle</b></h4> 
            <p>
                <?php 
                    $result = $conn->query("SELECT * FROM vehicle_table WHERE driver_assigned = '$user'");
                    $row = $result->fetch_assoc();
                    if ($row != NULL)
                        echo($row["model"]." - ".$row["reg_no"].". Current mileage is ".number_format($row["mileage"])." Kms.<br> Service status is <b><i>".$row["status"]."</i></b>. Due for service in <b>".number_format($row["next_service"]-$row["mileage"])." Kms</b>.");
                    else
                        echo("You have not been assigned a vehicle. This shall be addressed soon.");
                ?>
            </p> 
        </div>
        <div class="class3">
            <h4><b>Active Job Information</b></h4> 
            <p>
                <?php
                    $result2 = $conn->query("SELECT * FROM delivery_table WHERE driver = '$user' AND status = 'picked'");
                    $row2 = $result2->fetch_assoc();
                    if ($row2 != NULL)
                        echo ("Order No. ".$row2["delivery_number"]." - <b><i>".$row2["description"]."</i></b> for <b><i>".$row2["client"]."</i></b> to be picked up at <b><i>".$row2["pickup_location"]."</i></b> and to be delivered to <b><i>".$row2["delivery_point"]."</i></b> on <b><i>".date_format(date_create($row2['scheduled_delivery']),"D, d M Y")."</i></b>");
                    else {
                        $counts = $conn->query("SELECT count(delivery_number) AS deliveries FROM delivery_table WHERE driver = '$user' AND status != 'delivered' AND status != 'cancelled'");
                        $sum = $counts->fetch_assoc();
                        echo ("You don't have an active job at the moment. You currently have <i><b>".$sum['deliveries']."</i></b> pending jobs at the moment.");
                    }
                ?>
            </p> 
        </div>
    </div>
    <div class="container">
    <table>
        <tr>
            <th colspan="9"><h2>Jobs History</h2></th>
        </tr>
        <tr>
            <th>Order No.</th>
            <th>Description</th>
            <th>Client</th>
            <th>Pickup Location</th>
            <th>Delivery Location</th>
            <th>Date of Pickup</th>
            <th>Date of Delivery</th>
            <th>Status</th>
            <th>Info</th>
        </tr>
        <?php
            $result2 = $conn->query("SELECT * FROM delivery_table WHERE driver = '$user' ORDER BY CASE WHEN status = 'delivered' then 1 ELSE 0 END");
            $i = 1;
            while($rows = $result2->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $rows['delivery_number'];    ?></td>
            <td><?php echo $rows['description'];        ?></td>
            <td><?php echo $rows['client'];             ?></td>
            <td><?php echo $rows['pickup_location'];    ?></td>
            <td><?php echo $rows['delivery_point'];     ?></td>
            <td><?php echo date_format(date_create($rows['scheduled_pickup']),"D, d M Y");      ?></td>
            <td><?php echo date_format(date_create($rows['scheduled_delivery']),"D, d M Y");      ?></td>
            <td><?php echo ucfirst($rows['status'])     ?></td>
            <td><?php echo $rows['info'];               ?></td>
        </tr>
        <?php
            $i++;
            }
        ?>
        <tr><td colspan = "9"> End of report</td></tr>
    </table>
    </div>
</body>
</html>