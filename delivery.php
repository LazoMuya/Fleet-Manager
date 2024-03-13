<?php
    session_start();
    $id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Delivery Page</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a class="active" href="delivery.php">Deliver Job</a>
        <a href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="delivery_job" onsubmit="return validateForm()" action="delivery.php" method="POST">
        <h2>Deliver Goods</h2>
        <label>Work Order Number :</label><!-- select name='work_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT delivery_number,description,client,date_of_pickup FROM delivery_table WHERE driver = '$id' AND status = 'picked'");
            echo '<select name="work_no" id="work_no" class="select" onChange="pickupDate()">';
            echo '<option value="0" selected disabled>---- Choose the Work Number ----</option>';
            while ($row = $result->fetch_assoc()) {
                $delivery_id = $row['delivery_number'];
                $date = $row['date_of_pickup'];
                $name = "Work No. ".$delivery_id." - ".$row['description']." -    Client - ".$row['client']; 
                echo '<option date="'.$date.'" value="'.$delivery_id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>
        <script>
            function pickupDate() {
                var selectElement = document.getElementById("work_no");
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                var day = selectedOption.getAttribute("date");
                var dateParts = day.split("-");
                var dayOfRecord = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];
                document.getElementById("pickup").value = dayOfRecord;
            }
        </script>

        <label for="delivery">Date of Pickup:</label>
        <input type="text" id="pickup" name="pickup" placeholder="Waiting for Work No" readonly>

        <label for="delivery">Date of DropOff (DD-MM-YYYY) (26-10-2022):</label><br>
        <div class="dateClick">
            <input type="text" id="delivery" name="delivery" placeholder="Enter the Date of Delivery"><br>
            <div class="icon" onclick="insertTodayDate()">Today</div>
        </div><br>
        <script>
            function insertTodayDate() {
            var dateInput = document.getElementById("delivery");
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

        <label>Additional Information :</label><br>
        <textarea rows="4" columns="40" name="comment" placeholder="(Optional)" class="select"></textarea><br>

        <button type="submit" class="button">Update Delivery Record</button>
    </form>
    <script>
        function validateForm() {
            var work_no = document.forms.delivery_job.work_no.value;
            var pickup = document.forms.delivery_job.pickup.value;
            var delivery = document.forms.delivery_job.delivery.value;

            if(work_no == 0){
                window.alert("Select the Work Number");
                return false;
            }

            var pickupDate = parseDate(pickup);
            var deliveryDate = parseDate(delivery);

            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (!deliveryDate) {
                window.alert("Invalid delivery date");
                return false;
            }
            if(pickupDate > deliveryDate){
                alert("Delivery date cannot be before pickup date");
                return false;
            }
            if(deliveryDate > today){
                alert("Delivery date cannot be after today");
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
        $getstatus = "delivered";
        if($_POST['comment'] != NULL)
            $getcomment = $_POST['comment'].".";
        else
            $getcomment = NULL;
        //Delivery date
        $a = strtotime($_POST['delivery']);
        $getdelivery = date('Y-m-d' , $a);

        $query = "UPDATE delivery_table SET date_of_delivery = '$getdelivery', status = '$getstatus', info = CONCAT(info, '$getcomment') WHERE delivery_number = '$getwork'";

        
        $user_log = $_SESSION['id'];
        $table = "Delivery Table";
        $category = "update";
        $action = "Updating delivery record ".$getwork."to delivered";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Delivery Record <b>".$getwork."</b> successfully updated to delivered.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>