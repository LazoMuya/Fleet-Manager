<?php
    session_start();
    if($_SESSION["role"] != "dispatch"){
        header("Location: redirect.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Mark Vehicles For Maintenance</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a class="active" href="markmaintenance_html.php">Mark For Maintanance</a>
        <a href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="markmaintenance" onsubmit="return validateForm()" action="markmaintenance_html.php" method="POST">
        <h2>Mark Vehcile for Maintenance</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT reg_no,model,(next_service-mileage) AS service FROM vehicle_table WHERE active = 1 AND next_service-mileage < 2000 ORDER BY service");
            echo "<select name='reg_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['reg_no'];
                $name = $row['model']." - ".$id." due for service in ".$row['service']." Kms"; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <button type="submit" class="button">Set Vehicle for Maintenance</button>
    </form>
    <script>
        function validateForm() {
            var reg_no = document.forms.markmaintenance.reg_no.value;
            if (reg_no == 0) {
                alert("Please the vehicle to choose for maintenance");
                return false;
            }
        }
    </script>
</body>
</html>

<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";

    if ($_POST != NULL){
        $getreg_no = $_POST['reg_no'];
        
        $query = "UPDATE vehicle_table SET status = 'service' WHERE reg_no = '$getreg_no'";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "update";
        $action = "Marking vehicle ".$getreg_no." for service";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Vehicle <b>".$getreg_no."</b> marked for maintenance.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt -> prepare($log))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt  -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>