<?php
    session_start();
    if($_SESSION["role"] != "driver"){
        header("Location: redirect.php");
        exit();
    }
    $id = $_SESSION['id'];
    $conn = require __DIR__ . "\config\dbconnect.php";
    $result = $conn->query("SELECT * FROM vehicle_table WHERE driver_assigned = '$id'");
    $rows = $result->fetch_assoc();
    if($rows["reg_no"] != NULL)
        $getreg_no = $rows["reg_no"];
    else
    $getreg_no = NULL;
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Refuel Page</title>
</head>
<body>
    <div class="topnav">
        <a href="home_driver.php">Home</a>
        <a href="pickup.php">Pickup Job</a>
        <a href="delivery.php">Deliver Job</a>
        <a class="active" href="refuel_html.php">Refuel</a>
        <a href="driver_log_html.php">Mileage Log</a>
        <a href="jobupdate.php">Incomplete Job</a>
        <a href="vehicle_history.php">Vehicle History</a>
        <a href="fuel_history.php">Fuel Records</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="refuel" action="refuel_html.php" method="POST" onsubmit="return validateForm()">
        <h2>Refuel Log</h2>

        <label for="reg_no">Vehicle Registration Number :</label>
        <input type="text" id="reg_no" name="reg_no" value="<?php echo($getreg_no);?>" readonly>

        <label>Fuel Amount:</label>
        <input type="text" name="refuel" placeholder="Enter The Amount Refueled in Litres"><br>

        <label>Price per Litre:</label>
        <input type="text" name="price" placeholder="Enter The Price per Litre"><br>

        <label>Receipt Number:</label>
        <input type="text" name="receipt" placeholder="Enter The Receipt Number"><br>

        <button type="submit" class="button">Save Record</button>
    </form>
    <script>
        function validateForm() {
            var refuel = document.forms.refuel.refuel.value;
            var price = document.forms.refuel.price.value;
            var reg_no = document.forms.refuel.reg_no.value;
            var receipt = document.forms.refuel.receipt.value;
            
            if (refuel < 1 || isNaN(refuel) || refuel == null) {
                window.alert("Please enter a valid fuel amount.");
                return false;
            }
            if (price < 1 || isNaN(price) || price == null) {
                window.alert("Please enter a valid Price per Litre.");
                return false;
            }
            if (reg_no  == "" || reg_no == null) {
                window.alert("Please select the Vehicle's Registration Number.");
                return false;
            }
            if (receipt == "" || receipt == null) {
                window.alert("Please enter the Receipt Number.");
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
        $getrefuel = $_POST['refuel'];
        $getprice = $_POST['price'];
        $getreg_no = $_POST['reg_no'];
        $getreceipt = $_POST['receipt'];
        $getuser = $_SESSION['id'];

        $query = "INSERT INTO refuel_table(emp_no,vehicle_reg,amount_refueled,price,receipt_no)  VALUES ('$getuser','$getreg_no','$getrefuel','$getprice','$getreceipt')";

        $user_log = $_SESSION['id'];
        $table = "Refuel Table";
        $category = "insert";
        $action = "Creating a new entry";

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo "<div class='message'>Fuel Record Successfully Captured.<br> <b>".$getrefuel."</b> Litres at <b>".$getprice."</b> Ksh/L.</div>";
        else
            die($mysqli->error." ".$mysqli->errno);

        if ( ! $stmt -> prepare($log))
            die ("SQL Error ".$mysqli->error);
        if ( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>