<?php
    session_start();
    if($_SESSION["role"] != "engineering"){
        header("Location: redirect.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Reactive Vehicle</title>
</head>
<body>
    <div class="topnav">
        <a href="home_maintenance.php">Home</a>
        <a href="maintenance_html.php">Report Service</a>
        <a href="createvehicle_html.php">Add New Vehicle</a>
        <a href="deactivatevehicle.php">Deactivate Vehicle</a>
        <a class="active" href="reactivatevehicle.php">Reactivate Vehicle</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="deactivate" onsubmit="return validateForm()" action="reactivatevehicle.php" method="POST">
        <h2>Reactivate Vehicle</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT * FROM vehicle_table WHERE active = 0");
            echo "<select name='vehicle' class='select'>";
            echo '<option value="0" selected >---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                switch($row['category']){
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
                $id = $row['reg_no'];
                $name = $row['model']." - ".$id.". ".$category." currently ".date("Y")-$row['year_made']." years old."; 
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

        $query = "UPDATE vehicle_table SET active = 1 WHERE reg_no = '$getreg_no'";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "update";
        $action = "Reactivating vehicle ".$getreg_no." record.";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Vehicle record <b>".$getreg_no."</b> has been reactivated.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt -> prepare($log))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>