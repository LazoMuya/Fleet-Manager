<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Create a New Delivery</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a class="active" href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
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
    <form name="createdelivery" onsubmit="return validation()" action="createdelivery_html.php" method="POST">
        <h2>Create a New Delivery</h2>
        <label>Delivery Description :</label>
        <input type="text" name="description" id="description" placeholder="Short Description of the Delivery"><br>

        <label>Client :</label>
        <input type="text" name="client" placeholder="Enter Client's Details"><br>

        <label>Pickup Location :</label>
        <input type="text" name="scpoint" placeholder="Enter Pickup Location"><br>

        <label>Delivery Point :</label>
        <input type="text" name="destination" placeholder="Enter The Delivery Location"><br>

        <label>Scheduled Pickup Date (DD-MM-YYYY) (16-09-2022):</label>
        <input type="text" id="pickup" name="pickup" placeholder="Enter the Scheduled Pickup Date"><br>

        <label>Scheduled Delivery Date (DD-MM-YYYY) (14-02-2022):</label>
        <input type="text" id="delivery" name="delivery" placeholder="Enter the Scheduled Delivery Date"><br>

        <label>Driver Assigned :</label><!-- select name='driver_assigned' -->
        <?php 
            $conn = require __DIR__ . "\config\dbconnect.php";
            $result = $conn->query("SELECT emp_table.emp_number,emp_table.name,vehicle_table.category,count(delivery_number) AS deliveries FROM emp_table JOIN vehicle_table ON emp_table.emp_number = vehicle_table.driver_assigned LEFT JOIN delivery_table ON delivery_table.driver = emp_table.emp_number AND delivery_table.status != 'delivered' GROUP BY emp_table.emp_number,emp_table.name,vehicle_table.category");
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
                $deliveries = $row['deliveries'];
                $name = $row['name']." - Category: ".$cat.". Active deliveries - ".$deliveries; 
                echo '<option value="'.$id.'">'.$name.'</option>';
            }
            echo "</select>";
        ?>

        <label>Additional Info :</label><br>
        <textarea rows="4" name="info" placeholder="(Optional)" class="select"></textarea><br>

        <button type="submit" class="button">Create New Delivery</button>
    </form>
    <script>
        function validation() {
            var desc = document.forms.createdelivery.description.value;
            var client = document.forms.createdelivery.client.value;
            var scpoint = document.forms.createdelivery.scpoint.value;
            var destination = document.forms.createdelivery.destination.value;
            var pickup = document.forms.createdelivery.pickup.value;
            var delivery = document.forms.createdelivery.delivery.value;
            var driver = document.forms.createdelivery.driver_assigned.value;

            if (desc == "" || desc == null) {
                window.alert("Please enter the Item Descriptions.");
                return false;
            }
            if (client == "" || client == null) {
                window.alert("Please enter the Client's Details.");
                return false;
            }
            if (scpoint == "" || scpoint == null) {
                window.alert("Please enter the Pickup Location.");
                return false;
            }
            if (destination == "" || destination == null) {
                window.alert("Please enter the Delivery Destination.");
                return false;
            }
            if (driver == 0){
                window.alert("Please select a Driver");
                return false;
            }
            var pickupDate = parseDate(pickup);
            var deliveryDate = parseDate(delivery);

            var today = new Date();
            today.setHours(0, 0, 0, 0);

            if (!pickupDate) {
                alert("Invalid pickup date");
                return false;
            }
            if(today > pickupDate){
                alert("Pickup date cannot be before today");
                return false;
            }
            if(!deliveryDate){
                alert("Invalid delivery date");
                return false;
            }
            if(pickupDate > deliveryDate){
                alert("Pickup date cannot be after delivery date");
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
                return null;

            return date;
        }
    </script>
</body>
</html>

<?php
    $mysqli = require __DIR__ .  "\config\dbconnect.php";

    if ($_POST != NULL){
        $getuser = $_SESSION["id"];
        $getdescription = $_POST['description'];
        $getclient = $_POST['client'];
        $getscpoint = $_POST['scpoint'];
        $getdestination = $_POST['destination'];
        $getdriver_assigned = $_POST['driver_assigned'];
        //Pickup
        $a = strtotime($_POST['pickup']);
		$getpickup = date('Y-m-d' , $a);
        //Delivery
        $b = strtotime($_POST['delivery']);
		$getdelivery = date('Y-m-d' , $b);

        if(empty($_POST['info']))
            $getinfo = NULL;
        else
            $getinfo = $_POST['info']." - ";
            
        $query = "INSERT INTO delivery_table(description,client,pickup_location,delivery_point,scheduled_pickup,scheduled_delivery,created_by,driver,info,status) VALUES ('$getdescription','$getclient','$getscpoint','$getdestination','$getpickup','$getdelivery','$getuser','$getdriver_assigned','$getinfo','assigned')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'> New delivery record successfully created. </div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        $user_log = $_SESSION['id'];
        $table = "Delivery Table";
        $category = "insert";
        $action = "Creating a new delivery record";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";
        if ( ! $stmt ->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>