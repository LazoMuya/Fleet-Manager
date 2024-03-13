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
    <title>Pickup Page</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a class="active" href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="pickup_job" onsubmit="return validateForm()" action="pickup.php" method="POST">
        <h2>Pickup Delivery</h2>

        <label>Work Order Number :</label><!-- select name='work_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT delivery_number,description,client FROM delivery_table WHERE driver = '$id' AND status = 'assigned'");
            echo "<select name='work_no' id='work_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose the Work Number ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['delivery_number'];
                $name = "Work No. ".$id." - ".$row['description']." -    Client - ".$row['client']; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label for="pickup">Date of Pickup (DD-MM-YYYY) (06-09-2022):</label><br>
        <div class="dateClick">
            <input type="text" id="pickup" name="pickup" placeholder="Enter the Date of Pickup"><br>
            <div class="icon" onclick="insertTodayDate()">Today</div>
        </div><br>
        <script>
            function insertTodayDate() {
            var dateInput = document.getElementById("pickup");
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var year = today.getFullYear();
            var formattedDate = day + '-' + month + '-' + year;

            if (dateInput.value === formattedDate)
                dateInput.value = "";
            else
                dateInput.value = formattedDate;
            }
        </script>

        <button type="submit" class="button">Update Job Record</button>
    </form>
    <script>
        function validateForm() {
            var work_no = document.forms.pickup_job.work_no.value;
            var pickup = document.forms.pickup_job.pickup.value;

            if(work_no == 0){
                window.alert("Select the Work Number");
                return false;
            }
            var pickupDate = parseDate(pickup);

            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (!pickupDate) {
                alert("Invalid pickup date");
                return false;
            }
            if(pickupDate > today){
                alert("Pickup date cannot be after today");
                return false;
            }
            return true;
        }
        function parseDate(dateStr) {
            var parts = dateStr.split("-");
            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10) - 1;
            var year = parseInt(parts[2], 10);

            var date = new Date(year, month, day);

            if (isNaN(date.getTime()))
                return false;

            return date;
        }
    </script>
</body>
</html>

<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";

    if($_POST != NULL){
        $getwork = $_POST['work_no'];
        $getstatus = "picked";
        //Pickupdate
        $a = strtotime($_POST['pickup']);
        $getpickup = date('Y-m-d' , $a);

        $query = "UPDATE delivery_table SET date_of_pickup = '$getpickup', status = '$getstatus' WHERE delivery_number = '$getwork'";

        $user_log = $id;
        $table = "Delivery Table";
        $category = "update";
        $action = "Updating delivery record ".$getwork." to picked Up";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Delivery record <b>".$getwork."</b> successfully updated as picked.</div>";  
        else
            die($mysqli->error." ".$mysqli->errno);
    }
?>