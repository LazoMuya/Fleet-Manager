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
    <title>Add New User Form</title>
</head>
<body>
    <div class="topnav">
        <a href="home_dispatch.php">Home</a>
        <a href="createdelivery_html.php">Create Delivery</a>
        <a href="reassignjob_html.php">Reassign Delivery</a>
        <a href="assigndriver_html.php">Assign Drivers</a>
        <a href="unassigndriver_html.php">Unassign Driver</a>
        <a href="markmaintenance_html.php">Mark For Maintanance</a>
        <a class="active" href="createemp_html.php">Add New Employee</a>
        <a href="employee_info.php">All Users</a>
        <a href="vehicle_info.php">All Vehicles</a>
        <a href="deliveries_info.php">Deliveries Information</a>
        <a href="vehicles_due.php">Vehicles due for service</a>
        <div class="topnav-right">
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="content">
        <form name="createemp" onsubmit="return validateForm()" action="createemp_html.php" method="POST" >
            <h2>Add a New Employee</h2>
            <label>Full Name :</label>
            <input type="text" name="name" placeholder="Enter Employee's Name"><br>

            <label>Mobile Number :</label>
            <input type="text" name="mobile" placeholder="Enter Mobile Number"><br>

            <label>Role :</label><br>
            <select name="role" id="role" class="select">
                <option value="0" selected disabled>---- Select User's Role ----</option>
                <option value="driver">Driver</option>
                <option value="engineering">Engineering</option>
                <option value="dispatch">Dispatch Manager</option>
                <option value="manager">Operations Manager</option>
                <option value="Admin" disabled>Admin</option>
            </select><br>

            <label>Password :</label>
            <input type="password" name="password" placeholder="Enter Password"><br>

            <label>Confirm Password :</label>
            <input type="password" name="password2" placeholder="Confirm Password"><br><br>

            <button type="submit" class="button">Create New Account</button>
        </form>
    </div>
    <script>
        function validateForm() {
            var name = document.forms.createemp.name.value;
            var phone = document.forms.createemp.mobile.value;
            var role = document.forms.createemp.role.value;
            var password = document.forms.createemp.password.value;
            var password2 = document.forms.createemp.password2.value;

            if (name == "" || name == null) {
                window.alert("Please enter the full name.");
                return false;
            }
            if (phone.length < 10 || isNaN(phone) || phone == null) {
                window.alert("Please enter a valid phone number.");
                return false;
            }
            if (role == 0 || role == null) {
                window.alert("Please select role.");
                return false;
            }
            if(password.length <8){
                window.alert("Password should be atleast 6 character long");
                return false;
            } 
            if (password == "" || password2 == "" || password == null || password2 == null) {
                window.alert("Please enter the password in both fields");
                return false;
            } else if (password != password2) {
                window.alert("Passwords do not match")
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
        $getpassword = $_POST['password'];
        //$getpasswordhash = password_hash($getpassword, PASSWORD_DEFAULT);
        $getname = $_POST['name'];
        $getmobile = $_POST['mobile'];
        $getrole = $_POST['role'];

        $query = "INSERT INTO emp_table(name,mobile,role,password,active) VALUES ('$getname','$getmobile','$getrole','$getpassword','1')";
        $stmt = $mysqli->stmt_init();
        if ( ! $stmt->prepare($query))
            die ("SQL Error ".$mysqli->error);
        if($stmt -> execute()){
            $user_sql = "SELECT * FROM emp_table WHERE name='$getname' AND mobile='$getmobile'";
            $result = $mysqli->query($user_sql);
            $user = $result->fetch_assoc();
            echo "<div class='message'><br><br>New user account for <b>".$user['name']."</b> was successfully created. <br>Employee number <b>".$user['emp_number']."</b> was assigned. Click <a href='employee_info.php'>here</a> to see all records.</div>";
        } else 
            die($mysqli->error." ".$mysqli->errno);

        $user_log = $_SESSION['id'];
        $table = "Employee Table";
        $category = "insert";
        $action = "Creating a new employee record - ".$user['emp_number'];

        $log = "INSERT INTO log_table(user, table_affected, category, action) VALUES ('$user_log','$table','$category','$action')";
        if ( ! $stmt->prepare($log))
            die ("SQL Error ".$mysqli->error);
        if( ! $stmt -> execute())
            die($mysqli->error." ".$mysqli->errno);
        $mysqli->close();
    }
?>