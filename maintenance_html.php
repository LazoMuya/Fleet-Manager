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
    <title>Maintenance Page</title>
</head>
<body>
<div class="topnav">
        <a href="home_maintenance.php">Home</a>
        <a class="active" href="maintenance_html.php">Report Service</a>
        <a href="createvehicle_html.php">Add New Vehicle</a>
        <a href="deactivatevehicle.php">Deactivate Vehicle</a>
        <a href="reactivatevehicle.php">Reactivate Vehicle</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="maintenance" action="maintenance_html.php" method="POST" onsubmit="return validateForm()">
        <h2>Vehicle Maintenance Report</h2>
        <label>Vehicle Registration Number :</label><!-- select name='reg_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT reg_no,model,status,mileage FROM vehicle_table WHERE active = 1 AND status !='clear' ORDER BY status DESC");
            echo "<select name='reg_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose a Vehicle ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['reg_no'];
                $name = $id." - ".$row['model']." - ".$row['status']." - <b>Mileage is ".number_format($row['mileage'])." Kms</b>."; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label>Maintenance Report on Operations Done :</label><br>
        <textarea rows="4" name="ops_done" placeholder="Enter the Maintenace Operations Done on the Vehicle" class="select"></textarea><br>

        <label>Next Service Mileage (Kilometers) :</label>
        <input type="number" name="mileage" placeholder="Enter the Next Service Mileage"><br>

        <label for="status">Is the vehicle road worthy? :</label><br>
        <select name="status" id="status" class="select">
            <option value="0" selected disabled>---- Choose ----</option>
            <option value="clear">Yes</option>
            <option value="repair">No</option>
        </select><br>

        <div class="dateClick">
            <label>Date Completed:</label>
            <input type="date" id="date" name="date"><div class="icon" onclick="insertTodayDate()">Today</div><br>
        </div>
            <script>
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                today = yyyy + '-' + mm + '-' + dd;
                document.getElementById("date").setAttribute("max", today);

                function insertTodayDate() {
                    var dateInput = document.getElementById("date");
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

        <label>Duration of service operations e.g. 3 hours, same day, 2 days, etc. :</label>
        <input type="text" name="time" placeholder="Duration of service"><br>

        <label>Additional Comments :</label><br>
        <textarea rows="4" name="comment" placeholder="(Optional)" class="select"></textarea><br>

        <button type="submit" class="button">Update Maintenance Record</button>
    </form>
    <script>
        function validateForm() {
            var reg_no = document.forms.maintenance.reg_no.value;
            var ops_done = document.forms.maintenance.ops_done.value;
            var mileage = document.forms.maintenance.mileage.value;
            var status = document.forms.maintenance.status.value;
            var date = document.forms.maintenance.date.value;
            var time = document.forms.maintenance.time.value;

            if (reg_no == 0) {
                window.alert("Please enter the Vehicle's Registration Number.");
                return false;
            }
            if (ops_done == "" || ops_done == null) {
                window.alert("Please enter the operations done.");
                return false;
            }
            if (mileage == 0 || isNaN(mileage) || mileage == null) {
                window.alert("Please enter a valid mileage.");
                return false;
            }
            if (status == 0) {
                window.alert("Please enter the Status of the Vehicle.");
                return false;
            }
            if (time =="" || time == null){
                alert('Please enter the duration of the service.');
                return false;
            }

            if (date == "" || date == null) {
                alert("Please enter the date.");
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
        $getops_done = $_POST['ops_done'];
        $getmileage = $_POST['mileage'];
        $getstatus = $_POST['status'];
        $gettime = $_POST['time'];
        $getuser = $_SESSION['id'];
		$getdate = ($_POST['date']);

        if(empty($_POST['comment']))
            $getcomment = NULL;
        else
            $getcomment = $_POST['comment'];
        if($_POST['status']=="yes")
            $getstatus = "clear";
        else if($_POST['status']=="no")
            $getstatus = "repair";

        $query = "INSERT INTO maintenance_table(vehicle_regno,operations_done,next_service,date,duartion_taken,comment,emp_no) VALUES ('$getreg_no','$getops_done','$getmileage','$getdate','$gettime','$getcomment','$getuser')";
        $query2 = "UPDATE vehicle_table SET status = '$getstatus',next_service='$getmileage' WHERE reg_no = '$getreg_no'";
        
        $user_log = $_SESSION['id'];
        $table = "Maintenance Table";
        $category = "insert";
        $action = "Creating a new maintenance record for vehicle - ".$getreg_no;

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Maintenance record for vehicle <b>".$getreg_no."</b> successfully updated.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt->prepare($query2))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt -> execute())
            echo "<div class='message'><br>Vehicle record successfully updated.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt -> prepare($log))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>