<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT name,emp_number,mobile,role FROM emp_table WHERE active = 1 ORDER BY emp_number";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Employee Information</title>
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
        <a class="active" href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
    <table>
        <tr >
            <th colspan="5"><h1>Employee Information</h1></th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Employee Number</th>
            <th>Mobile Contact</th>
            <th>Employee Role</th>
        </tr>
        <?php
            $i = 1;
            while($rows=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $i;                      ?></td>
            <td><?php echo $rows['name'];           ?></td>
            <td><?php echo $rows['emp_number'];     ?></td>
            <td><?php echo $rows['mobile'];         ?></td>
            <td><?php echo ucfirst($rows['role']);  ?></td>
        </tr>
        <?php
            $i++;
            }
        ?>
        <tr><td colspan = "5"> End of report</td></tr>
    </table>
    </div>
</body>
</html>