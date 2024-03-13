<?php
    session_start();
    $id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Mileage Log</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a class="active" href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="info">
        <div class="class1">
            <h4><b>Current Vehicle</b></h4> 
            <p>
                <?php 
                    $conn = require __DIR__ . "\config\dbconnect.php";
                    $result = $conn->query("SELECT * FROM vehicle_table WHERE driver_assigned = '$id'");
                    $rows = $result->fetch_assoc();
                    $lastmileage = $rows["mileage"];
                    $getreg_no = $rows["reg_no"];
                    if ($rows != NULL)
                        echo($rows["model"]." - ".$rows["reg_no"].". The last recorded mileage is ".number_format($lastmileage)." Kms.");
                    else
                        echo("You have not been assigned a vehicle.");
                ?>
            </p> 
        </div>
    </div>
    <form name="driver_log" action="driver_log_html.php" method="POST" onsubmit="return validateForm()">
        <h2>Update Vehicle Mileage</h2>
        <label for="last_mileage">Last Recorded Mileage:</label>
        <input type="text" id="last_mileage" name="last_mileage" value="<?php echo($lastmileage);?>" readonly>
        
        <label>The Current Mileage (Kilometers) :</label>
        <input type="text" name="mileage" placeholder="Enter Mileage Covered"><br>

        <button type="submit" class="button">Save Record</button>
    </form>
    <script>
        function validateForm() {
            var last_mileage = document.forms.driver_log.last_mileage.value;
            var mileage = document.forms.driver_log.mileage.value;
            if (mileage == 0 || isNaN(mileage) || mileage == null) {
                window.alert("Please enter a valid mileage.");
                return false;
            } else if (mileage<=last_mileage){
                window.alert("The vehicle mileage is can't be less than what is on record");
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
        $getmileage = $_POST['mileage'];
        $getuser = $_SESSION['id'];
        $distance = $_POST['mileage'] - $lastmileage;

        $query = "INSERT INTO driver_log(employee_number,distance_covered,vehicle_registration) VALUES ('$getuser','$distance','$getreg_no')";
        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Driver log successfully recorded for vehicle <b>".$getreg_no."</b>.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        $query2 = "UPDATE vehicle_table SET mileage = '$getmileage' WHERE reg_no = '$getreg_no'";

        if ( ! $stmt->prepare($query2))
            die ("SQL Error ".$mysqli->error);
        if ($stmt -> execute())
            echo "<div class='message'><p> Vehicle mileage successfully updated.</p> </div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        $user_log = $_SESSION['id'];
        $table = "Driver Log Table";
        $category = "insert";
        $action = "Created a new record for vehicle ".$getreg_no.".";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>