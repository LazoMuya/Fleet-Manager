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

        <label>Scheduled Pickup Date:</label>
        <input type="date" id="pickup" name="pickup"><br>
            <script>
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                today = yyyy + '-' + mm + '-' + dd;
                document.getElementById("pickup").setAttribute("min", today);
            </script>

        <label>Scheduled Delivery Date:</label>
        <input type="date" id="delivery" name="delivery"><br>
            <script>
                document.getElementById("delivery").setAttribute("min", today);
            </script>


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
            var pickupDate = document.forms.createdelivery.pickup.value;
            var deliveryDate = document.forms.createdelivery.delivery.value;
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
            if (pickupDate == "" || pickupDate == null) {
                window.alert("Please enter the Pickup Date.");
                return false;
            }
            if (deliveryDate == "" || deliveryDate == null) {
                window.alert("Please enter the Delivery Date.");
                return false;
            }
            if (driver == 0){
                window.alert("Please select a Driver");
                return false;
            }
            if(pickupDate > deliveryDate){
                alert("Pickup date cannot be after delivery date");
                return false;
            }
            return true;
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
        $getpickup = $_POST['pickup'];
        $getdelivery = $_POST['delivery'];

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