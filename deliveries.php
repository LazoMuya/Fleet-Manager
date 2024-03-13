<?php
    session_start();
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT * FROM delivery_table ORDER BY delivery_number desc";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Deliveries Information</title>
</head>
<body>
    <div class="topnav">
        <a href="home_manager.php">Home</a>
        <a href="vehicles.php">Vehicle Information</a>
        <a href="employees.php">Employee Information</a>
        <a class="active" href="deliveries.php">Deliveries Information</a>
        <a href="latedeliveries.php">Late Deliveries</a>
        <a href="fuelbreakdown.php">Fuel Information</a>
        <a href="servicereports.php">Service Reports</a>
        <div class="topnav-right">
            <a><?php echo $_SESSION['user'];?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
    <table>
        <tr >
            <th colspan="8"><h1>Deliveries Information</h1></th>
        </tr>
        <tr>
            <th>Delivery No.</th>
            <th>Description</th>
            <th>Client</th>
            <th>Scheduled Delivery</th>
            <th>Date of Delivery</th>
            <th>Assigned driver</th>
            <th>Status</th>
            <th>Delivery Time</th>
        </tr>
        <?php
            while($rows=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $rows['delivery_number'];                                        ?></td>
            <td><?php echo $rows['description'];                                            ?></td>
            <td><?php echo $rows['client'];                                                 ?></td>
            <td><?php echo date_format(date_create($rows['scheduled_delivery']),"D, d M Y");?></td>
            <td><?php if ($rows['date_of_delivery'] != NULL) echo date_format(date_create($rows['date_of_delivery']),"D, d M Y");   else echo "-";  ?></td>
            <td><?php echo $rows['driver'];                                                 ?></td>
            <td><?php echo ucfirst($rows['status']);                                        ?></td>
            <td><?php if($rows['scheduled_delivery'] < $rows['date_of_delivery']) echo "Late"; else echo"On Time";?></td>
        </tr>
        <?php
            }
        ?>
        <tr><td colspan = "8"> End of report</td></tr>
    </table>
    </div>
</body>
</html>