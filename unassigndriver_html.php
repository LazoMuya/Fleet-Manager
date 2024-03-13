<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Unassign Drivers to vehicles</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a class="active" href="unassigndriver_html.php">Unassign Driver</a>
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
    <form name="unassign" onsubmit="return validateForm()" action="unassigndriver_html.php" method="POST">
        <h2>Unassign a Driver From a Vehicle</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT vehicle_table.reg_no,vehicle_table.model,vehicle_table.driver_assigned,emp_table.name FROM vehicle_table,emp_table WHERE emp_table.emp_number=vehicle_table.driver_assigned AND driver_assigned IS NOT NULL;");
            echo "<select name='vehicle' class='select'>";
            echo '<option value="0" selected disabled>---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['reg_no'];
                $name = $row['model']." - ".$id." | Assigned to driver ".$row['name']." - ".$row['driver_assigned']; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <button type="submit" class="button">Unassign Driver from Vehicle</button>
    </form>
    <script>
        function validateForm() {
            var reg_no = document.forms.unassign.vehicle.value;
            if (reg_no == 0) {
                alert("You must select a vehicle");
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
        $getreg_no = $_POST['vehicle'];

        $query = "UPDATE vehicle_table SET driver_assigned = NULL WHERE reg_no = '$getreg_no'";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "update";
        $action = "Unassigning vehicle ".$getreg_no." from driver";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Vehicle <b>".$getreg_no."</b> unassigned from driver.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>