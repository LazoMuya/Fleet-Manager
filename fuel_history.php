<?php
    session_start();
    if($_SESSION["role"] != "driver"){
        header("Location: redirect.php");
        exit();
    }
    $id = $_SESSION['id'];
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT * FROM refuel_table WHERE emp_no = '$id' ORDER BY entry_id DESC";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Fuel History</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a class="active" href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
    <table>
        <tr >
            <th colspan="6"><h1>Fuel Breakdown</h1></th>
        </tr>
        <tr>
            <th>Entry No.</th>
            <th>Amount of Fuel</th>
            <th>Cost</th>
            <th>Vehicle Registration</th>
            <th>Time</th>
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
            <td><?php echo $rows['receipt_no'];                                                 ?></td>
        </tr>
        <?php
            }
        ?>
        <tr><td colspan = "6"> End of report</td></tr>
    </table>
    </div>
</body>
</html>