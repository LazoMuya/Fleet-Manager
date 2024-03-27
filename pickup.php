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

        <div class="dateClick">
            <label>Pickup Date:</label>
            <input type="date" id="pickup" name="pickup"> <div class="icon" onclick="insertTodayDate()">Today</div><br>
        </div>
            <script>
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                today = yyyy + '-' + mm + '-' + dd;
                document.getElementById("pickup").setAttribute("max", today);

                var minDate = new Date();
                var pastDate = new Date(minDate.setDate(minDate.getDate() - 14));
                var d = String(pastDate.getDate()).padStart(2, '0');
                var m = String(pastDate.getMonth() + 1).padStart(2, '0');
                var yy = pastDate.getFullYear();
                result = yy + '-' + m + '-' + d;
                document.getElementById("pickup").setAttribute("min", result);

                function insertTodayDate() {
                    var dateInput = document.getElementById("pickup");
                    var today = new Date();
                    var day = String(today.getDate()).padStart(2, '0');
                    var month = String(today.getMonth() + 1).padStart(2, '0');
                    var year = today.getFullYear();
                    var formattedDate = year + '-' + month + '-' + day;

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
            if (pickup == "" || pickup == null) {
                alert("Enter a pickup date");
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
        $getwork = $_POST['work_no'];
        $getstatus = "picked";
        $getpickup = $_POST['pickup'];

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