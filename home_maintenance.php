<?php
    session_start();
    $conn = require __DIR__ . "\config\dbconnect.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Maintenance Home</title>
</head>
<body>
    <div class="topnav">
        <a class="active" href="home_maintenance.php">Home</a>
        <a href="maintenance_html.php">Report Service</a>
        <a href="createvehicle_html.php">Add New Vehicle</a>
        <a href="deactivatevehicle.php">Deactivate Vehicle</a>
        <a href="reactivatevehicle.php">Reactivate Vehicle</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b><?php echo (ucfirst($_SESSION["role"])); ?></b></h4> 
            <p><?php echo ($_SESSION["user"]); ?></p> 
        </div>
        <div class="class2">
            <h4><b>Vehicles due for service</b></h4>
            <p>
                <?php 
                    $result = $conn->query("SELECT COUNT(reg_no) as count FROM vehicle_table WHERE active = 1 and status = 'service'");
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 0)
                        echo("There are no vehicles due for service.");
                    else if ($row['count'] == 1)
                        echo("There is currently <b>".$row['count']."</b> vehicle due for service. Click <a href='maintenance_html.php'>here</a> to report service.");
                    else
                        echo("There are currently <b>".$row['count']."</b> vehicles. Click <a href='maintenance_html.php'>here</a> to report service.");
                ?>
            </p>
        </div>
    </div>
    <div class="container">
    <table>
        <tr>
            <th colspan="6"><h2>Vehicles due for service</h2></th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Make and Model</th>
            <th>Vehicle Registration</th>
            <th>Mileage</th>
            <th>Category & Class</th>
            <th>Vehicle Status</th>

        </tr>
        <?php
            $result = $conn->query("SELECT * FROM vehicle_table WHERE active = 1 AND status != 'clear' ORDER BY status DESC");
            $i = 1;
            while($row = $result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $i;                                      ?></td>
            <td><?php echo $row['model'];                           ?></td>
            <td><?php echo $row['reg_no'];                          ?></td>
            <td><?php echo number_format($row['mileage'])." Kms";   ?></td>
            <td><?php switch($row['category']){
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
                    echo $category;                                 ?></td>
            <td><?php echo ucfirst($row['status'])                  ?></td>
        </tr>
        <?php
            $i++;
            }
        ?>
        <tr><td colspan = "6"> End of report</td></tr>
    </table>
    </div>
</body>
</html>