<?php
    session_start();
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT * FROM refuel_table ORDER BY entry_id DESC";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Fuel Breakdown</title>
</head>
<body>
    <div class="topnav">
        <a href="home_manager.php">Home</a>
        <a href="vehicles.php">Vehicle Information</a>
        <a href="employees.php">Employee Information</a>
        <a href="deliveries.php">Deliveries Information</a>
        <a href="latedeliveries.php">Late Deliveries</a>
        <a class="active" href="fuelbreakdown.php">Fuel Information</a>
        <a href="servicereports.php">Service Reports</a>
        <div class="topnav-right">
            <a><?php echo $_SESSION['user'];?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
    <table>
        <tr >
            <th colspan="7"><h1>Fuel Breakdown</h1></th>
        </tr>
        <tr>
            <th>Entry No.</th>
            <th>Amount of Fuel</th>
            <th>Cost</th>
            <th>Vehicle Registration</th>
            <th>Time</th>
            <th>Employee No.</th>
            <th>Receipt No.</th>
        </tr>
        <?php
            while($rows=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $rows['entry_id'];?></td>
            <td><?php echo $rows['amount_refueled']." L";?></td>
            <td><?php echo number_format($rows['amount_refueled']*$rows['price'], 2, '.', ',')." Ksh";?></td>
            <td><?php echo $rows['vehicle_reg'];?></td>
            <td><?php echo date_format(date_create($rows['time']),"D, d M Y - h:i A");?></td>
            <td><?php echo $rows['emp_no'];                                        ?></td>
            <td><?php echo $rows['receipt_no'];                                                 ?></td>
        </tr>
        <?php
            }
        ?>
        <tr><td colspan = "7"> End of report</td></tr>
    </table>
    </div>
</body>
</html>