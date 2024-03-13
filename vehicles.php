<?php
    session_start();
    if($_SESSION["role"] != "dispatch"){
        header("Location: redirect.php");
        exit();
    }
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    $sql = "SELECT * FROM vehicle_table WHERE active = 1";
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Vehicle Information</title>
</head>
<body>
    <div class="topnav">
        <a href="home_manager.php">Home</a>
        <a class="active" href="vehicles.php">Vehicle Information</a>
        <a href="employees.php">Employee Information</a>
        <a href="deliveries.php">Deliveries Information</a>
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
            <tr>
                <th colspan="7"><h2>All Vehicles Report</h2></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Make and Model</th>
                <th>Registration</th>
                <th>Category & Class</th>
                <th>Mileage</th>
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
                <td><?php switch($rows['category']){
                            case "t":
                                $category = "Trailer";
                                break;
                            case "lo":
                                $category = "Light - Open";
                                break;
                            case "lc":
                                $category = "Light - Closed";
                                break;
                            case "mo":
                                $category = "Medium - Open";
                                break;
                            case "mc":
                                $category = "Medium - Closed";
                                break;
                            case "ho":
                                $category = "Heavy - Open";
                                break;
                            case "hc":
                                $category = "Heavy - Closed";
                                break;
                        }
                        echo $category;                                             ?></td>
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