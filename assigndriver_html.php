<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Assign Driver</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a class="active" href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a href="markmaintenance_html.php">Mark For Maintanance</a>
        <a href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="assign" action="assigndriver_html.php" method="POST" onsubmit="return validateForm()">
        <h2>Assign a Driver to a Vehicle</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT reg_no,model,category,status FROM vehicle_table WHERE active = 1 AND driver_assigned IS NULL AND status != 'repair' ORDER BY status");
            echo "<select name='reg_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                switch($row['category']){
                    case "t":
                        $cat = "Trailer";
                        break;
                    case "lo":
                        $cat = "Light - Open";
                        break;
                    case "lc":
                        $cat = "Light - Closed";
                        break;
                    case "mo":
                        $cat = "Medium - Open";
                        break;
                    case "mc":
                        $cat = "Medium - Closed";
                        break;
                    case "ho":
                        $cat = "Heavy - Open";
                        break;
                    case "hc":
                        $cat = "Heavy - Closed";
                        break;
                }
                $id = $row['reg_no'];
                $name = $row['model']." - ".$cat." (".$row['status'].")"." - ".$id; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label>Driver :</label><!-- select name='driver' -->
        <?php 
            $con = require __DIR__ . "\config\dbconnect.php";
            $result = $con->query("SELECT emp_number,name FROM emp_table WHERE role = 'driver' AND NOT exists (select * from vehicle_table where emp_table.emp_number = vehicle_table.driver_assigned);");
            echo "<select name='driver' class='select'>";
            echo '<option value="0" selected disabled>---- Choose a Driver ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['emp_number'];
                $name = $row['name']." - ".$id; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <button type="submit" class="button">Assign Driver to Vehicle</button>
    </form>
    <script>
        function validateForm() {
            var reg_no = document.forms.assign.reg_no.value;
            var driver = document.forms.assign.driver.value;

            if (reg_no == 0) {
                window.alert("Please select the Vehicle's Registration Number.");
                return false;
            }
            if (driver == 0) {
                window.alert("Please select the Driver.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    if ($_POST != NULL){
        $getreg_no = $_POST['reg_no'];
        $getuser = $_POST['driver'];

        $query = "UPDATE vehicle_table SET driver_assigned = '$getuser' WHERE reg_no = '$getreg_no'";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "update";
        $action = "Assigned driver ".$getuser." to vehicle ".$getreg_no;

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'><p> Driver <b>".$getuser."</b> Assigned to vehicle <b>".$getreg_no."</b>.</p></div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>