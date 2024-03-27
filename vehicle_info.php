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
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a href="markmaintenance_html.php">Mark For Maintanance</a>
        <a href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a class="active" href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
    <table>
        <tr>
            <th colspan="8"><h2>Vehicle Status Report</h2></th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Make and Model</th>
            <th>Vehicle Registration</th>
            <th>Category & Class</th>
            <th>Mileage</th>
            <th>Vehicle Status</th>
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
            <td><?php echo ucfirst($rows['status']);                            ?></td>
            <td><?php $age=date("Y")-$rows['year_made']; if ($age == 1) echo $age." Year"; else if($age == 0) echo "< 1 Year"; else echo $age." Years";    ?></td>
            <td><?php if ($rows['driver_assigned'] != NULL) echo $rows['driver_assigned']; else echo "-";                            ?></td>
        </tr>
        <?php
            $i++;
            }
        ?>
        <tr><td colspan = "8"> End of report</td></tr>
    </table>
    </div>
</body>
</html>