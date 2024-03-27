<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT * FROM delivery_table ORDER BY delivery_number";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Delivery Information</title>
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
        <a class="active" href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
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
            <th>Status</th>
            <th>Scheduled Delivery</th>
            <th>Date of Delivery</th>
            <th>Assigned driver</th>
            <th>Additional info</th>
        </tr>
        <?php
            while($rows=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $rows['delivery_number'];                                        ?></td>
            <td><?php echo $rows['description'];                                            ?></td>
            <td><?php echo $rows['client'];                                                 ?></td>
            <td><?php echo ucfirst($rows['status']);                                        ?></td>
            <td><?php echo date_format(date_create($rows['scheduled_delivery']),"D, d M Y");?></td>
            <td><?php if ($rows['date_of_delivery'] != NULL) echo date_format(date_create($rows['date_of_delivery']),"D, d M Y"); elseif ($rows['status'] == "cancelled") echo "Cancelled";  else echo "Pending";  ?></td>
            <td><?php echo $rows['driver'];                                                 ?></td>
            <td><?php if ($rows['info'] != NULL) echo ucfirst($rows['info']); else echo "-";?></td>
        </tr>
        <?php
            }
        ?>
        <tr><td colspan = "8"> End of report</td></tr>
    </table>
    </div>
</body>
</html>