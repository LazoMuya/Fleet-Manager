<?php
    session_start();
    if($_SESSION["role"] != "driver"){
        header("Location: redirect.php");
        exit();
    }
    $id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Report Job Error</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a class="active" href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="deliveryupdate" onsubmit="validateform()" action="jobupdate.php" method="POST">
        <h2>Unable To Complete Delivery</h2>
        <label>Work Order Number :</label><!-- select name='work_no' -->
        <?php
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT delivery_number,description,client FROM delivery_table WHERE driver = '$id' AND status = 'picked'");
            echo "<select name='work_no' id='work_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose the Work Number ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['delivery_number'];
                $name = "Work No. ".$id." - ".$row['description']." -    Client - ".$row['client']; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label>Additional Information :</label><br>
        <textarea rows="4" columns="40" name="comment" placeholder="(Optional)" class="select"></textarea><br>

        <button type="submit" class="button">Update Job Record</button>
    </form>
    <script>
        function validateForm() {
            var work_no = document.forms.deliveryupdate.work_no.value;

            if(work_no == 0){
                window.alert("Select the Work Number");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>

<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";

    if($_POST != NULL){
        $getwork_no = $_POST['work_no'];
        $getcomments = $_POST['comment']." - ";
        $getstatus = "incomplete";
    
        $query = "UPDATE delivery_table SET info = CONCAT(info, '$getcomments'), status = '$getstatus' WHERE delivery_number = '$getwork_no'";

        $user_log = $_SESSION['id'];
        $table = "Delivery Table";
        $category = "update";
        $action = "Updating delivery record ".$getwork_no." to incomplete";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Delivery record <b>".$getwork_no."</b> successfully updated";
        else
            die($mysqli->error." ".$mysqli->errno);
        
        if ( ! $stmt ->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>