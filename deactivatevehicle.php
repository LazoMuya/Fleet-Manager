<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Deactive Vehicle</title>
</head>
<body>
    <div class="topnav">
        <a href="home_maintenance.php">Home</a>
        <a href="maintenance_html.php">Report Service</a>
        <a href="createvehicle_html.php">Add New Vehicle</a>
        <a class="active" href="deactivatevehicle.php">Deactivate Vehicle</a>
        <a href="reactivatevehicle.php">Reactivate Vehicle</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="deactivate" onsubmit="return validateForm()" action="deactivatevehicle.php" method="POST">
        <h2>Deactivate Vehicle Record</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT * FROM vehicle_table WHERE active = 1");
            echo "<select name='vehicle' class='select'>";
            echo '<option value="0" selected >---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                if ($row['driver_assigned'] != NULL)
                    $driver = $row['driver_assigned'];
                else
                    $driver = "N/A";
                $id = $row['reg_no'];
                $name = $row['model']." - ".$id." | Assigned to driver  - ".$driver; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <button type="submit" class="button">Deactivate Record</button>
    </form>
    <script>
        function validateForm() {
            var reg_no = document.forms.deactivate.vehicle.value;
            if (reg_no == 0) {
                window.alert("You must select a vehicle");
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

        $query = "UPDATE vehicle_table SET active = 0, driver_assigned = NULL WHERE reg_no = '$getreg_no'";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "update";
        $action = "Deactivating vehicle ".$getreg_no." record.";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Vehicle record <b>".$getreg_no."</b> has been deactivated.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>