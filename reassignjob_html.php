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
    <title>Assign Job</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a class="active" href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
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
    <form name="reassign_job" onsubmit="return validateForm()" action="reassignjob_html.php" method="POST">
        <h2>Update Delivery</h2>
        <label>Work Order Number :</label><!-- select name='work_no' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT delivery_table.delivery_number,delivery_table.description,delivery_table.driver,emp_table.name,delivery_table.status FROM delivery_table,emp_table WHERE emp_table.emp_number=delivery_table.driver and status != 'delivered' ORDER BY status,delivery_number");
            echo "<select name='work_no' id='work_no' class='select'>";
            echo '<option value="0" selected disabled>---- Choose the Work Number ----</option>';
            while ($row = $result->fetch_assoc()) {
                $id = $row['delivery_number'];
                $name = "Work No. ".$id." - ".$row['description']." - Assigned to - ".$row['name']." ".$row['driver']; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label>Driver Assigned :</label><!-- select name='driver_assigned' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT emp_table.emp_number,emp_table.name,vehicle_table.category FROM emp_table JOIN vehicle_table ON emp_table.emp_number = vehicle_table.driver_assigned");   
            echo "<select name='driver_assigned' class='select'>";
            echo '<option value="0" selected disabled>---- Assign a Driver ----</option>';
            while ($row = $result->fetch_assoc()) {
                if($row['category'] == 'lo')
                    $cat = "Light - Open";
                else if($row['category'] == 'lc')
                    $cat = "Light - Closed";
                else if($row['category'] == 'mo')
                    $cat = "Medium - Open";
                else if($row['category'] == 'mc')
                    $cat = "Medium - Closed";
                else if($row['category'] == 'ho')
                    $cat = "Heavy - Open";
                else if($row['category'] == 'hc')
                    $cat = "Heavy - Closed";
                else if($row['category'] == 't')
                    $cat = "Trailer";
                $id = $row['emp_number'];
                $name = $row['name']." - ".$id.". Vehicle category: ".$cat; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <button type="submit" class="button">Reassign Delivery</button>
    </form>
</body>
    <script>
        function validateForm() {
            var work_no = document.forms.reassign_job.work_no.value;
            var driver_assigned = document.forms.reassign_job.driver_assigned.value;
            if (work_no == 0) {
                alert("Please the Work Number of the delivery that is to be reassigned");
                return false;
            }
            if (driver_assigned == 0) {
                alert("Please select the driver to be assigned the delivery");
                return false;
            }
        }
    </script>
</html>

<?php
    $mysqli = require __DIR__ . "\config\dbconnect.php";

    if ($_POST != NULL){
        $getwork_no = $_POST['work_no'];
        $getemployee_no = $_POST['driver_assigned'];

        $query = ("SELECT delivery_table.delivery_number, delivery_table.driver, delivery_table.info, emp_table.name FROM delivery_table,emp_table WHERE emp_table.emp_number=delivery_table.driver and delivery_table.delivery_number = '$getwork_no'");
        $result = $mysqli->query($query);
        $delivery = $result->fetch_assoc();

        $info = $delivery["info"]." Reassigned from ".$delivery["name"]." ".$delivery["driver"].". ";

        $query2 = "UPDATE delivery_table SET driver = '$getemployee_no', info = CONCAT(info, '$info'), status = 'assigned' WHERE delivery_number = '$getwork_no'";
        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query2))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Job <b>".$getwork_no."</b> Assigned to new driver <b>".$getemployee_no."</b>.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        $user_log = $_SESSION['id'];
        $table = "Delivery Table";
        $category = "update";
        $action = "Reassigning delivery ".$getwork_no." to driver ".$getemployee_no;

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";
        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>