<?php
    session_start();
    if($_SESSION["role"] != "manager"){
        header("Location: redirect.php");
        exit();
    }
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    if($_POST == NULL || $_POST['role'] == 0)
        $sql = "SELECT name,emp_number,mobile,role FROM emp_table WHERE active = 1";
    else{
        $role = $_POST['role'];
        $sql = "SELECT name,emp_number,mobile,role FROM emp_table WHERE active = 1 AND role = '$role'";
    }
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Document</title>
</head>
<body>
    <div class="topnav">
        <a href="home_manager.php">Home</a>
        <a href="vehicles.php">Vehicle Information</a>
        <a class="active" href="employees.php">Employee Information</a>
        <a href="deliveries.php">Deliveries Information</a>
        <a href="latedeliveries.php">Late Deliveries</a>
        <a href="fuelbreakdown.php">Fuel Information</a>
        <a href="servicereports.php">Service Reports</a>
        <div class="topnav-right">
            <a><?php echo $_SESSION['user'];?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="category" action="employees.php" method="POST"><br>
        <label for="role">Select Employees to view :</label><br>
        <select name="role" id="role" class="select">
            <option value="0" default>All Employees</option>
            <option value="driver">Driver</option>
            <option value="engineering">Engineering</option>
            <option value="dispatch">Dispatcher</option>
            <option value="manager">Operations Manager</option>
        </select><br>

        <button type="submit" class="button">View employees</button>
    </form>
    <div class="container">
        <table>
            <tr >
                <th colspan="5"><h1>Employee Information</h1></th>
            </tr>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Employee No.</th>
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