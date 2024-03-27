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
    <title>Add New Vehicle</title>
</head>
<body>
    <div class="topnav">
        <a href="home_maintenance.php">Home</a>
        <a href="maintenance_html.php">Report Service</a>
        <a class="active" href="createvehicle_html.php">Add New Vehicle</a>
        <a href="deactivatevehicle.php">Deactivate Vehicle</a>
        <a href="reactivatevehicle.php">Reactivate Vehicle</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <form name="addvehicle" onsubmit="return validateForm()" action="createvehicle_html.php" method="POST">
        <h2>Add Vehicle</h2>
        <label>Make and Model :</label>
        <input type="text" name="make" placeholder="Make & Model"><br>

        <label>Vehicle Registration Number :</label>
        <input type="text" name="reg_no" placeholder="Vehicle Registration"><br>

        <label for="vehicle_category">Vehicle Category :</label><br><div class="wrapper">
        <select name="vehicle_category" id="vehicle_category" class="select">
            <option value="0" selected disabled>---- Select The Vehicle's Category ----</option>
            <option value="lo">Light - Open</option>
            <option value="lc">Light - Closed</option>
            <option value="mo">Medium - Open</option>
            <option value="mc">Medium - Closed</option>
            <option value="ho">Heavy - Open</option>
            <option value="hc">Heavy - Closed</option>
            <option value="t">Trailer</option>
        </select><br>

        <label>Year of Manufacture :</label>
        <select name="year" id="year" class="select">
            <option value="0" selected disabled>---- Select Year of Manufacture ----</option>       
                <script>
                    let dateDropdown = document.getElementById('year');
                    let currentYear = new Date().getFullYear();    
                    let earliestYear = 1990;     
                    while (currentYear >= earliestYear) {      
                        let dateOption = document.createElement('option');          
                        dateOption.text = currentYear;      
                        dateOption.value = currentYear;        
                        dateDropdown.add(dateOption);      
                        currentYear -= 1;    
                    }
                </script>
        </select><br> 
        
        <!-- <label for="date_bought">Date of Purchase (DD-MM-YYYY) (26-10-2022):</label>
        <input type="text" id="date_bought" name="date_bought" placeholder="Enter the Date of Purchase"><br> -->

        <label>Date of Purchase:</label>
        <input type="date" id="date_bought" name="date_bought"><br>
            <script>
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                today = yyyy + '-' + mm + '-' + dd;
                document.getElementById("date_bought").setAttribute("max", today);
            </script>
        
        <label>Mileage (Kilometers) :</label>
        <input type="text" name="mileage" placeholder="Vehicle Mileage"><br>

        <button type="submit" class="button">Add To Database</button> <br>
    </form>
    <script>
        function validateForm() {
            var make = document.forms.addvehicle.make.value;
            var reg_no = document.forms.addvehicle.reg_no.value;
            var year = document.forms.addvehicle.year.value;
            var date_bought = document.forms.addvehicle.date_bought.value;
            var mileage = document.forms.addvehicle.mileage.value;
            var category = document.forms.addvehicle.vehicle_category.value;

            if (make == "" || make == null) {
                window.alert("Please enter the Make and Model of the Vehicle.");
                return false;
            }
            if (reg_no == "" || reg_no == null) {
                window.alert("Please enter the Vehicle Registration Number.");
                return false;
            }
            if (category == "0"){
                window.alert("Please select the vehicle's category");
                return false;
            }
            if (year == 0) {
                window.alert("Please enter the Year of Manufacture.");
                return false;
            }
            if (mileage == "" || mileage == null || isNaN(mileage)) {
                window.alert("Please enter a valid mileage.");
                return false;
            }
            if (date_bought == "" || date_bought == null) {
                window.alert("Invalid date bought");
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
        $getmake = $_POST['make'];
        $getreg_no = strtoupper($_POST['reg_no']);
        $getcategory = $_POST['vehicle_category'];
        $getyear = $_POST['year'];
        $getmileage = $_POST['mileage'];
        $getstatus = 'service';
		$getpurchase_date = ($_POST['date_bought']);
        
        $query = "INSERT INTO vehicle_table(model,reg_no,category,year_made,date_purchased,mileage,status,active) VALUES ('$getmake','$getreg_no','$getcategory','$getyear','$getpurchase_date','$getmileage','$getstatus','1')";

        $user_log = $_SESSION['id'];
        $table = "Vehicle Table";
        $category = "insert";
        $action = "Creating a new vehicle record - ".$getreg_no;

        $log = "INSERT INTO log_table(user,table_affected,category,action) VALUES ('$user_log','$table','$category','$action')";

        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute())
            echo("<div class='message'>New vehicle ".$getreg_no." successfully recorded.</div>");
        else {
            if($mysqli->errno === 1062){
                die("<div class='message'>The vehicle already exists in this database.</div>");
            }
            die($mysqli->error." ".$mysqli->errno);
        }

        if ( ! $stmt -> prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
    }
?>